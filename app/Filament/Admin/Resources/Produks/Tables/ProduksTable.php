<?php

namespace App\Filament\Admin\Resources\Produks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\PaginationMode;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ProduksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->paginationMode(PaginationMode::Simple)
            ->columns([
                ImageColumn::make('gambarUtama.url')
                    ->label('')
                    ->square()
                    ->size(48),
                TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('kategori.nama')
                    ->label('Kategori')
                    ->sortable()
                    ->badge(),
                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('harga')
                    ->label('Harga')
                    ->money('IDR', divideBy: 1)
                    ->sortable(),
                TextColumn::make('varians_count')
                    ->counts('varians')
                    ->label('Varian')
                    ->badge(),
                TextColumn::make('badge')
                    ->label('Label Produk')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'baru' => 'success',
                        'terlaris' => 'warning',
                        'terbatas' => 'danger',
                        'preorder' => 'info',
                        default => 'gray',
                    })
                    ->toggleable(),
                IconColumn::make('aktif')->boolean(),
                IconColumn::make('unggulan')->label('Unggulan')->boolean()->toggleable(),
                TextColumn::make('urutan')->label('Urutan')->numeric()->sortable()->toggleable(),
            ])
            ->defaultSort('urutan')
            ->filters([
                SelectFilter::make('kategori_id')
                    ->relationship('kategori', 'nama')
                    ->label('Kategori'),
                TernaryFilter::make('aktif')->label('Aktif'),
                TernaryFilter::make('unggulan')->label('Unggulan'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
