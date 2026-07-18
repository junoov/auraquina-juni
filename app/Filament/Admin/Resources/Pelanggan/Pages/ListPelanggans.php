<?php

namespace App\Filament\Admin\Resources\Pelanggan\Pages;

use App\Filament\Admin\Resources\PelangganResource;
use Filament\Resources\Pages\ListRecords;

class ListPelanggans extends ListRecords
{
    protected static string $resource = PelangganResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
