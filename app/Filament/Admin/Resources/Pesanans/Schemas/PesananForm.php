<?php

namespace App\Filament\Admin\Resources\Pesanans\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * Form EDIT (sekarang jadi halaman sekunder, bukan default).
 *
 * Filosofi setelah redesign:
 * - Default "buka pesanan" = ViewRecord + Infolist (lihat PesananInfolist)
 * - Halaman ini hanya untuk admin yang BENAR-BENAR mau edit field manual.
 *
 * Yang bisa diedit di sini hanya field yang legitimate untuk diubah manual:
 *   status, nomor_resi, kurir, dan field bantuan pelanggan.
 *
 * Field pelanggan & finansial (nama, telepon, alamat, total, dll) SENGAJA
 * tidak dimasukkan karena read-only by design (integritas data / bukti transaksi).
 * Untuk koreksi alamat sebelum dikirim, gunakan action "Ubah Alamat" yang
 * terkontrol + tercatat di activitylog.
 */
class PesananForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Status & Pengiriman')
                ->description('Ubah status pesanan atau input nomor resi manual.')
                ->columns(2)
                ->schema([
                    Select::make('status')
                        ->label('Status')
                        ->options(self::statusOptions())
                        ->required()
                        ->helperText('Hanya ubah jika yakin. Transisi normal sebaiknya via tombol workflow di halaman lihat.'),
                    TextInput::make('nomor_resi')
                        ->label('Nomor Resi')
                        ->placeholder('Otomatis terisi saat klik tombol "Kirim"'),
                    TextInput::make('kurir_pengiriman')
                        ->label('Kurir')
                        ->placeholder('JNE / J&T / SiCepat / AnterAja'),
                ]),

            Section::make('Bantuan pelanggan')
                ->description('Penanganan retur, pengembalian dana, atau komplain pelanggan.')
                ->columns(2)
                ->schema([
                    Select::make('after_sales_status')
                        ->label('Status bantuan pelanggan')
                        ->options(self::afterSalesStatusOptions()),
                    Select::make('after_sales_type')
                        ->label('Jenis bantuan')
                        ->options(self::afterSalesTypeOptions()),
                    DateTimePicker::make('after_sales_requested_at')->label('Diminta Pada'),
                    DateTimePicker::make('after_sales_resolved_at')->label('Diselesaikan Pada'),
                    Textarea::make('after_sales_reason')
                        ->label('Alasan / Catatan')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function afterSalesStatusOptions(): array
    {
        return [
            'requested' => 'Diminta pelanggan',
            'in_review' => 'Sedang ditinjau',
            'resolved' => 'Selesai ditangani',
            'rejected' => 'Ditolak',
        ];
    }

    public static function afterSalesTypeOptions(): array
    {
        return [
            'return' => 'Penukaran atau retur',
            'refund' => 'Pengembalian dana',
            'issue' => 'Komplain Pesanan',
        ];
    }

    public static function statusOptions(): array
    {
        return [
            'pending_payment' => 'Menunggu bayar',
            'paid' => 'Dibayar',
            'processing' => 'Diproses',
            'packed' => 'Dikemas',
            'shipped' => 'Dikirim',
            'delivered' => 'Diterima',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            'expired' => 'Kedaluwarsa',
            'return_requested' => 'Permintaan Retur',
            'refunded' => 'Dana Dikembalikan',
        ];
    }
}
