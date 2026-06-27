<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Pesanan;
use Filament\Widgets\ChartWidget;

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

        return [
            'datasets' => [[
                'label' => 'Jumlah Pesanan',
                'data' => collect(array_keys($labels))
                    ->map(fn (string $status) => Pesanan::where('status', $status)->count())
                    ->all(),
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
