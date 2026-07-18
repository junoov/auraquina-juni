<?php

namespace App\Filament\Admin\Resources\Pelanggan\Pages;

use App\Filament\Admin\Resources\PelangganResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPelanggan extends EditRecord
{
    protected static string $resource = PelangganResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
