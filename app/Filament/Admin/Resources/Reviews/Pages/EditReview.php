<?php

namespace App\Filament\Admin\Resources\Reviews\Pages;

use App\Filament\Admin\Resources\Reviews\ReviewResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditReview extends EditRecord
{
    protected static string $resource = ReviewResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return [
            'status' => $data['status'],
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
