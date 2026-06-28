<?php

namespace App\Filament\Admin\Resources\Pesanans\Pages;

use App\Filament\Admin\Resources\Pesanans\PesananResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditPesanan extends EditRecord
{
    protected static string $resource = PesananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cetakInvoice')
                ->label('Cetak Invoice')
                ->icon('heroicon-o-document-text')
                ->url(fn ($record) => $record->signedInvoiceUrl())
                ->openUrlInNewTab(),
        ];
    }
}
