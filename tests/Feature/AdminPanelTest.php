<?php

namespace Tests\Feature;

use App\Filament\Admin\Resources\Pesanans\Schemas\PesananInfolist;
use App\Filament\Admin\Widgets\DashboardStats;
use App\Models\Pesanan;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
        $this->owner = User::where('email', 'owner@auraquina.id')->firstOrFail();
    }

    public function test_owner_sees_a_clear_action_oriented_dashboard(): void
    {
        $this->actingAs($this->owner)
            ->get('/admin')
            ->assertOk()
            ->assertSeeText('Ringkasan Toko')
            ->assertSeeText('Lihat pesanan, pembayaran, dan stok yang perlu ditangani hari ini.');

        Livewire::test(DashboardStats::class)
            ->assertSeeText('Pesanan baru')
            ->assertSeeText('Penjualan hari ini')
            ->assertSeeText('Siap diproses')
            ->assertSeeText('Stok perlu dicek');
    }

    public function test_admin_navigation_uses_plain_task_based_labels(): void
    {
        $this->actingAs($this->owner)
            ->get('/admin')
            ->assertOk()
            ->assertSeeText('Produk & Stok')
            ->assertSeeText('Pesanan Masuk')
            ->assertSeeText('Promosi')
            ->assertSeeText('Pengaturan Toko');
    }

    public function test_stock_page_explains_inventory_status_without_technical_language(): void
    {
        $this->actingAs($this->owner)
            ->get('/admin/stok-management')
            ->assertOk()
            ->assertSeeText('Stok Produk')
            ->assertSeeText('Total Varian')
            ->assertSeeText('Aman')
            ->assertSeeText('Hampir habis')
            ->assertSeeText('Habis');
    }

    public function test_owner_can_open_an_order_with_activity_history(): void
    {
        $pesanan = $this->createOrder([
            'status' => Pesanan::STATUS_PAID,
        ]);

        activity('pesanan')
            ->performedOn($pesanan)
            ->causedBy($this->owner)
            ->withProperties([
                'actor' => 'admin',
                'from_status' => Pesanan::STATUS_PENDING_PAYMENT,
                'to_status' => Pesanan::STATUS_PAID,
            ])
            ->log('status_changed');

        $activityState = PesananInfolist::activityLog($pesanan);

        $this->assertIsArray($activityState);
        $this->assertContains('status_changed', array_column($activityState, 'description'));

        $this->actingAs($this->owner)
            ->get(route('filament.admin.resources.pesanans.view', ['record' => $pesanan]))
            ->assertOk()
            ->assertSeeText('Riwayat Aktivitas');
    }

    public function test_pelanggan_cannot_access_admin_panel(): void
    {
        $pelanggan = User::factory()->create();
        $pelanggan->assignRole('pelanggan');

        $this->actingAs($pelanggan)
            ->get('/admin')
            ->assertForbidden();
    }

    private function createOrder(array $overrides = []): Pesanan
    {
        return Pesanan::create(array_merge([
            'kode_pesanan' => Pesanan::generateKode(),
            'session_id' => 'admin-panel-test',
            'status' => Pesanan::STATUS_PENDING_PAYMENT,
            'nama_penerima' => 'Aisha',
            'telepon' => '081234567890',
            'kota' => 'Malang',
            'alamat_lengkap' => 'Jl. Tenang No. 1',
            'metode_pengiriman' => 'JNE Reguler',
            'metode_pembayaran' => 'Transfer Bank',
            'subtotal' => 100000,
            'ongkir' => 10000,
            'diskon' => 0,
            'total' => 110000,
            'batas_bayar' => now()->addHour(),
        ], $overrides));
    }
}
