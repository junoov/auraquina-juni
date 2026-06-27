<?php

namespace App\Filament\Admin\Resources\Vouchers\Schemas;

use App\Models\Voucher;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class VoucherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Voucher')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Voucher')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('code')
                            ->label('Kode Voucher')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->formatStateUsing(fn ($state) => $state ? Str::upper((string) $state) : $state)
                            ->dehydrateStateUsing(fn ($state) => Str::upper(trim((string) $state))),
                        Select::make('type')
                            ->label('Tipe')
                            ->options([
                                Voucher::TYPE_PERCENT => 'Persentase',
                                Voucher::TYPE_FIXED => 'Potongan Tetap',
                                Voucher::TYPE_FREE_SHIPPING => 'Gratis Ongkir',
                            ])
                            ->required(),
                        TextInput::make('value')
                            ->label('Nilai')
                            ->numeric()
                            ->required()
                            ->default(0),
                    ]),
                Section::make('Aturan Pemakaian')
                    ->columns(3)
                    ->schema([
                        TextInput::make('min_subtotal')
                            ->label('Minimal Belanja')
                            ->numeric()
                            ->default(0)
                            ->prefix('Rp'),
                        TextInput::make('max_discount')
                            ->label('Maksimal Diskon')
                            ->numeric()
                            ->nullable()
                            ->prefix('Rp'),
                        TextInput::make('usage_limit')
                            ->label('Batas Pemakaian')
                            ->numeric()
                            ->nullable(),
                        TextInput::make('used_count')
                            ->label('Sudah Dipakai')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated(false),
                        Toggle::make('active')
                            ->label('Aktif')
                            ->default(true)
                            ->required(),
                    ]),
                Section::make('Periode Aktif')
                    ->columns(2)
                    ->schema([
                        DateTimePicker::make('starts_at')->label('Mulai Pada')->seconds(false),
                        DateTimePicker::make('ends_at')->label('Berakhir Pada')->seconds(false),
                    ]),
            ]);
    }
}
