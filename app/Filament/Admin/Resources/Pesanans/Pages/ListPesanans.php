<?php

namespace App\Filament\Admin\Resources\Pesanans\Pages;

use App\Filament\Admin\Resources\Pesanans\PesananResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPesanans extends ListRecords
{
    protected static string $resource = PesananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
