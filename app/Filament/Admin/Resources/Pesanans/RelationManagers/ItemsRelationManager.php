<?php

namespace App\Filament\Admin\Resources\Pesanans\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Item Pesanan';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('gambar_url')->label('Gambar')->square()->size(48),
                TextColumn::make('nama_produk')->label('Nama Produk')->wrap()->searchable(),
                TextColumn::make('varian_label')->label('Varian'),
                TextColumn::make('harga')->label('Harga')->money('IDR', divideBy: 1),
                TextColumn::make('jumlah')->label('Jumlah'),
                TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->state(fn ($record) => $record->harga * $record->jumlah)
                    ->money('IDR', divideBy: 1),
            ])
            ->paginated(false);
    }
}
