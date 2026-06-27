<?php

namespace App\Filament\Admin\Resources\Pesanans\Pages;

use App\Filament\Admin\Resources\Pesanans\PesananResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPesanan extends EditRecord
{
    protected static string $resource = PesananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
