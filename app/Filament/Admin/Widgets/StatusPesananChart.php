<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Pesanan;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class StatusPesananChart extends ChartWidget
{
    protected ?string $heading = 'Status pesanan';

    protected ?string $description = 'Jumlah pesanan di setiap tahap.';

    protected ?string $maxHeight = '320px';

    protected ?string $pollingInterval = null;

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $labels = [
            Pesanan::STATUS_PENDING_PAYMENT => 'Menunggu Bayar',
            Pesanan::STATUS_PAID => 'Dibayar',
            Pesanan::STATUS_PROCESSING => 'Diproses',
            Pesanan::STATUS_PACKED => 'Dikemas',
            Pesanan::STATUS_SHIPPED => 'Dikirim',
            Pesanan::STATUS_DELIVERED => 'Diterima',
            Pesanan::STATUS_COMPLETED => 'Selesai',
            Pesanan::STATUS_CANCELLED => 'Batal',
        ];

        // Single query with GROUP BY + cache - instead of 8 separate COUNT queries
        $counts = Cache::remember('admin.dashboard.status_counts', 120, function () use ($labels) {
            $results = Pesanan::query()
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            // Map to label order with 0 for missing statuses
            return collect(array_keys($labels))->mapWithKeys(
                fn ($status) => [$status => $results[$status] ?? 0]
            )->toArray();
        });

        return [
            'datasets' => [[
                'label' => 'Jumlah pesanan',
                'data' => array_values($counts),
                'backgroundColor' => [
                    '#6B7280',
                    '#111827',
                    '#4B5563',
                    '#9CA3AF',
                    '#374151',
                    '#059669',
                    '#059669',
                    '#DC2626',
                ],
            ]],
            'labels' => array_values($labels),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
