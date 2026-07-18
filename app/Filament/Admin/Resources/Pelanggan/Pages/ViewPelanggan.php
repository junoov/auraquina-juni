<?php

namespace App\Filament\Admin\Resources\Pelanggan\Pages;

use App\Filament\Admin\Resources\PelangganResource;
use Filament\Resources\Pages\ViewRecord;

class ViewPelanggan extends ViewRecord
{
    protected static string $resource = PelangganResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
