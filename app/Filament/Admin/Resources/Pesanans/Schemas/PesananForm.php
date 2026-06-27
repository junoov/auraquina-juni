<?php

namespace App\Filament\Admin\Resources\Pesanans\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PesananForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Pesanan')
                ->columns(2)
                ->schema([
                    TextInput::make('kode_pesanan')
                        ->label('Kode Pesanan')
                        ->disabled()
                        ->dehydrated(false),
                    Select::make('status')
                        ->label('Status')
                        ->options(self::statusOptions())
                        ->disabled()
                        ->dehydrated(false)
                        ->required(),
                    DateTimePicker::make('batas_bayar')->label('Batas Bayar'),
                    DateTimePicker::make('dibayar_pada')->label('Dibayar Pada'),
                ]),
            Section::make('Penerima')
                ->columns(2)
                ->schema([
                    TextInput::make('nama_penerima')->label('Nama Penerima')->required(),
                    TextInput::make('telepon')->label('Telepon')->required(),
                    TextInput::make('kota')->label('Kota'),
                    Textarea::make('alamat_lengkap')->label('Alamat Lengkap')->columnSpanFull(),
                ]),
            Section::make('Pengiriman & Pembayaran')
                ->columns(2)
                ->schema([
                    TextInput::make('metode_pengiriman')->label('Metode Pengiriman'),
                    TextInput::make('kurir_pengiriman')->label('Kurir')->disabled()->dehydrated(false),
                    TextInput::make('nomor_resi')->label('Nomor Resi')->disabled()->dehydrated(false),
                    DateTimePicker::make('dikirim_pada')->label('Dikirim Pada')->disabled()->dehydrated(false),
                    TextInput::make('metode_pembayaran')->label('Metode Pembayaran'),
                ]),
            Section::make('Nominal')
                ->columns(4)
                ->schema([
                    TextInput::make('subtotal')->label('Subtotal')->numeric()->prefix('Rp')->disabled(),
                    TextInput::make('ongkir')->label('Ongkir')->numeric()->prefix('Rp'),
                    TextInput::make('diskon')->label('Diskon')->numeric()->prefix('Rp'),
                    TextInput::make('total')->label('Total')->numeric()->prefix('Rp')->disabled(),
                ]),
            Section::make('After-Sales')
                ->columns(2)
                ->schema([
                    Select::make('after_sales_status')
                        ->label('Status After-Sales')
                        ->options(self::afterSalesStatusOptions()),
                    Select::make('after_sales_type')
                        ->label('Jenis Request')
                        ->options(self::afterSalesTypeOptions()),
                    DateTimePicker::make('after_sales_requested_at')->label('Diminta Pada'),
                    DateTimePicker::make('after_sales_resolved_at')->label('Diselesaikan Pada'),
                    Textarea::make('after_sales_reason')->label('Alasan / Catatan Customer')->columnSpanFull(),
                ]),
        ]);
    }

    public static function afterSalesStatusOptions(): array
    {
        return [
            'requested' => 'Requested',
            'in_review' => 'In Review',
            'resolved' => 'Resolved',
            'rejected' => 'Rejected',
        ];
    }

    public static function afterSalesTypeOptions(): array
    {
        return [
            'return' => 'Penukaran / Return',
            'refund' => 'Refund',
            'issue' => 'Komplain Pesanan',
        ];
    }

    public static function statusOptions(): array
    {
        return [
            'pending_payment' => 'Menunggu Pembayaran',
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
