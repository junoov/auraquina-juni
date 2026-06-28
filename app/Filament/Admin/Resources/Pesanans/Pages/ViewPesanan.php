<?php

namespace App\Filament\Admin\Resources\Pesanans\Pages;

use App\Filament\Admin\Resources\Pesanans\Actions\PesananActions;
use App\Filament\Admin\Resources\Pesanans\PesananResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPesanan extends ViewRecord
{
    protected static string $resource = PesananResource::class;

    /**
     * Header actions = semua tombol workflow + edit.
     * Inilah alasan utama redesign: sebelumnya tombol-tombol ini HANYA
     * ada di list table. Begitu admin masuk halaman pesanan, mereka hilang.
     * Sekarang mereka ada di setiap pesanan yang dibuka.
     *
     * Urutan = alur fulfillment natural:
     * Konfirmasi Bayar → Proses → Kemas → Kirim → (Refund/After-Sales/Batal).
     * Filament otomatis sembunyikan action yang gak relevan dengan status.
     */
    protected function getHeaderActions(): array
    {
        return [
            ...PesananActions::workflow(),
            PesananActions::editAddress(),
            EditAction::make()
                ->label('Edit Manual')
                ->color('gray'),
        ];
    }
}
