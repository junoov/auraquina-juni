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
            ->assertSee('My Orders')
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
