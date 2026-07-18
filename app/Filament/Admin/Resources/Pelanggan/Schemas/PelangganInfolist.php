<?php

namespace App\Filament\Admin\Resources\Pelanggan\Schemas;

use App\Models\Pesanan;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class PelangganInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(12)
            ->components([
                Section::make('Profil Pelanggan')
                    ->icon('heroicon-o-user')
                    ->columnSpan(6)
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nama Lengkap')
                            ->weight(FontWeight::Bold)
                            ->inlineLabel(),
                        TextEntry::make('email')
                            ->label('Alamat Email')
                            ->copyable()
                            ->inlineLabel(),
                        TextEntry::make('phone')
                            ->label('No. Telepon')
                            ->placeholder('—')
                            ->inlineLabel(),
                        TextEntry::make('created_at')
                            ->label('Bergabung Sejak')
                            ->dateTime('d M Y, H:i')
                            ->inlineLabel(),
                    ]),

                Section::make('Statistik Belanja')
                    ->icon('heroicon-o-presentation-chart-bar')
                    ->columnSpan(6)
                    ->schema([
                        TextEntry::make('total_pesanan')
                            ->label('Jumlah Pesanan')
                            ->state(fn ($record) => $record->pesanans()->count() . ' kali')
                            ->weight(FontWeight::Bold)
                            ->inlineLabel(),
                        TextEntry::make('total_belanja')
                            ->label('Total Pengeluaran')
                            ->state(fn ($record) => 'Rp ' . number_format(
                                $record->pesanans()
                                    ->whereIn('status', [
                                        Pesanan::STATUS_PAID,
                                        Pesanan::STATUS_PROCESSING,
                                        Pesanan::STATUS_PACKED,
                                        Pesanan::STATUS_SHIPPED,
                                        Pesanan::STATUS_DELIVERED,
                                        Pesanan::STATUS_COMPLETED
                                    ])
                                    ->sum('total'),
                                0, ',', '.'
                            ))
                            ->weight(FontWeight::Bold)
                            ->inlineLabel(),
                    ]),

                Section::make('Riwayat Transaksi')
                    ->icon('heroicon-o-shopping-bag')
                    ->columnSpan(12)
                    ->schema([
                        TextEntry::make('pesanans')
                            ->hiddenLabel()
                            ->state(fn ($record) => $record->pesanans()->latest()->get())
                            ->view('filament.admin.infolists.customer-orders'),
                    ]),
            ]);
    }
}
