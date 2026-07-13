<?php

namespace Tests\Feature;

use App\Models\Pesanan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountOrderCenterTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_update_profile(): void
    {
        $user = User::factory()->create([
            'phone' => '081111111111',
        ]);

        $this->actingAs($user)
            ->patch(route('account.profile.update'), [
                'name' => 'Aisha Updated',
                'email' => 'aisha-updated@example.com',
                'phone' => '082222222222',
            ])
            ->assertRedirect(route('account.show'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Aisha Updated',
            'email' => 'aisha-updated@example.com',
            'phone' => '082222222222',
        ]);
    }

    public function test_authenticated_user_can_see_orders_page(): void
    {
        $user = User::factory()->create();
        $pesanan = $this->createOrder([
            'user_id' => $user->id,
            'status' => Pesanan::STATUS_DELIVERED,
        ]);

        $this->actingAs($user)
            ->get(route('account.orders'))
            ->assertOk()
            ->assertSee('Pesanan Saya')
            ->assertSee($pesanan->kode_pesanan);
    }

    public function test_owner_can_submit_after_sales_request_for_delivered_order(): void
    {
        $user = User::factory()->create();
        $pesanan = $this->createOrder([
            'user_id' => $user->id,
            'session_id' => 'different-session',
            'status' => Pesanan::STATUS_DELIVERED,
        ]);

        $this->actingAs($user)
            ->post(route('pesanan.after-sales', $pesanan->kode_pesanan), [
                'type' => 'issue',
                'reason' => 'Produk yang diterima memiliki kendala dan butuh ditinjau.',
            ])
            ->assertRedirect();

        $pesanan->refresh();

        $this->assertSame('requested', $pesanan->after_sales_status);
        $this->assertSame('issue', $pesanan->after_sales_type);
        $this->assertNotNull($pesanan->after_sales_requested_at);
    }

    public function test_order_detail_shows_status_timeline_and_richer_after_sales_state(): void
    {
        $user = User::factory()->create();
        $pesanan = $this->createOrder([
            'user_id' => $user->id,
            'status' => Pesanan::STATUS_SHIPPED,
            'kurir_pengiriman' => 'JNE',
            'nomor_resi' => 'JNE123456789',
            'dikirim_pada' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('pesanan.show', $pesanan->kode_pesanan))
            ->assertOk()
            ->assertSee('Perjalanan Pesanan')
            ->assertSee('Dikemas')
            ->assertSee('Dikirim')
            ->assertSee('JNE123456789')
            ->assertSee('After-Sales Care');
    }

    public function test_after_sales_request_stores_solution_items_and_evidence(): void
    {
        $user = User::factory()->create();
        $pesanan = $this->createOrder([
            'user_id' => $user->id,
            'status' => Pesanan::STATUS_DELIVERED,
        ]);

        $this->actingAs($user)
            ->post(route('pesanan.after-sales', $pesanan->kode_pesanan), [
                'type' => 'refund',
                'solution' => 'refund_only',
                'items' => ['Khimar Mocha - warna tidak sesuai'],
                'evidence_urls' => ['https://example.com/bukti.jpg'],
                'reason' => 'Warna produk yang diterima berbeda jauh dari foto katalog.',
            ])
            ->assertRedirect();

        $pesanan->refresh();

        $this->assertSame('refund_only', $pesanan->after_sales_solution);
        $this->assertSame(['Khimar Mocha - warna tidak sesuai'], $pesanan->after_sales_items);
        $this->assertSame(['https://example.com/bukti.jpg'], $pesanan->after_sales_evidence);
    }

    public function test_authenticated_user_can_manage_multiple_saved_addresses(): void
    {
        $user = User::factory()->create([
            'phone' => '081111111111',
        ]);

        $this->actingAs($user)
            ->post(route('account.addresses.store'), [
                'label' => 'Rumah',
                'recipient_name' => 'Aisha Rumah',
                'city' => 'Malang',
                'address' => 'Jl. Melati No. 7',
                'is_default' => '1',
            ])
            ->assertRedirect(route('account.delivery'));

        $this->assertDatabaseHas('user_addresses', [
            'user_id' => $user->id,
            'label' => 'Rumah',
            'recipient_name' => 'Aisha Rumah',
            'phone' => '081111111111',
            'is_default' => true,
        ]);

        // Ensure only ONE address was created (no duplicate submission bug)
        $this->assertEquals(1, $user->addresses()->count());

        $this->actingAs($user)
            ->get(route('account.delivery'))
            ->assertOk()
            ->assertSee('Alamat Tersimpan')
            ->assertSee('Aisha Rumah')
            ->assertSee('Utama');
    }

    public function test_duplicate_address_submission_is_prevented(): void
    {
        $user = User::factory()->create([
            'phone' => '081111111111',
        ]);

        $payload = [
            'label' => 'Rumah',
            'recipient_name' => 'Aisha Rumah',
            'city' => 'Malang',
            'address' => 'Jl. Melati No. 7',
            'is_default' => '1',
        ];

        // First submission creates the address
        $this->actingAs($user)->post(route('account.addresses.store'), $payload)
            ->assertRedirect(route('account.delivery'));

        // Second identical submission should NOT create a duplicate
        $this->actingAs($user)->post(route('account.addresses.store'), $payload)
            ->assertRedirect(route('account.delivery'));

        // Only ONE address should exist
        $this->assertEquals(1, $user->addresses()->count());
    }

    public function test_checkout_renders_saved_address_selector(): void
    {
        $user = User::factory()->create([
            'name' => 'Aisha Customer',
            'email' => 'aisha@example.com',
        ]);

        $user->addresses()->create([
            'label' => 'Rumah',
            'recipient_name' => 'Aisha Rumah',
            'phone' => '081222222222',
            'city' => 'Malang',
            'address' => 'Jl. Melati No. 7',
            'is_default' => true,
        ]);

        $this->actingAs($user)
            ->withSession([
                'checkout_payload' => [
                    'mode' => 'buy_now',
                    'items' => [[
                        'id' => 1,
                        'name' => 'Khimar Mocha',
                        'variant' => 'M / Mocha',
                        'qty' => 1,
                        'price' => 199000,
                        'img' => 'https://example.com/product.jpg',
                    ]],
                ],
            ])
            ->get(route('checkout'))
            ->assertOk()
            ->assertSee('Alamat Tersimpan')
            ->assertSee('Rumah · Aisha Rumah')
            ->assertSee('selectSavedAddress(this)', false)
            ->assertSee('let selectedCheckoutAddress = null;', false)
            ->assertSee('data-address="Jl. Melati No. 7"', false);
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
}
