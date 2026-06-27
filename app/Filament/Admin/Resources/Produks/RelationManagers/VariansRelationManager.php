<?php

namespace App\Filament\Admin\Resources\Produks\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VariansRelationManager extends RelationManager
{
    protected static string $relationship = 'varians';

    protected static ?string $title = 'Varian';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('ukuran')->label('Ukuran')->required()->maxLength(50),
            TextInput::make('warna')->label('Warna')->required()->maxLength(100),
            ColorPicker::make('kode_warna')->label('Kode Warna (hex)'),
            TextInput::make('sku')->required()->unique(ignoreRecord: true),
            TextInput::make('stok')->label('Stok')->numeric()->default(0)->required(),
            TextInput::make('penyesuaian_harga')
                ->label('Penyesuaian Harga')
                ->numeric()
                ->default(0)
                ->prefix('Rp')
                ->helperText('+/- terhadap harga produk utama'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('warna')->label('Warna')->searchable(),
                TextColumn::make('ukuran')->label('Ukuran')->searchable(),
                TextColumn::make('sku')->label('SKU')->copyable(),
                TextColumn::make('stok')
                    ->label('Stok')
                    ->numeric()
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state <= 0 => 'danger',
                        $state < 5 => 'warning',
                        default => 'success',
                    }),
                TextColumn::make('penyesuaian_harga')->label('Penyesuaian Harga')->money('IDR', divideBy: 1),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
