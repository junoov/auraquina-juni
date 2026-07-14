<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Pesanan;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class StatusPesananChart extends ChartWidget
{
    protected ?string $heading = 'Sebaran Status Pesanan';

    protected ?string $pollingInterval = null;

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
                'label' => 'Jumlah Pesanan',
                'data' => array_values($counts),
                'backgroundColor' => [
                    '#F59E0B',
                    '#10B981',
                    '#3B82F6',
                    '#6366F1',
                    '#0EA5E9',
                    '#22C55E',
                    '#047857',
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
