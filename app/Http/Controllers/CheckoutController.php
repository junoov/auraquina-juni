<?php

namespace App\Http\Controllers;

use App\Models\ItemKeranjang;
use App\Models\ItemPesanan;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\UserAddress;
use App\Models\VarianProduk;
use App\Models\Voucher;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class CheckoutController extends Controller
{
    private function sessionId(): string
    {
        return session()->getId();
    }

    private function mapCheckoutItem(Produk $produk, ?VarianProduk $varian, int $qty, ?int $cartItemId = null): array
    {
        $price = $produk->harga + ($varian?->penyesuaian_harga ?? 0);
        $variant = collect([$varian?->warna, $varian?->ukuran])->filter()->implode(' / ');

        return [
            'cart_item_id' => $cartItemId,
            'produk_id' => $produk->id,
            'varian_id' => $varian?->id,
            'slug' => $produk->slug,
            'name' => $produk->nama,
            'variant' => $variant ?: 'Default',
            'qty' => $qty,
            'price' => $price,
            'img' => $varian?->gambarVarianUtama?->full_url ?? $produk->gambarUtama?->full_url,
        ];
    }

    private function cartItems(?array $selectedIds = null): Collection
    {
        $query = ItemKeranjang::with(['produk.gambarUtama', 'varian.gambarVarianUtama'])
            ->where($this->identifier());

        if ($selectedIds) {
            $query->whereIn('id', $selectedIds);
        }

        return $query->get()->map(function (ItemKeranjang $item) {
            return $this->mapCheckoutItem($item->produk, $item->varian, $item->jumlah, $item->id);
        });
    }

    private function checkoutItemsFromSession(): Collection
    {
        $payload = session('checkout_payload');

        if (($payload['mode'] ?? null) === 'buy_now' && ! empty($payload['items'])) {
            return collect($payload['items']);
        }

        if (($payload['mode'] ?? null) === 'cart' && ! empty($payload['selected_ids'])) {
            return $this->cartItems($payload['selected_ids']);
        }

        return $this->cartItems();
    }

    private function checkoutTotals(Collection $checkoutItems): array
    {
        $subtotal = $checkoutItems->sum(fn (array $item) => $item['price'] * $item['qty']);
        $shipping = 11500;
        $voucher = null;
        $discount = 0;

        $voucherCode = session('checkout_voucher_code');

        if ($voucherCode) {
            $voucher = Voucher::where('code', strtoupper((string) $voucherCode))->first();

            if ($voucher?->isAvailableFor($subtotal)) {
                $discount = $voucher->discountFor($subtotal, $shipping);
            } else {
                session()->forget('checkout_voucher_code');
                $voucher = null;
            }
        }

        return [
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'discount' => $discount,
            'total' => max(0, $subtotal + $shipping - $discount),
            'voucher' => $voucher,
        ];
    }

    private function signedOrderUrl(Pesanan $pesanan): string
    {
        return URL::temporarySignedRoute('pesanan.show', now()->addDays(30), [
            'kode' => $pesanan->kode_pesanan,
        ]);
    }

    private function reserveCheckoutStock(Collection $checkoutItems): void
    {
        foreach ($checkoutItems as $item) {
            $variantId = $item['varian_id'] ?? null;

            if (! $variantId) {
                throw new DomainException('Varian produk wajib dipilih sebelum checkout.');
            }

            $variant = VarianProduk::whereKey($variantId)->lockForUpdate()->first();

            if (! $variant || $variant->produk_id !== $item['produk_id']) {
                throw new DomainException('Varian produk tidak valid.');
            }

            if ($variant->stok < $item['qty']) {
                throw new DomainException("Stok {$item['name']} ({$item['variant']}) tidak mencukupi.");
            }

            $variant->forceFill(['stok' => $variant->stok - $item['qty']])->save();
        }
    }

    private function canAccessOrder(Pesanan $pesanan, Request $request): bool
    {
        if ($request->hasValidSignature()) {
            return true;
        }

        if (auth()->id() && $pesanan->user_id === auth()->id()) {
            return true;
        }

        return hash_equals((string) $pesanan->session_id, $this->sessionId());
    }

    private function authorizedOrder(Request $request, string $kode): Pesanan
    {
        $pesanan = Pesanan::with('items')->where('kode_pesanan', $kode)->firstOrFail();

        if (! $this->canAccessOrder($pesanan, $request)) {
            abort(403);
        }

        $pesanan->expireIfOverdue();

        return $pesanan->refresh()->load('items');
    }

    public function show()
    {
        $checkoutItems = $this->checkoutItemsFromSession();

        if ($checkoutItems->isEmpty()) {
            return redirect()->route('shop.index');
        }

        $totals = $this->checkoutTotals($checkoutItems);
        $subtotal = $totals['subtotal'];
        $shipping = $totals['shipping'];
        $discount = $totals['discount'];
        $total = $totals['total'];
        $voucher = $totals['voucher'];

        $addresses = auth()->check()
            ? UserAddress::where('user_id', auth()->id())->orderByDesc('is_default')->latest()->get()
            : collect();

        return view('checkout', compact('checkoutItems', 'subtotal', 'shipping', 'discount', 'total', 'voucher', 'addresses'));
    }

    public function applyVoucher(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50',
        ]);

        $checkoutItems = $this->checkoutItemsFromSession();

        if ($checkoutItems->isEmpty()) {
            return response()->json(['error' => 'Tidak ada item checkout untuk voucher ini.'], 422);
        }

        $subtotal = $checkoutItems->sum(fn (array $item) => $item['price'] * $item['qty']);
        $voucher = Voucher::where('code', strtoupper(trim($validated['code'])))->first();

        if (! $voucher || ! $voucher->isAvailableFor($subtotal)) {
            return response()->json(['error' => 'Voucher tidak valid atau belum memenuhi minimal belanja.'], 422);
        }

        session(['checkout_voucher_code' => $voucher->code]);
        $totals = $this->checkoutTotals($checkoutItems);

        return response()->json([
            'success' => true,
            'message' => "Voucher {$voucher->code} berhasil dipakai.",
            'voucher' => $voucher->code,
            'discount' => $totals['discount'],
            'total' => $totals['total'],
        ]);
    }

    public function buyNow(Request $request)
    {
        $validated = $request->validate([
            'produk_id' => 'required|exists:produks,id',
            'varian_id' => 'nullable|exists:varian_produks,id',
            'jumlah' => 'required|integer|min:1|max:99',
        ]);

        $produk = Produk::with('gambarUtama')->where('aktif', true)->findOrFail($validated['produk_id']);
        $varian = isset($validated['varian_id']) ? VarianProduk::find($validated['varian_id']) : null;

        if ($varian && $varian->produk_id !== $produk->id) {
            abort(422, 'Varian tidak valid untuk produk ini.');
        }

        if ($varian && $varian->stok < $validated['jumlah']) {
            return response()->json(['error' => 'Stok tidak mencukupi'], 422);
        }

        session([
            'checkout_payload' => [
                'mode' => 'buy_now',
                'items' => [
                    $this->mapCheckoutItem($produk, $varian, $validated['jumlah']),
                ],
            ],
        ]);

        return response()->json([
            'success' => true,
            'redirect' => route('checkout'),
        ]);
    }

    public function fromCart(Request $request)
    {
        $validated = $request->validate([
            'item_ids' => 'required|array|min:1',
            'item_ids.*' => 'integer',
        ]);

        $selectedIds = ItemKeranjang::where($this->identifier())
            ->whereIn('id', $validated['item_ids'])
            ->pluck('id')
            ->all();

        if ($selectedIds === []) {
            return response()->json(['error' => 'Tidak ada item checkout yang dipilih'], 422);
        }

        session([
            'checkout_payload' => [
                'mode' => 'cart',
                'selected_ids' => $selectedIds,
            ],
        ]);

        return response()->json([
            'success' => true,
            'redirect' => route('checkout'),
        ]);
    }

    /**
     * Place order — persist to DB, clear cart items, redirect to order page.
     */
    public function placeOrder(Request $request)
    {
        $validated = $request->validate([
            'nama_penerima' => 'required|string|max:255',
            'telepon' => 'required|string|max:30',
            'email' => 'nullable|email|max:255',
            'kota' => 'nullable|string|max:255',
            'alamat_lengkap' => 'nullable|string|max:1000',
            'metode_pengiriman' => 'required|string|max:100',
            'metode_pembayaran' => 'required|string|max:100',
        ]);

        $payload = session('checkout_payload');
        $checkoutItems = $this->checkoutItemsFromSession();

        if ($checkoutItems->isEmpty()) {
            return response()->json(['error' => 'Tidak ada item untuk dipesan'], 422);
        }

        $totals = $this->checkoutTotals($checkoutItems);
        $subtotal = $totals['subtotal'];
        $shipping = $totals['shipping'];
        $discount = $totals['discount'];
        $total = $totals['total'];
        $voucher = $totals['voucher'];

        try {
            $pesanan = DB::transaction(function () use ($validated, $checkoutItems, $subtotal, $shipping, $discount, $total, $voucher, $payload) {
                $this->reserveCheckoutStock($checkoutItems);

                if ($user = auth()->user()) {
                    $user->fill([
                        'recipient_name' => $validated['nama_penerima'],
                        'phone' => $validated['telepon'],
                        'city' => $validated['kota'] ?? null,
                        'address' => $validated['alamat_lengkap'] ?? null,
                    ])->save();
                }

                $pesanan = Pesanan::create([
                    'kode_pesanan' => Pesanan::generateKode(),
                    'session_id' => $this->sessionId(),
                    'user_id' => auth()->id(),
                    'status' => Pesanan::STATUS_PENDING_PAYMENT,
                    'nama_penerima' => $validated['nama_penerima'],
                    'telepon' => $validated['telepon'],
                    'email' => $validated['email'] ?? auth()->user()?->email,
                    'kota' => $validated['kota'] ?? null,
                    'alamat_lengkap' => $validated['alamat_lengkap'] ?? null,
                    'metode_pengiriman' => $validated['metode_pengiriman'],
                    'metode_pembayaran' => $validated['metode_pembayaran'],
                    'subtotal' => $subtotal,
                    'ongkir' => $shipping,
                    'diskon' => $discount,
                    'voucher_id' => $voucher?->id,
                    'voucher_code' => $voucher?->code,
                    'total' => $total,
                    'batas_bayar' => now()->addHour(),
                    'stock_reserved_at' => now(),
                ]);

                foreach ($checkoutItems as $item) {
                    ItemPesanan::create([
                        'pesanan_id' => $pesanan->id,
                        'produk_id' => $item['produk_id'],
                        'varian_id' => $item['varian_id'] ?? null,
                        'nama_produk' => $item['name'],
                        'varian_label' => $item['variant'],
                        'harga' => $item['price'],
                        'jumlah' => $item['qty'],
                        'gambar_url' => $item['img'],
                    ]);
                }

                // Remove purchased items from cart
                if (($payload['mode'] ?? null) === 'cart' && ! empty($payload['selected_ids'])) {
                    ItemKeranjang::where($this->identifier())
                        ->whereIn('id', $payload['selected_ids'])
                        ->delete();
                }

                if ($voucher) {
                    $voucher->increment('used_count');
                }

                return $pesanan;
            });
        } catch (DomainException $exception) {
            return response()->json(['error' => $exception->getMessage()], 422);
        }

        // Clear checkout session
        session()->forget(['checkout_payload', 'checkout_voucher_code']);

        $pesanan->sendOrderPlacedNotification();

        // Generate Midtrans Core API payment for online methods
        if ($this->isMidtransPayment($pesanan->metode_pembayaran)) {
            $this->processMidtransPayment($pesanan);
        }

        return response()->json([
            'success' => true,
            'redirect' => $this->signedOrderUrl($pesanan),
        ]);
    }

    /**
     * Show order detail / payment page.
     */
    public function showOrder(Request $request, string $kode)
    {
        $pesanan = $this->authorizedOrder($request, $kode);

        return view('pesanan-detail', compact('pesanan'));
    }

    public function trackOrder()
    {
        return view('track-order', ['pesanan' => null]);
    }

    public function lookupOrder(Request $request)
    {
        $validated = $request->validate([
            'kode_pesanan' => ['required', 'string', 'max:40'],
            'contact' => ['required', 'string', 'max:255'],
        ], [
            'kode_pesanan.required' => 'Nomor pesanan wajib diisi.',
            'contact.required' => 'Email atau nomor HP wajib diisi.',
        ]);

        $pesanan = Pesanan::with('items')
            ->where('kode_pesanan', strtoupper(trim($validated['kode_pesanan'])))
            ->first();

        if (! $pesanan || ! $this->orderContactMatches($pesanan, $validated['contact'])) {
            return back()
                ->withErrors(['kode_pesanan' => 'Pesanan tidak ditemukan. Pastikan nomor pesanan dan email atau nomor HP sudah benar.'])
                ->withInput();
        }

        $pesanan->expireIfOverdue();

        return view('track-order', ['pesanan' => $pesanan->refresh()->load('items')]);
    }

    private function orderContactMatches(Pesanan $pesanan, string $contact): bool
    {
        $contact = trim($contact);
        $phone = preg_replace('/\D+/', '', $contact) ?? '';
        $orderPhone = preg_replace('/\D+/', '', (string) $pesanan->telepon) ?? '';

        return hash_equals(strtolower((string) $pesanan->email), strtolower($contact))
            || ($phone !== '' && hash_equals($orderPhone, $phone));
    }

    public function showInvoice(Request $request, string $kode)
    {
        $pesanan = $this->authorizedOrder($request, $kode);

        return view('pesanan-invoice', compact('pesanan'));
    }

    public function cancelOrder(Request $request, string $kode)
    {
        $pesanan = $this->authorizedOrder($request, $kode);

        try {
            $pesanan->transitionTo(Pesanan::STATUS_CANCELLED, 'customer');
        } catch (DomainException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return redirect($this->signedOrderUrl($pesanan))->with('status', 'Pesanan berhasil dibatalkan.');
    }

    public function confirmReceived(Request $request, string $kode)
    {
        $pesanan = $this->authorizedOrder($request, $kode);

        try {
            $pesanan->transitionTo(Pesanan::STATUS_COMPLETED, 'customer');
        } catch (DomainException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return redirect($this->signedOrderUrl($pesanan))->with('status', 'Terima kasih, pesanan sudah ditandai selesai.');
    }

    public function requestAfterSales(Request $request, string $kode)
    {
        $pesanan = $this->authorizedOrder($request, $kode);

        if (! $pesanan->canRequestAfterSales()) {
            return back()->with('error', 'Pesanan ini belum bisa diajukan untuk after-sales atau sudah memiliki request aktif.');
        }

        $validated = $request->validate([
            'type' => 'required|in:return,refund,issue',
            'solution' => 'nullable|in:return_refund,refund_only,exchange,item_check',
            'items' => 'nullable|array|max:10',
            'items.*' => 'string|max:255',
            'evidence_urls' => 'nullable|array|max:5',
            'evidence_urls.*' => 'url|max:1000',
            'reason' => 'required|string|min:10|max:1000',
        ], [
            'type.required' => 'Pilih jenis kebutuhan after-sales.',
            'type.in' => 'Jenis after-sales tidak valid.',
            'reason.required' => 'Mohon jelaskan kendala yang dialami.',
            'reason.min' => 'Penjelasan minimal 10 karakter.',
        ]);

        $pesanan->forceFill([
            'after_sales_status' => 'requested',
            'after_sales_type' => $validated['type'],
            'after_sales_solution' => $validated['solution'] ?? null,
            'after_sales_reason' => trim($validated['reason']),
            'after_sales_items' => array_values($validated['items'] ?? []),
            'after_sales_evidence' => array_values($validated['evidence_urls'] ?? []),
            'after_sales_requested_at' => now(),
        ])->save();

        return redirect($this->signedOrderUrl($pesanan).'#after-sales')->with('status', 'Permintaan after-sales berhasil dikirim. Tim kami akan meninjau pesanan Anda.');
    }

    /**
     * Midtrans callback/notification handler.
     */
    public function midtransCallback(Request $request)
    {
        $serverKey = config('services.midtrans.server_key');

        // Verify notification authenticity
        $hashed = hash('sha512', $request->input('order_id') . $request->input('status_code') . $request->input('gross_amount') . $serverKey);
        $signature = $request->input('signature_key');

        if ($hashed !== $signature) {
            Log::warning('Midtrans: Invalid signature', ['order_id' => $request->input('order_id')]);
            return response()->json(['status' => 'invalid signature'], 403);
        }

        $orderId = $request->input('order_id');
        $pesanan = Pesanan::where('kode_pesanan', $orderId)->first();

        if (! $pesanan) {
            Log::warning('Midtrans: Order not found', ['order_id' => $orderId]);
            return response()->json(['status' => 'order not found'], 404);
        }

        $transactionStatus = $request->input('transaction_status');
        $fraudStatus = $request->input('fraud_status');

        $pesanan->forceFill([
            'midtrans_status' => $transactionStatus,
            'midtrans_raw_response' => json_encode($request->all()),
        ])->save();

        // Map Midtrans status to our status
        if ($transactionStatus === 'capture' && $fraudStatus === 'accept') {
            $pesanan->transitionTo(Pesanan::STATUS_PAID, 'midtrans');
        } elseif ($transactionStatus === 'settlement') {
            $pesanan->transitionTo(Pesanan::STATUS_PAID, 'midtrans');
        } elseif ($transactionStatus === 'pending') {
            // Still pending, no status change
        } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire'], true)) {
            if ($pesanan->status === Pesanan::STATUS_PENDING_PAYMENT) {
                $pesanan->transitionTo(Pesanan::STATUS_CANCELLED, 'midtrans');
            }
        } elseif ($transactionStatus === 'refund') {
            $pesanan->transitionTo(Pesanan::STATUS_REFUNDED, 'midtrans');
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Regenerate Payment for pending order (e.g. "Bayar Sekarang" button).
     */
    public function retryPayment(Request $request, string $kode)
    {
        $pesanan = $this->authorizedOrder($request, $kode);

        if ($pesanan->status !== Pesanan::STATUS_PENDING_PAYMENT) {
            return back()->with('error', 'Pesanan ini sudah tidak bisa dibayar.');
        }

        $this->processMidtransPayment($pesanan);

        return redirect($this->signedOrderUrl($pesanan))->with('status', 'Metode pembayaran diperbarui.');
    }

    private function isMidtransPayment(string $metode): bool
    {
        $metode = strtolower($metode);

        // Match checkout form values
        if (in_array($metode, ['qris', 'snap', 'va', 'gopay', 'ovo', 'dana', 'shopeepay', 'linkaja'], true)) {
            return true;
        }

        // Match "Transfer Bank BCA", "Transfer Bank Mandiri", etc.
        if (str_contains($metode, 'transfer') || str_contains($metode, 'other bank')) {
            return true;
        }

        // Match anything with qris/va in it
        return str_contains($metode, 'qris') || str_contains($metode, 'va');
    }

    private function getEnabledPayments(string $metode): array
    {
        $metode = strtolower($metode);

        // QRIS — use gopay only so QR code shows directly (universal QRIS, bisa discan semua app)
        if (str_contains($metode, 'qris')) {
            return ['gopay'];
        }

        // Transfer Bank BCA
        if (str_contains($metode, 'bca')) {
            return ['bca_va'];
        }

        // Transfer Bank Mandiri
        if (str_contains($metode, 'mandiri')) {
            return ['echannel'];
        }

        // Transfer Bank BRI
        if (str_contains($metode, 'bri')) {
            return ['bri_va'];
        }

        // Transfer Bank BSI
        if (str_contains($metode, 'bsi')) {
            return ['bsi_va'];
        }

        if (str_contains($metode, 'cimb')) {
            return ['cimb_va'];
        }

        if (str_contains($metode, 'seabank')) {
            return ['seabank_va'];
        }

        if (str_contains($metode, 'danamon')) {
            return ['danamon_va'];
        }

        if (str_contains($metode, 'saqu')) {
            return ['saqu_va'];
        }

        if (str_contains($metode, 'other')) {
            return ['other_va'];
        }

        // Fallback — show VA options
        return ['bca_va', 'echannel', 'bni_va', 'bri_va', 'permata_va', 'cimb_va', 'seabank_va', 'danamon_va', 'bsi_va', 'saqu_va', 'other_va'];
    }

    private function processMidtransPayment(Pesanan $pesanan): void
    {
        try {
            // Configure Midtrans
            \Midtrans\Config::$isProduction = config('services.midtrans.is_production', false);
            \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
            \Midtrans\Config::$clientKey = config('services.midtrans.client_key');

            // Build item details
            $itemDetails = $pesanan->items->map(function ($item) {
                return [
                    'id' => (string) $item->produk_id,
                    'price' => (int) $item->harga,
                    'quantity' => (int) $item->jumlah,
                    'name' => mb_substr($item->nama_produk . ' ' . $item->varian_label, 0, 50),
                ];
            })->toArray();

            // Add shipping cost
            $itemDetails[] = [
                'id' => 'ongkir',
                'price' => (int) $pesanan->ongkir,
                'quantity' => 1,
                'name' => 'Ongkos Kirim',
            ];

            // Discount
            if ($pesanan->diskon > 0) {
                $itemDetails[] = [
                    'id' => 'diskon',
                    'price' => -(int) $pesanan->diskon,
                    'quantity' => 1,
                    'name' => 'Diskon',
                ];
            }

            $params = [
                'transaction_details' => [
                    'order_id' => $pesanan->kode_pesanan,
                    'gross_amount' => (int) $pesanan->total,
                ],
                'item_details' => $itemDetails,
                'customer_details' => [
                    'first_name' => mb_substr($pesanan->nama_penerima, 0, 255),
                    'phone' => $pesanan->telepon,
                    'email' => $pesanan->customerEmail() ?: 'customer@example.com',
                ],
            ];

            $metode = strtolower($pesanan->metode_pembayaran);

            $enabledPayments = $this->getEnabledPayments($pesanan->metode_pembayaran);
            $snapOnlyPayments = ['bsi_va', 'cimb_va', 'seabank_va', 'danamon_va', 'saqu_va', 'other_va'];

            if (array_intersect($enabledPayments, $snapOnlyPayments)) {
                $params['enabled_payments'] = $enabledPayments;

                $response = \Midtrans\Snap::createTransaction($params);

                $pesanan->forceFill([
                    'midtrans_snap_token' => $response->token ?? null,
                    'midtrans_redirect_url' => $response->redirect_url ?? null,
                    'midtrans_raw_response' => json_encode($response),
                    'midtrans_status' => 'pending',
                ])->save();

                return;
            }

            // Set payment type based on method
            if (str_contains($metode, 'qris') || str_contains($metode, 'gopay')) {
                $params['payment_type'] = 'gopay';
            } elseif (str_contains($metode, 'bca')) {
                $params['payment_type'] = 'bank_transfer';
                $params['bank_transfer'] = ['bank' => 'bca'];
            } elseif (str_contains($metode, 'mandiri')) {
                $params['payment_type'] = 'echannel'; // Mandiri uses echannel (Bill Payment)
                $params['echannel'] = ['bill_info1' => 'Payment', 'bill_info2' => 'Auraquina'];
            } elseif (str_contains($metode, 'bri')) {
                $params['payment_type'] = 'bank_transfer';
                $params['bank_transfer'] = ['bank' => 'bri'];
            } elseif (str_contains($metode, 'bni')) {
                $params['payment_type'] = 'bank_transfer';
                $params['bank_transfer'] = ['bank' => 'bni'];
            } elseif (str_contains($metode, 'permata')) {
                $params['payment_type'] = 'bank_transfer';
                $params['bank_transfer'] = ['bank' => 'permata'];
            } else {
                $params['payment_type'] = 'gopay'; // Fallback to QRIS/GoPay
            }

            $response = \Midtrans\CoreApi::charge($params);

            $pesanan->forceFill([
                'midtrans_raw_response' => json_encode($response),
                'midtrans_status' => $response->transaction_status ?? null,
            ])->save();

        } catch (\Exception $e) {
            Log::error('Midtrans Core API Error', [
                'order_id' => $pesanan->kode_pesanan,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
