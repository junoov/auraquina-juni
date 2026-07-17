<?php

namespace App\Filament\Admin\Resources\Produks\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VariansRelationManager extends RelationManager
{
    protected static string $relationship = 'varians';

    protected static ?string $title = 'Kelola Varian Produk';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // ── Semua info varian dalam 1 section compact ──
                Section::make()
                    ->schema([
                        // ── Baris 1: Warna + Ukuran + Kode Warna (3 kolom) ──
                        Grid::make(3)->schema([
                            TextInput::make('warna')
                                ->label('Warna')
                                ->placeholder('Hitam, Navy, Sage...')
                                ->required()
                                ->maxLength(100),
                            TextInput::make('ukuran')
                                ->label('Ukuran')
                                ->placeholder('S, M, L, XL...')
                                ->required()
                                ->maxLength(50),
                            ColorPicker::make('kode_warna')
                                ->label('Warna Swatch')
                                ->helperText('Pengganti foto jika belum ada gambar.'),
                        ]),

                        // ── Baris 2: Kode Varian + Stok + Selisih Harga (3 kolom) ──
                        Grid::make(3)->schema([
                            TextInput::make('sku')
                                ->label('Kode Varian')
                                ->placeholder('GAMIS-HITAM-M')
                                ->helperText('Kode unik untuk varian ini.')
                                ->required()
                                ->unique(ignoreRecord: true),
                            TextInput::make('stok')
                                ->label('Stok')
                                ->helperText('Sisa stok varian ini.')
                                ->numeric()
                                ->minValue(0)
                                ->default(0)
                                ->required(),
                            TextInput::make('penyesuaian_harga')
                                ->label('Selisih Harga')
                                ->helperText('Tambahan/kurangan dari harga utama. 0 = sama.')
                                ->numeric()
                                ->default(0)
                                ->prefix('Rp'),
                        ]),

                        // ── Foto Varian: compact horizontal repeater ──
                        Section::make('Foto Varian')
                            ->icon('heroicon-o-camera')
                            ->description('Foto yang tampil saat pembeli memilih warna.')
                            ->collapsed(fn ($record) => $record && $record->gambarVarians()->count() === 0)
                            ->collapsible()
                            ->schema([
                                Repeater::make('gambarVarians')
                                    ->relationship()
                                    ->hiddenLabel()
                                    ->addActionLabel('+ Tambah Foto')
                                    ->reorderableWithButtons()
                                    ->grid(2)
                                    ->schema([
                                        FileUpload::make('url')
                                            ->hiddenLabel()
                                            ->image()
                                            ->disk('r2')
                                            ->directory('produk/varian')
                                            ->imageEditor()
                                            ->maxSize(5120)
                                            ->required()
                                            ->columnSpanFull(),
                                        TextInput::make('alt')
                                            ->label('Keterangan')
                                            ->placeholder('Gamis Hitam Depan')
                                            ->maxLength(255),
                                        Toggle::make('utama')
                                            ->label('Foto Utama')
                                            ->helperText('Jadi ikon warna di toko.')
                                            ->default(false)
                                            ->inline(false)
                                            ->columnSpan(1),
                                        TextInput::make('urutan')
                                            ->label('Urutan')
                                            ->numeric()
                                            ->default(0)
                                            ->required()
                                            ->columnSpan(1),
                                    ])
                                    ->itemLabel(fn (array $state): ?string => $state['utama'] ? 'Foto utama' : 'Foto')
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('gambarVarianUtama.url')
                    ->label('Foto')
                    ->disk('r2')
                    ->circular()
                    ->size(40)
                    ->defaultImageUrl(function ($record) {
                        if ($record && $record->kode_warna) {
                            $encoded = urlencode($record->kode_warna);

                            return "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='40' height='40'><rect width='40' height='40' rx='8' fill='{$encoded}'/></svg>";
                        }

                        return null;
                    }),
                TextColumn::make('warna')
                    ->label('Warna')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('ukuran')
                    ->label('Ukuran')
                    ->searchable()
                    ->badge()
                    ->color('gray'),
                TextColumn::make('sku')
                    ->label('Kode Varian')
                    ->copyable()
                    ->tooltip('Klik untuk menyalin kode'),
                TextColumn::make('stok')
                    ->label('Stok')
                    ->numeric()
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state <= 0 => 'danger',
                        $state < 5 => 'warning',
                        default => 'success',
                    })
                    ->tooltip(fn ($state) => $state <= 0
                        ? 'Stok habis'
                        : ($state < 5 ? "Sisa {$state} unit" : "Stok aman: {$state} unit")),
                TextColumn::make('penyesuaian_harga')
                    ->label('Selisih Harga')
                    ->money('IDR', divideBy: 1)
                    ->tooltip('Selisih dari harga produk utama'),
            ])
            ->defaultSort('warna')
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Varian')
                    ->slideOver()
                    ->modalWidth('7xl'),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Ubah')
                    ->slideOver()
                    ->modalWidth('7xl'),
                DeleteAction::make()->label('Hapus'),
            ]);
    }
}
