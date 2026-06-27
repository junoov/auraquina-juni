<?php

namespace App\Filament\Admin\Resources\Halamans\Pages;

use App\Filament\Admin\Resources\Halamans\HalamanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHalamans extends ListRecords
{
    protected static string $resource = HalamanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
