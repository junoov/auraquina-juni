<?php

namespace App\Filament\Admin\Pages;

use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;

class Dashboard extends BaseDashboard
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?string $navigationLabel = 'Ringkasan Toko';

    protected static ?string $title = 'Ringkasan Toko';

    protected ?string $subheading = 'Lihat pesanan, pembayaran, dan stok yang perlu ditangani hari ini.';

    public function getColumns(): int|array
    {
        return 1;
    }
}
