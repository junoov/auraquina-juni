<?php

namespace Tests\Feature;

use App\Models\ItemPesanan;
use App\Models\Kategori;
use App\Models\LoyaltyVoucher;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\Review;
use App\Models\User;
use App\Models\VarianProduk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoyaltyVoucherTest extends TestCase
{
    use RefreshDatabase;

    public function test_missed_milestones_are_awarded_once(): void
    {
        $user = User::factory()->create();
        $produk = $this->createProduct();

        $this->createQualifiedOrders($user, $produk, 4);
        LoyaltyVoucher::awardForUser($user);

        $this->assertDatabaseHas('loyalty_vouchers', [
            'user_id' => $user->id,
            'milestone' => 3,
            'value' => 15000,
        ]);

        $this->createQualifiedOrders($user, $produk, 2);
        LoyaltyVoucher::awardForUser($user);
        LoyaltyVoucher::awardForUser($user);

        $this->assertSame([3, 6], LoyaltyVoucher::where('user_id', $user->id)->orderBy('milestone')->pluck('milestone')->all());
    }

    public function test_loyalty_code_is_owner_only_and_redeemed_once(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $produk = $this->createProduct();
        $varian = VarianProduk::create([
            'produk_id' => $produk->id,
            'ukuran' => 'M',
            'warna' => 'Hitam',
            'sku' => 'VAR-' . uniqid(),
            'stok' => 5,
        ]);
        $voucher = LoyaltyVoucher::create([
            'user_id' => $owner->id,
            'milestone' => 3,
            'code' => 'AQ15-3-TESTCODE',
            'value' => 15000,
        ]);
        $checkoutPayload = [
            'mode' => 'buy_now',
            'items' => [[
                'produk_id' => $produk->id,
                'varian_id' => $varian->id,
                'slug' => $produk->slug,
                'name' => $produk->nama,
                'variant' => 'Hitam / M',
                'qty' => 1,
                'price' => $produk->harga,
                'img' => null,
            ]],
        ];

        $this->actingAs($otherUser)
            ->withSession(['checkout_payload' => $checkoutPayload])
            ->postJson(route('checkout.voucher'), ['code' => $voucher->code])
            ->assertUnprocessable();

        $this->actingAs($owner)
            ->withSession(['checkout_payload' => $checkoutPayload])
            ->postJson(route('checkout.voucher'), ['code' => $voucher->code])
            ->assertOk()
            ->assertJsonPath('discount', 15000);

        $this->postJson(route('checkout.place-order'), [
            'nama_penerima' => 'Pelanggan Loyalty',
            'telepon' => '081234567890',
            'email' => $owner->email,
            'kota' => 'Bandung',
            'alamat_lengkap' => 'Jl. Loyalty No. 3',
            'metode_pengiriman' => 'JNE Reguler',
            'metode_pembayaran' => 'Transfer BCA',
        ])->assertOk();

        $voucher->refresh();
        $this->assertNotNull($voucher->used_at);
        $this->assertNotNull($voucher->pesanan_id);
        $this->assertDatabaseHas('pesanans', [
            'id' => $voucher->pesanan_id,
            'diskon' => 15000,
        ]);

        $this->withSession(['checkout_payload' => $checkoutPayload])
            ->postJson(route('checkout.voucher'), ['code' => $voucher->code])
            ->assertUnprocessable();
    }

    public function test_multiple_loyalty_vouchers_stack(): void
    {
        $user = User::factory()->create();
        $produk = $this->createProduct();
        $varian = VarianProduk::create([
            'produk_id' => $produk->id,
            'ukuran' => 'M',
            'warna' => 'Hitam',
            'sku' => 'VAR-' . uniqid(),
            'stok' => 10,
        ]);
        $voucher1 = LoyaltyVoucher::create([
            'user_id' => $user->id,
            'milestone' => 3,
            'code' => 'AQ15-3-AAA',
            'value' => 15000,
        ]);
        $voucher2 = LoyaltyVoucher::create([
            'user_id' => $user->id,
            'milestone' => 6,
            'code' => 'AQ15-6-BBB',
            'value' => 15000,
        ]);
        $checkoutPayload = [
            'mode' => 'buy_now',
            'items' => [[
                'produk_id' => $produk->id,
                'varian_id' => $varian->id,
                'slug' => $produk->slug,
                'name' => $produk->nama,
                'variant' => 'Hitam / M',
                'qty' => 1,
                'price' => $produk->harga,
                'img' => null,
            ]],
        ];

        $this->actingAs($user)
            ->withSession(['checkout_payload' => $checkoutPayload])
            ->postJson(route('checkout.voucher'), ['code' => $voucher1->code])
            ->assertOk()
            ->assertJsonPath('discount', 15000);

        $this->actingAs($user)
            ->withSession(['checkout_payload' => $checkoutPayload])
            ->postJson(route('checkout.voucher'), ['code' => $voucher2->code])
            ->assertOk()
            ->assertJsonPath('discount', 30000);

        $this->postJson(route('checkout.place-order'), [
            'nama_penerima' => 'Pelanggan Stack',
            'telepon' => '081234567890',
            'email' => $user->email,
            'kota' => 'Bandung',
            'alamat_lengkap' => 'Jl. Stack No. 6',
            'metode_pengiriman' => 'JNE Reguler',
            'metode_pembayaran' => 'Transfer BCA',
        ])->assertOk();

        $voucher1->refresh();
        $voucher2->refresh();
        $this->assertNotNull($voucher1->used_at);
        $this->assertNotNull($voucher2->used_at);
        $this->assertDatabaseHas('pesanans', [
            'id' => $voucher1->pesanan_id,
            'diskon' => 30000,
        ]);
    }

    public function test_voucher_stack_capped_at_subtotal_plus_shipping(): void
    {
        $user = User::factory()->create();
        $produk = $this->createProduct();
        $varian = VarianProduk::create([
            'produk_id' => $produk->id,
            'ukuran' => 'M',
            'warna' => 'Hitam',
            'sku' => 'VAR-' . uniqid(),
            'stok' => 10,
        ]);
        $voucher1 = LoyaltyVoucher::create([
            'user_id' => $user->id,
            'milestone' => 3,
            'code' => 'AQ15-3-CCC',
            'value' => 15000,
        ]);
        $voucher2 = LoyaltyVoucher::create([
            'user_id' => $user->id,
            'milestone' => 6,
            'code' => 'AQ15-6-DDD',
            'value' => 15000,
        ]);
        $voucher3 = LoyaltyVoucher::create([
            'user_id' => $user->id,
            'milestone' => 9,
            'code' => 'AQ15-9-EEE',
            'value' => 15000,
        ]);
        $checkoutPayload = [
            'mode' => 'buy_now',
            'items' => [[
                'produk_id' => $produk->id,
                'varian_id' => $varian->id,
                'slug' => $produk->slug,
                'name' => $produk->nama,
                'variant' => 'Hitam / M',
                'qty' => 1,
                'price' => $produk->harga,
                'img' => null,
            ]],
        ];

        $this->actingAs($user)
            ->withSession(['checkout_payload' => $checkoutPayload])
            ->postJson(route('checkout.voucher'), ['code' => $voucher1->code])
            ->assertOk()
            ->assertJsonPath('discount', 15000);

        $this->actingAs($user)
            ->withSession(['checkout_payload' => $checkoutPayload])
            ->postJson(route('checkout.voucher'), ['code' => $voucher2->code])
            ->assertOk()
            ->assertJsonPath('discount', 30000);

        $this->actingAs($user)
            ->withSession(['checkout_payload' => $checkoutPayload])
            ->postJson(route('checkout.voucher'), ['code' => $voucher3->code])
            ->assertOk()
            ->assertJsonPath('discount', 45000);

        $this->postJson(route('checkout.place-order'), [
            'nama_penerima' => 'Pelanggan Cap',
            'telepon' => '081234567890',
            'email' => $user->email,
            'kota' => 'Bandung',
            'alamat_lengkap' => 'Jl. Cap No. 9',
            'metode_pengiriman' => 'JNE Reguler',
            'metode_pembayaran' => 'Transfer BCA',
        ])->assertOk();

        $this->assertDatabaseHas('pesanans', [
            'diskon' => 45000,
            'total' => 66500,
        ]);
    }

    private function createQualifiedOrders(User $user, Produk $produk, int $count): void
    {
        for ($index = 0; $index < $count; $index++) {
            $pesanan = Pesanan::create([
                'kode_pesanan' => Pesanan::generateKode(),
                'session_id' => 'loyalty-' . uniqid(),
                'user_id' => $user->id,
                'status' => Pesanan::STATUS_COMPLETED,
                'nama_penerima' => $user->name,
                'telepon' => '081234567890',
                'metode_pengiriman' => 'JNE Reguler',
                'metode_pembayaran' => 'Transfer BCA',
                'subtotal' => $produk->harga,
                'ongkir' => 11500,
                'diskon' => 0,
                'total' => $produk->harga + 11500,
                'batas_bayar' => now()->addHour(),
            ]);
            ItemPesanan::create([
                'pesanan_id' => $pesanan->id,
                'produk_id' => $produk->id,
                'nama_produk' => $produk->nama,
                'varian_label' => 'Default',
                'harga' => $produk->harga,
                'jumlah' => 1,
            ]);
            Review::create([
                'produk_id' => $produk->id,
                'user_id' => $user->id,
                'pesanan_id' => $pesanan->id,
                'rating' => 5,
                'review' => 'Ulasan pembelian loyalty yang valid dan cukup panjang.',
                'status' => Review::STATUS_APPROVED,
            ]);
        }
    }

    private function createProduct(): Produk
    {
        $suffix = str_replace('.', '', uniqid('', true));
        $kategori = Kategori::create([
            'nama' => 'Loyalty ' . $suffix,
            'slug' => 'loyalty-' . $suffix,
            'aktif' => true,
        ]);

        return Produk::create([
            'kategori_id' => $kategori->id,
            'nama' => 'Produk Loyalty',
            'slug' => 'produk-loyalty-' . $suffix,
            'harga' => 100000,
            'sku' => 'LOY-' . $suffix,
            'aktif' => true,
        ]);
    }
}
