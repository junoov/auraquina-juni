<?php

namespace Tests\Feature;

use App\Mail\OrderPlacedMail;
use App\Mail\OrderStatusUpdatedMail;
use App\Models\Kategori;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\VarianProduk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class OrderNotificationInvoiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_place_order_sends_customer_email_notification(): void
    {
        Mail::fake();

        [$produk, $varian] = $this->createProductVariant();

        $this->withSession([
            'checkout_payload' => [
                'mode' => 'buy_now',
                'items' => [[
                    'produk_id' => $produk->id,
                    'varian_id' => $varian->id,
                    'slug' => $produk->slug,
                    'name' => $produk->nama,
                    'variant' => $varian->warna.' / '.$varian->ukuran,
                    'qty' => 1,
                    'price' => $produk->harga,
                    'img' => null,
                ]],
            ],
        ])->postJson(route('checkout.place-order'), [
            'nama_penerima' => 'Aisha',
            'telepon' => '081234567890',
            'email' => 'customer@example.com',
            'kota' => 'Malang',
            'alamat_lengkap' => 'Jl. Tenang No. 1',
            'metode_pengiriman' => 'JNE Reguler',
            'metode_pembayaran' => 'QRIS',
        ])->assertOk();

        Mail::assertSent(OrderPlacedMail::class);
    }

    public function test_signed_invoice_route_is_accessible(): void
    {
        $pesanan = $this->createOrder([
            'email' => 'customer@example.com',
            'status' => Pesanan::STATUS_PAID,
        ]);
        $url = URL::temporarySignedRoute('pesanan.invoice', now()->addDays(30), [
            'kode' => $pesanan->kode_pesanan,
        ]);

        $this->get($url)
            ->assertOk()
            ->assertSee($pesanan->kode_pesanan);
    }

    public function test_status_transition_sends_update_email(): void
    {
        Mail::fake();

        $pesanan = $this->createOrder([
            'email' => 'customer@example.com',
            'status' => Pesanan::STATUS_PENDING_PAYMENT,
        ]);

        $pesanan->transitionTo(Pesanan::STATUS_PAID, 'test');

        Mail::assertSent(OrderStatusUpdatedMail::class);
    }

    private function createOrder(array $overrides = []): Pesanan
    {
        return Pesanan::create(array_merge([
            'kode_pesanan' => Pesanan::generateKode(),
            'session_id' => 'test-session',
            'user_id' => null,
            'status' => Pesanan::STATUS_PENDING_PAYMENT,
            'nama_penerima' => 'Aisha',
            'telepon' => '081234567890',
            'email' => 'customer@example.com',
            'kota' => 'Malang',
            'alamat_lengkap' => 'Jl. Tenang No. 1',
            'metode_pengiriman' => 'JNE Reguler',
            'metode_pembayaran' => 'QRIS',
            'subtotal' => 489000,
            'ongkir' => 11500,
            'diskon' => 0,
            'total' => 500500,
            'batas_bayar' => now()->addHour(),
        ], $overrides));
    }

    private function createProductVariant(): array
    {
        $suffix = str_replace('.', '', uniqid('', true));
        $kategori = Kategori::create([
            'nama' => 'Kategori '.$suffix,
            'slug' => 'kategori-'.$suffix,
            'aktif' => true,
        ]);

        $produk = Produk::create([
            'kategori_id' => $kategori->id,
            'nama' => 'Produk '.$suffix,
            'slug' => 'produk-'.$suffix,
            'harga' => 199000,
            'sku' => 'PRD-'.$suffix,
            'aktif' => true,
        ]);

        $varian = VarianProduk::create([
            'produk_id' => $produk->id,
            'ukuran' => 'M',
            'warna' => 'Hitam',
            'sku' => 'VAR-'.$suffix,
            'stok' => 5,
            'penyesuaian_harga' => 0,
        ]);

        return [$produk, $varian];
    }
}
