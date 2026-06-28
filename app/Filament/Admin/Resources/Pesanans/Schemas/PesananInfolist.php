<?php

namespace App\Filament\Admin\Resources\Pesanans\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

/**
 * Infolist read-only untuk halaman "Lihat Pesanan".
 *
 * Filosofi: admin buka pesanan untuk MELIHAT & mengambil keputusan,
 * bukan untuk mengisi form. Semua info penting ditampilkan rapi,
 * urutan dari yang paling sering dipakai.
 *
 * Layout: 2 kolom (mirip Shopify / Shopee Seller Center)
 *  - Baris 1 : Ringkasan Pesanan (kiri) + Pelanggan (kanan)
 *  - Baris 2 : Item Pesanan (full width, produk cards)
 *  - Baris 3 : Pengiriman (kiri) + Pembayaran (kanan)
 *  - Baris 4 : After-Sales (full width, conditional)
 *  - Baris 5 : Riwayat Aktivitas (full width, collapsible)
 */
class PesananInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                // ====== Baris 1: Ringkasan (kiri) + Pelanggan (kanan) ======
                Section::make('Ringkasan Pesanan')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        TextEntry::make('kode_pesanan')
                            ->label('Kode Pesanan')
                            ->weight(FontWeight::Bold)
                            ->copyable()
                            ->copyMessage('Kode pesanan disalin')
                            ->inlineLabel(),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->formatStateUsing(fn ($state): string => PesananForm::statusOptions()[$state] ?? ucfirst(str_replace('_', ' ', (string) $state)))
                            ->color(fn ($state): string => self::statusColor($state))
                            ->inlineLabel(),
                        TextEntry::make('created_at')
                            ->label('Tanggal')
                            ->dateTime('d M Y, H:i')
                            ->inlineLabel(),
                        TextEntry::make('total')
                            ->label('Total')
                            ->weight(FontWeight::Bold)
                            ->money('IDR', divideBy: 1)
                            ->size(\Filament\Support\Enums\TextSize::Large)
                            ->color('primary')
                            ->inlineLabel(),
                    ]),

                Section::make('Pelanggan')
                    ->icon('heroicon-o-user')
                    ->schema([
                        TextEntry::make('nama_penerima')->label('Nama')->inlineLabel(),
                        TextEntry::make('telepon')
                            ->label('Telepon')
                            ->inlineLabel()
                            ->copyable(),
                        TextEntry::make('kota')->label('Kota')->inlineLabel(),
                        TextEntry::make('alamat_lengkap')
                            ->label('Alamat')
                            ->placeholder('-')
                            ->inlineLabel(),
                    ]),

                // ====== Baris 2: Item Pesanan (full width) ======
                Section::make('Item Pesanan')
                    ->columnSpan(2)
                    ->icon('heroicon-o-shopping-bag')
                    ->schema([
                        TextEntry::make('items')
                            ->hiddenLabel()
                            ->state(fn ($record) => $record->items)
                            ->view('filament.admin.infolists.order-items'),
                    ]),

                // ====== Baris 3: Pengiriman (kiri) + Pembayaran (kanan) ======
                Section::make('Pengiriman')
                    ->icon('heroicon-o-truck')
                    ->schema([
                        TextEntry::make('metode_pengiriman')
                            ->label('Layanan')
                            ->placeholder('-')
                            ->inlineLabel(),
                        TextEntry::make('kurir_pengiriman')
                            ->label('Kurir')
                            ->placeholder('-')
                            ->inlineLabel(),
                        TextEntry::make('nomor_resi')
                            ->label('No. Resi')
                            ->placeholder('Belum ada')
                            ->inlineLabel()
                            ->copyable()
                            ->copyMessage('Resi disalin')
                            ->visible(fn ($record): bool => filled($record->nomor_resi)),
                        TextEntry::make('dikirim_pada')
                            ->label('Dikirim')
                            ->dateTime('d M Y, H:i')
                            ->placeholder('-')
                            ->inlineLabel()
                            ->visible(fn ($record): bool => filled($record->dikirim_pada)),
                    ]),

                Section::make('Pembayaran')
                    ->icon('heroicon-o-banknotes')
                    ->schema([
                        TextEntry::make('metode_pembayaran')
                            ->label('Metode')
                            ->placeholder('-')
                            ->inlineLabel(),
                        TextEntry::make('subtotal')
                            ->label('Subtotal')
                            ->money('IDR', divideBy: 1)
                            ->inlineLabel(),
                        TextEntry::make('ongkir')
                            ->label('Ongkir')
                            ->money('IDR', divideBy: 1)
                            ->inlineLabel(),
                        TextEntry::make('diskon')
                            ->label('Diskon')
                            ->money('IDR', divideBy: 1)
                            ->color(fn ($state): string => $state > 0 ? 'success' : 'gray')
                            ->inlineLabel(),
                        TextEntry::make('total')
                            ->label('Total')
                            ->weight(FontWeight::Bold)
                            ->money('IDR', divideBy: 1)
                            ->color('primary')
                            ->inlineLabel(),
                    ]),

                // ====== Baris 4: After-Sales (full width, conditional) ======
                Section::make('After-Sales')
                    ->columnSpan(2)
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->schema([
                        TextEntry::make('after_sales_status')
                            ->label('Status')
                            ->badge()
                            ->placeholder('Tidak ada request')
                            ->formatStateUsing(fn ($state) => $state ? (PesananForm::afterSalesStatusOptions()[$state] ?? ucfirst((string) $state)) : null)
                            ->color(fn ($state): string => match ($state) {
                                'requested' => 'warning',
                                'in_review' => 'info',
                                'resolved' => 'success',
                                'rejected' => 'danger',
                                default => 'gray',
                            })
                            ->inlineLabel(),
                        TextEntry::make('after_sales_type')
                            ->label('Jenis')
                            ->placeholder('-')
                            ->formatStateUsing(fn ($state) => $state ? (PesananForm::afterSalesTypeOptions()[$state] ?? $state) : null)
                            ->inlineLabel(),
                        TextEntry::make('after_sales_reason')
                            ->label('Catatan')
                            ->placeholder('-')
                            ->inlineLabel(),
                        TextEntry::make('after_sales_requested_at')
                            ->label('Diminta')
                            ->dateTime('d M Y, H:i')
                            ->placeholder('-')
                            ->inlineLabel(),
                    ])
                    ->visible(fn ($record): bool => filled($record?->after_sales_status)),

                // ====== Baris 5: Riwayat Aktivitas (full width, collapsible) ======
                Section::make('Riwayat Aktivitas')
                    ->columnSpan(2)
                    ->icon('heroicon-o-clock')
                    ->collapsible()
                    ->schema([
                        TextEntry::make('activity_log')
                            ->hiddenLabel()
                            ->state(fn ($record) => self::activityLog($record))
                            ->placeholder('Belum ada aktivitas tercatat.')
                            ->view('filament.admin.infolists.activity-log'),
                    ]),
            ]);
    }

    /**
     * Ambil aktivitas pesanan dari spatie/activitylog.
     */
    public static function activityLog($record)
    {
        return $record->activities()
            ->latest()
            ->limit(20)
            ->get();
    }

    public static function statusColor(string $state): string
    {
        return match ($state) {
            'pending_payment' => 'warning',
            'paid' => 'info',
            'processing', 'packed' => 'primary',
            'shipped' => 'info',
            'delivered', 'completed' => 'success',
            'cancelled', 'expired' => 'danger',
            'return_requested' => 'warning',
            'refunded' => 'gray',
            default => 'gray',
        };
    }
}
