<?php

namespace Tests\Feature;

use App\Models\Pesanan;
use App\Models\ItemPesanan;
use App\Models\Kategori;
use App\Models\Produk;
use App\Models\User;
use App\Models\VarianProduk;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class OrderAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_detail_rejects_unknown_guest_without_signed_url(): void
    {
        $pesanan = $this->createOrder(['session_id' => 'different-session']);

        $this->get(route('pesanan.show', $pesanan->kode_pesanan))
            ->assertForbidden();
    }

    public function test_order_detail_allows_valid_signed_url(): void
    {
        $pesanan = $this->createOrder(['session_id' => 'different-session']);
        $url = URL::temporarySignedRoute('pesanan.show', now()->addDays(30), [
            'kode' => $pesanan->kode_pesanan,
        ]);

        $this->get($url)
            ->assertOk()
            ->assertSee($pesanan->kode_pesanan);
    }

    public function test_order_detail_allows_owner_user(): void
    {
        $user = User::factory()->create();
        $pesanan = $this->createOrder([
            'session_id' => 'different-session',
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)
            ->get(route('pesanan.show', $pesanan->kode_pesanan))
            ->assertOk()
            ->assertSee($pesanan->kode_pesanan);
    }

    public function test_order_status_transition_blocks_invalid_jump(): void
    {
        $pesanan = $this->createOrder(['status' => Pesanan::STATUS_PENDING_PAYMENT]);

        $this->expectException(DomainException::class);

        $pesanan->transitionTo(Pesanan::STATUS_SHIPPED, 'test');
    }

    public function test_overdue_pending_order_expires_when_viewed(): void
    {
        $pesanan = $this->createOrder([
            'status' => Pesanan::STATUS_PENDING_PAYMENT,
            'batas_bayar' => now()->subMinute(),
        ]);
        $url = URL::temporarySignedRoute('pesanan.show', now()->addDays(30), [
            'kode' => $pesanan->kode_pesanan,
        ]);

        $this->get($url)->assertOk();

        $this->assertSame(Pesanan::STATUS_EXPIRED, $pesanan->refresh()->status);
    }

    public function test_place_order_reserves_variant_stock(): void
    {
        [$produk, $varian] = $this->createProductVariant(['stok' => 5]);

        $this->withSession([
            'checkout_payload' => [
                'mode' => 'buy_now',
                'items' => [$this->checkoutItem($produk, $varian, 2)],
            ],
        ])->postJson(route('checkout.place-order'), $this->validCheckoutPayload())
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertSame(3, $varian->refresh()->stok);
        $this->assertNotNull(Pesanan::latest('id')->first()->stock_reserved_at);
    }

    public function test_place_order_rejects_when_variant_stock_is_insufficient(): void
    {
        [$produk, $varian] = $this->createProductVariant(['stok' => 1]);

        $this->withSession([
            'checkout_payload' => [
                'mode' => 'buy_now',
                'items' => [$this->checkoutItem($produk, $varian, 2)],
            ],
        ])->postJson(route('checkout.place-order'), $this->validCheckoutPayload())
            ->assertUnprocessable()
            ->assertJsonPath('error', 'Stok Test Product (Hitam / M) tidak mencukupi.');

        $this->assertSame(1, $varian->refresh()->stok);
    }

    public function test_authenticated_checkout_updates_only_current_users_delivery_address(): void
    {
        [$produk, $varian] = $this->createProductVariant(['stok' => 5]);
        $currentUser = User::factory()->create([
            'recipient_name' => 'Alamat Lama',
            'phone' => '081111111111',
            'city' => 'Kota Lama',
            'address' => 'Jl. Lama No. 1',
        ]);
        $otherUser = User::factory()->create([
            'recipient_name' => 'Akun Lain',
            'phone' => '082222222222',
            'city' => 'Kota Lain',
            'address' => 'Jl. Lain No. 2',
        ]);

        $this->actingAs($currentUser)
            ->withSession([
                'checkout_payload' => [
                    'mode' => 'buy_now',
                    'items' => [$this->checkoutItem($produk, $varian, 1)],
                ],
            ])->postJson(route('checkout.place-order'), [
                'nama_penerima' => 'Penerima Baru',
                'telepon' => '089999999999',
                'kota' => 'Kota Baru',
                'alamat_lengkap' => 'Jl. Baru No. 9',
                'metode_pengiriman' => 'JNE Reguler',
                'metode_pembayaran' => 'QRIS',
            ])->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('users', [
            'id' => $currentUser->id,
            'recipient_name' => 'Penerima Baru',
            'phone' => '089999999999',
            'city' => 'Kota Baru',
            'address' => 'Jl. Baru No. 9',
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $otherUser->id,
            'recipient_name' => 'Akun Lain',
            'phone' => '082222222222',
            'city' => 'Kota Lain',
            'address' => 'Jl. Lain No. 2',
        ]);
    }

    public function test_cancelled_reserved_order_restores_variant_stock_once(): void
    {
        [$produk, $varian] = $this->createProductVariant(['stok' => 3]);
        $pesanan = $this->createOrder(['stock_reserved_at' => now()]);
        $this->createOrderItem($pesanan, $produk, $varian, 2);

        $pesanan->transitionTo(Pesanan::STATUS_CANCELLED, 'test');

        $this->assertSame(5, $varian->refresh()->stok);
        $this->assertNull($pesanan->refresh()->stock_reserved_at);
    }

    public function test_overdue_reserved_order_restores_variant_stock_when_expired(): void
    {
        [$produk, $varian] = $this->createProductVariant(['stok' => 3]);
        $pesanan = $this->createOrder([
            'stock_reserved_at' => now(),
            'batas_bayar' => now()->subMinute(),
        ]);
        $this->createOrderItem($pesanan, $produk, $varian, 2);
        $url = URL::temporarySignedRoute('pesanan.show', now()->addDays(30), [
            'kode' => $pesanan->kode_pesanan,
        ]);

        $this->get($url)->assertOk();

        $this->assertSame(Pesanan::STATUS_EXPIRED, $pesanan->refresh()->status);
        $this->assertSame(5, $varian->refresh()->stok);
        $this->assertNull($pesanan->stock_reserved_at);
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

    private function createProductVariant(array $variantOverrides = []): array
    {
        $suffix = str_replace('.', '', uniqid('', true));
        $kategori = Kategori::create([
            'nama' => 'Test Category '.$suffix,
            'slug' => 'test-category-'.$suffix,
            'aktif' => true,
        ]);
        $produk = Produk::create([
            'kategori_id' => $kategori->id,
            'nama' => 'Test Product',
            'slug' => 'test-product-'.$suffix,
            'harga' => 100000,
            'sku' => 'TP-'.$suffix,
            'aktif' => true,
        ]);
        $varian = VarianProduk::create(array_merge([
            'produk_id' => $produk->id,
            'ukuran' => 'M',
            'warna' => 'Hitam',
            'sku' => 'TV-'.$suffix,
            'stok' => 5,
            'penyesuaian_harga' => 0,
        ], $variantOverrides));

        return [$produk, $varian];
    }

    private function checkoutItem(Produk $produk, VarianProduk $varian, int $qty): array
    {
        return [
            'produk_id' => $produk->id,
            'varian_id' => $varian->id,
            'slug' => $produk->slug,
            'name' => $produk->nama,
            'variant' => $varian->warna.' / '.$varian->ukuran,
            'qty' => $qty,
            'price' => $produk->harga,
            'img' => null,
        ];
    }

    private function createOrderItem(Pesanan $pesanan, Produk $produk, VarianProduk $varian, int $qty): ItemPesanan
    {
        return ItemPesanan::create([
            'pesanan_id' => $pesanan->id,
            'produk_id' => $produk->id,
            'varian_id' => $varian->id,
            'nama_produk' => $produk->nama,
            'varian_label' => $varian->warna.' / '.$varian->ukuran,
            'harga' => $produk->harga,
            'jumlah' => $qty,
        ]);
    }

    private function validCheckoutPayload(): array
    {
        return [
            'nama_penerima' => 'Aisha',
            'telepon' => '081234567890',
            'kota' => 'Malang',
            'alamat_lengkap' => 'Jl. Tenang No. 1',
            'metode_pengiriman' => 'JNE Reguler',
            'metode_pembayaran' => 'QRIS',
        ];
    }
}
