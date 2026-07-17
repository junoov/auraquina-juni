<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Pages\StokManagement;
use App\Filament\Admin\Resources\Pesanans\PesananResource;
use App\Models\Pesanan;
use App\Models\VarianProduk;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class DashboardStats extends BaseWidget
{
    protected ?string $pollingInterval = null;

    protected int|string|array $columnSpan = 'full';

    protected function getColumns(): int|array|null
    {
        return [
            'default' => 1,
            'sm' => 2,
            'xl' => 4,
        ];
    }

    protected function getStats(): array
    {
        $stats = Cache::remember('admin.dashboard.stats', 60, fn () => [
            'todayOrders' => Pesanan::whereDate('created_at', today())->count(),
            'todayRevenue' => (int) Pesanan::whereIn('status', ['paid', 'processing', 'packed', 'shipped', 'delivered', 'completed'])
                ->whereDate('created_at', today())
                ->sum('total'),
            'needsShipping' => Pesanan::whereIn('status', ['paid', 'processing', 'packed'])->count(),
            'lowStock' => VarianProduk::where('stok', '<', 5)->count(),
        ]);

        return [
            Stat::make('Pesanan baru', $stats['todayOrders'])
                ->description('Masuk hari ini')
                ->icon('heroicon-o-shopping-bag')
                ->url(PesananResource::getUrl('index'))
                ->color('primary'),
            Stat::make('Penjualan hari ini', 'Rp '.number_format($stats['todayRevenue'], 0, ',', '.'))
                ->description('Dari pesanan sudah dibayar')
                ->icon('heroicon-o-banknotes')
                ->url(PesananResource::getUrl('index'))
                ->color('success'),
            Stat::make('Siap diproses', $stats['needsShipping'])
                ->description('Perlu dikemas atau dikirim')
                ->icon('heroicon-o-truck')
                ->url(PesananResource::getUrl('index'))
                ->color($stats['needsShipping'] > 0 ? 'warning' : 'gray'),
            Stat::make('Stok perlu dicek', $stats['lowStock'])
                ->description('Varian kurang dari 5')
                ->icon('heroicon-o-archive-box')
                ->url(StokManagement::getUrl())
                ->color($stats['lowStock'] > 0 ? 'danger' : 'gray'),
        ];
    }
}
