<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\Review;
use App\Models\VarianProduk;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class DashboardStats extends BaseWidget
{
    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $stats = Cache::remember('admin.dashboard.stats', 60, fn () => [
            'todayOrders' => Pesanan::whereDate('created_at', today())->count(),
            'todayRevenue' => (int) Pesanan::whereIn('status', ['paid', 'processing', 'packed', 'shipped', 'delivered', 'completed'])
                ->whereDate('created_at', today())
                ->sum('total'),
            'pendingPayment' => Pesanan::where('status', 'pending_payment')->count(),
            'needsShipping' => Pesanan::whereIn('status', ['paid', 'processing', 'packed'])->count(),
            'activeAfterSales' => Pesanan::whereIn('after_sales_status', ['requested', 'in_review'])->count(),
            'pendingReviews' => Review::where('status', Review::STATUS_PENDING)->count(),
            'lowStock' => VarianProduk::where('stok', '<', 5)->count(),
            'totalProduk' => Produk::where('aktif', true)->count(),
        ]);

        return [
            Stat::make('Pesanan Hari Ini', $stats['todayOrders'])
                ->description('Total pesanan masuk')
                ->color('primary'),
            Stat::make('Pendapatan Hari Ini', 'Rp '.number_format($stats['todayRevenue'], 0, ',', '.'))
                ->description('Pesanan sudah dibayar')
                ->color('success'),
            Stat::make('Menunggu Pembayaran', $stats['pendingPayment'])
                ->description('Belum bayar')
                ->color('warning'),
            Stat::make('Perlu Dikirim', $stats['needsShipping'])
                ->description('Dibayar / diproses / dikemas')
                ->color('info'),
            Stat::make('After-Sales Aktif', $stats['activeAfterSales'])
                ->description('Request yang masih ditangani')
                ->color($stats['activeAfterSales'] > 0 ? 'warning' : 'gray'),
            Stat::make('Ulasan Menunggu', $stats['pendingReviews'])
                ->description('Perlu ditinjau admin')
                ->color($stats['pendingReviews'] > 0 ? 'danger' : 'gray'),
            Stat::make('Stok Rendah', $stats['lowStock'])
                ->description('Varian < 5 stok')
                ->color($stats['lowStock'] > 0 ? 'danger' : 'gray'),
            Stat::make('Produk Aktif', $stats['totalProduk'])
                ->description('Status aktif')
                ->color('gray'),
        ];
    }
}
