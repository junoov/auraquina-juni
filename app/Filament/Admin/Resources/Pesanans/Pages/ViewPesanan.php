<?php

namespace App\Filament\Admin\Resources\Pesanans\Pages;

use App\Filament\Admin\Resources\Pesanans\Actions\PesananActions;
use App\Filament\Admin\Resources\Pesanans\PesananResource;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPesanan extends ViewRecord
{
    protected static string $resource = PesananResource::class;

    /**
     * Header actions.
     *
     * Semua tombol workflow di-group ke dalam dropdown "Tindakan" supaya header
     * tidak sesak. Tombol Edit Manual tetap standalone di luar group.
     *
     * Urutan = alur fulfillment natural:
     * Konfirmasi Bayar → Proses → Kemas → Kirim → (Refund/After-Sales/Batal).
     * Filament otomatis sembunyikan action yang gak relevan dengan status.
     */
    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                ...PesananActions::workflow(),
                PesananActions::editAddress(),
            ])
                ->label('Tindakan')
                ->icon('heroicon-o-bolt')
                ->color('primary')
                ->button()
                ->dropdownWidth('lg'),

            EditAction::make()
                ->label('Edit Manual')
                ->color('gray'),
        ];
    }
}
