<?php

namespace App\Filament\Admin\Resources\Produks\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProdukForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Utama')
                    ->columns(2)
                    ->schema([
                        Select::make('kategori_id')
                            ->label('Kategori')
                            ->relationship('kategori', 'nama')
                            ->preload()
                            ->searchable()
                            ->required(),
                        TextInput::make('sku')->label('SKU')->required()->unique(ignoreRecord: true),
                        TextInput::make('nama')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug((string) $state)))
                            ->columnSpanFull(),
                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->columnSpanFull(),
                        Textarea::make('deskripsi_singkat')->label('Deskripsi Singkat')->maxLength(500)->columnSpanFull(),
                        Textarea::make('deskripsi')->label('Deskripsi')->columnSpanFull()->rows(6),
                    ]),

                Section::make('Harga Produk')
                    ->columns(3)
                    ->schema([
                        TextInput::make('harga')
                            ->label('Harga Jual')
                            ->helperText('Harga yang dibayar pembeli.')
                            ->required()
                            ->numeric()
                            ->prefix('Rp'),
                        TextInput::make('harga_coret')
                            ->label('Harga Sebelum Diskon')
                            ->helperText('Opsional. Isi hanya kalau ingin menampilkan harga lama yang dicoret.')
                            ->numeric()
                            ->prefix('Rp')
                            ->nullable(),
                        TextInput::make('berat')
                            ->label('Berat Produk')
                            ->helperText('Dipakai untuk hitung ongkir.')
                            ->required()
                            ->numeric()
                            ->suffix('gram')
                            ->default(0),
                    ]),

                Section::make('Detail Produk')
                    ->columns(2)
                    ->schema([
                        TextInput::make('bahan')->label('Bahan'),
                        Select::make('badge')->label('Label Produk')->options([
                            'baru' => 'Baru',
                            'terlaris' => 'Terlaris',
                            'terbatas' => 'Terbatas',
                            'preorder' => 'Pra-pesan',
                        ])->nullable(),
                        Textarea::make('perawatan')->label('Perawatan')->columnSpanFull(),
                        Textarea::make('info_model')->label('Info Model')->columnSpanFull(),
                    ]),

                Section::make('Status & Tampilan')
                    ->columns(3)
                    ->schema([
                        Toggle::make('aktif')->label('Aktif')->default(true)->required(),
                        Toggle::make('unggulan')->label('Unggulan'),
                        TextInput::make('urutan')->label('Urutan')->numeric()->default(0)->required(),
                    ]),
            ]);
    }
}
