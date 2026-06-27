<?php

namespace App\Filament\Admin\Resources\Halamans\Pages;

use App\Filament\Admin\Resources\Halamans\HalamanResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHalaman extends EditRecord
{
    protected static string $resource = HalamanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
