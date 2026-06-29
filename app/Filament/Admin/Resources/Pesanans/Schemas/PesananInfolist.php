<?php

namespace App\Filament\Admin\Resources\Pesanans\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

/**
 * Infolist read-only untuk halaman "Lihat Pesanan".
 *
 * Layout 2 kolom yang clean terinspirasi Shopify Admin Order Detail:
 *  - Baris 1 : Ringkasan Pesanan (kiri, lebih lebar) + Status (kanan, compact)
 *  - Baris 2 : Pelanggan (kiri) + Pengiriman (kanan)
 *  - Baris 3 : Item Pesanan (full width, produk cards)
 *  - Baris 4 : Pembayaran (full width, ringkasan financial)
 *  - Baris 5 : After-Sales (full width, conditional)
 *  - Baris 6 : Riwayat Aktivitas (full width, collapsible)
 */
class PesananInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(12)
            ->components([

                // ====== Baris 1: Ringkasan Pesanan (8 kolom) + Status (4 kolom) ======
                Section::make('Ringkasan Pesanan')
                    ->icon('heroicon-o-document-text')
                    ->columnSpan(8)
                    ->schema([
                        TextEntry::make('kode_pesanan')
                            ->label('Kode Pesanan')
                            ->weight(FontWeight::Bold)
                            ->copyable()
                            ->copyMessage('Kode pesanan disalin')
                            ->inlineLabel(),
                        TextEntry::make('created_at')
                            ->label('Tanggal Pesanan')
                            ->dateTime('d M Y, H:i')
                            ->inlineLabel(),
                    ]),

                Section::make('Status')
                    ->icon('heroicon-o-flag')
                    ->columnSpan(4)
                    ->schema([
                        TextEntry::make('status')
                            ->hiddenLabel()
                            ->badge()
                            ->size(\Filament\Support\Enums\TextSize::Large)
                            ->formatStateUsing(fn ($state): string => PesananForm::statusOptions()[$state] ?? ucfirst(str_replace('_', ' ', (string) $state)))
                            ->color(fn ($state): string => self::statusColor($state)),
                        TextEntry::make('total')
                            ->hiddenLabel()
                            ->weight(FontWeight::Bold)
                            ->money('IDR', divideBy: 1)
                            ->size(\Filament\Support\Enums\TextSize::Large)
                            ->color('primary'),
                    ]),

                // ====== Baris 2: Pelanggan (7 kolom) + Pengiriman (5 kolom) ======
                Section::make('Pelanggan')
                    ->icon('heroicon-o-user')
                    ->columnSpan(7)
                    ->schema([
                        TextEntry::make('nama_penerima')->label('Nama')->inlineLabel(),
                        TextEntry::make('telepon')
                            ->label('Telepon')
                            ->inlineLabel()
                            ->copyable(),
                        TextEntry::make('alamat_lengkap')
                            ->label('Alamat')
                            ->placeholder('-')
                            ->columnSpanFull()
                            ->inlineLabel(),
                    ]),

                Section::make('Pengiriman')
                    ->icon('heroicon-o-truck')
                    ->columnSpan(5)
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
                        TextEntry::make('kota')
                            ->label('Kota')
                            ->placeholder('-')
                            ->inlineLabel(),
                    ]),

                // ====== Baris 3: Item Pesanan (full width) ======
                Section::make('Item Pesanan')
                    ->columnSpan(12)
                    ->icon('heroicon-o-shopping-bag')
                    ->collapsible()
                    ->schema([
                        TextEntry::make('items')
                            ->hiddenLabel()
                            ->state(fn ($record) => $record->items)
                            ->view('filament.admin.infolists.order-items'),
                    ]),

                // ====== Baris 4a: Pembayaran — Menunggu Pembayaran (pending_payment) ======
                Section::make('Pembayaran')
                    ->columnSpan(12)
                    ->icon('heroicon-o-clock')
                    ->description('Pesanan menunggu pembayaran dari pelanggan.')
                    ->schema([
                        TextEntry::make('metode_pembayaran')
                            ->label('Metode')
                            ->placeholder('-')
                            ->inlineLabel(),
                        TextEntry::make('batas_bayar')
                            ->label('Batas Bayar')
                            ->dateTime('d M Y, H:i')
                            ->placeholder('-')
                            ->color('warning')
                            ->icon('heroicon-o-exclamation-triangle')
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
                    ])
                    ->visible(fn ($record): bool => $record->status === 'pending_payment'),

                // ====== Baris 4b: Pembayaran — Sudah Terverifikasi (paid+) ======
                Section::make('Verifikasi Pembayaran')
                    ->columnSpan(12)
                    ->icon('heroicon-o-banknotes')
                    ->description('Pembayaran telah diterima dan terverifikasi.')
                    ->columns(3)
                    ->schema([
                        // Kolom kiri: Status & Metode
                        Section::make('Status Transaksi')
                            ->columnSpan(1)
                            ->compact()
                            ->schema([
                                TextEntry::make('midtrans_status')
                                    ->label('Status Midtrans')
                                    ->badge()
                                    ->placeholder('-')
                                    ->formatStateUsing(fn ($state): string => self::midtransStatusLabel($state))
                                    ->color(fn ($state): string => self::midtransStatusColor($state))
                                    ->inlineLabel(),
                                TextEntry::make('metode_pembayaran')
                                    ->label('Metode')
                                    ->placeholder('-')
                                    ->inlineLabel(),
                                TextEntry::make('midtrans_payment_type')
                                    ->label('Jenis Bayar')
                                    ->placeholder('-')
                                    ->state(fn ($record): ?string => self::extractMidtransField($record, 'payment_type'))
                                    ->formatStateUsing(fn ($state): string => self::paymentTypeLabel($state))
                                    ->inlineLabel(),
                            ]),

                        // Kolom tengah: Detail Transaksi
                        Section::make('Detail Transaksi')
                            ->columnSpan(1)
                            ->compact()
                            ->schema([
                                TextEntry::make('midtrans_transaction_id')
                                    ->label('ID Transaksi')
                                    ->placeholder('-')
                                    ->state(fn ($record): ?string => self::extractMidtransField($record, 'transaction_id'))
                                    ->copyable()
                                    ->copyMessage('ID Transaksi disalin')
                                    ->inlineLabel(),
                                TextEntry::make('midtrans_transaction_time')
                                    ->label('Waktu Transaksi')
                                    ->placeholder('-')
                                    ->state(fn ($record): ?string => self::extractMidtransField($record, 'transaction_time'))
                                    ->formatStateUsing(fn ($state): ?string => $state ? \Carbon\Carbon::parse($state)->format('d M Y, H:i') : null)
                                    ->inlineLabel(),
                                TextEntry::make('dibayar_pada')
                                    ->label('Dibayar Pada')
                                    ->dateTime('d M Y, H:i')
                                    ->placeholder('-')
                                    ->color('success')
                                    ->icon('heroicon-o-check-circle')
                                    ->inlineLabel(),
                            ]),

                        // Kolom kanan: Bank/VA (conditional)
                        Section::make('Detail Bank')
                            ->columnSpan(1)
                            ->compact()
                            ->schema([
                                TextEntry::make('midtrans_bank')
                                    ->label('Bank')
                                    ->placeholder('-')
                                    ->state(fn ($record): ?string => self::extractMidtransField($record, 'bank'))
                                    ->inlineLabel(),
                                TextEntry::make('midtrans_va_number')
                                    ->label('Nomor VA')
                                    ->placeholder('-')
                                    ->state(fn ($record): ?string => self::extractMidtransField($record, 'va_number'))
                                    ->copyable()
                                    ->copyMessage('Nomor VA disalin')
                                    ->inlineLabel(),
                                TextEntry::make('midtrans_settlement_time')
                                    ->label('Waktu Settlement')
                                    ->placeholder('-')
                                    ->state(fn ($record): ?string => self::extractMidtransField($record, 'settlement_time'))
                                    ->formatStateUsing(fn ($state): ?string => $state ? \Carbon\Carbon::parse($state)->format('d M Y, H:i') : null)
                                    ->inlineLabel(),
                            ])
                            ->visible(fn ($record): bool => self::extractMidtransField($record, 'bank') || self::extractMidtransField($record, 'va_number')),
                    ])
                    ->visible(fn ($record): bool => $record->status !== 'pending_payment'),

                // ====== Baris 4c: Ringkasan Finansial (selalu tampil) ======
                Section::make('Ringkasan Finansial')
                    ->columnSpan(12)
                    ->icon('heroicon-o-receipt-percent')
                    ->compact()
                    ->columns(4)
                    ->schema([
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

                // ====== Baris 5: After-Sales (full width, conditional) ======
                Section::make('After-Sales')
                    ->columnSpan(12)
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

                // ====== Baris 6: Riwayat Aktivitas (full width, collapsible) ======
                Section::make('Riwayat Aktivitas')
                    ->columnSpan(12)
                    ->icon('heroicon-o-clock')
                    ->collapsible()
                    ->collapsed()
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

    /**
     * Extract field dari midtrans_raw_response JSON.
     */
    public static function extractMidtransField($record, string $field): ?string
    {
        if (! $record?->midtrans_raw_response) {
            return null;
        }

        $data = is_string($record->midtrans_raw_response)
            ? json_decode($record->midtrans_raw_response, true)
            : $record->midtrans_raw_response;

        if (! is_array($data)) {
            return null;
        }

        // Handle nested: va_numbers[0].va_number
        if ($field === 'va_number' && isset($data['va_numbers']) && is_array($data['va_numbers']) && count($data['va_numbers']) > 0) {
            return $data['va_numbers'][0]['va_number'] ?? null;
        }

        return $data[$field] ?? null;
    }

    /**
     * Label yang human-readable untuk midtrans_status.
     */
    public static function midtransStatusLabel(?string $state): string
    {
        return match ($state) {
            'pending' => 'Pending',
            'capture' => 'Capture',
            'settlement' => 'Settlement',
            'deny' => 'Ditolak',
            'cancel' => 'Dibatalkan',
            'expire' => 'Kedaluwarsa',
            'refund' => 'Refund',
            'partial_refund' => 'Refund Sebagian',
            'chargeback' => 'Chargeback',
            'partial_chargeback' => 'Chargeback Sebagian',
            default => ucfirst((string) ($state ?? '-')),
        };
    }

    /**
     * Warna badge untuk midtrans_status.
     */
    public static function midtransStatusColor(?string $state): string
    {
        return match ($state) {
            'capture', 'settlement' => 'success',
            'pending' => 'warning',
            'deny', 'cancel', 'expire' => 'danger',
            'refund', 'partial_refund' => 'gray',
            'chargeback', 'partial_chargeback' => 'warning',
            default => 'gray',
        };
    }

    /**
     * Label yang human-readable untuk payment_type dari Midtrans.
     */
    public static function paymentTypeLabel(?string $state): string
    {
        return match ($state) {
            'gopay' => 'GoPay',
            'shopeepay' => 'ShopeePay',
            'qris' => 'QRIS',
            'bank_transfer' => 'Transfer Bank',
            'echannel' => 'Mandiri Bill',
            'bca_klikpay' => 'BCA KlikPay',
            'bca_va' => 'Virtual Account BCA',
            'bni_va' => 'Virtual Account BNI',
            'bri_va' => 'Virtual Account BRI',
            'permata_va' => 'Virtual Account Permata',
            'cimb_va' => 'Virtual Account CIMB',
            'other_va' => 'Virtual Account Lainnya',
            'gci' => 'GCI',
            'indomaret' => 'Indomaret',
            'alfamart' => 'Alfamart',
            default => ucfirst(str_replace('_', ' ', (string) ($state ?? '-'))),
        };
    }
}
