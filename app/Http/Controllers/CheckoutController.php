<?php

namespace App\Http\Controllers;

use App\Models\ItemKeranjang;
use App\Models\ItemPesanan;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\VarianProduk;
use App\Models\Voucher;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
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
            'img' => $varian?->gambarVarianUtama?->url ?? $produk->gambarUtama?->url,
        ];
    }

    private function cartItems(?array $selectedIds = null): Collection
    {
        $query = ItemKeranjang::with(['produk.gambarUtama', 'varian.gambarVarianUtama'])
            ->where('session_id', $this->sessionId());

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
            return redirect()->route('keranjang.index');
        }

        $totals = $this->checkoutTotals($checkoutItems);
        $subtotal = $totals['subtotal'];
        $shipping = $totals['shipping'];
        $discount = $totals['discount'];
        $total = $totals['total'];
        $voucher = $totals['voucher'];

        return view('checkout', compact('checkoutItems', 'subtotal', 'shipping', 'discount', 'total', 'voucher'));
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

        $selectedIds = ItemKeranjang::where('session_id', $this->sessionId())
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
                    ItemKeranjang::where('session_id', $this->sessionId())
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
            'after_sales_reason' => trim($validated['reason']),
            'after_sales_requested_at' => now(),
        ])->save();

        return redirect($this->signedOrderUrl($pesanan).'#after-sales')->with('status', 'Permintaan after-sales berhasil dikirim. Tim kami akan meninjau pesanan Anda.');
    }
}
