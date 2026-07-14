<?php

namespace App\Filament\Admin\Resources\Produks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\PaginationMode;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class ProduksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->modifyQueryUsing(fn ($query) => $query
                ->with(['varians', 'gambarUtama', 'kategori'])
            )
            ->paginationMode(PaginationMode::Simple)
            ->columns([
                ImageColumn::make('gambarUtama.url')
                    ->label('')
                    ->getStateUsing(function ($record) {
                        if (!$record->gambarUtama?->url) return null;
                        $path = $record->gambarUtama->url;
                        if (str_starts_with($path, 'http')) return $path;
                        return config('filesystems.disks.r2.url') . '/' . ltrim($path, '/');
                    })
                    ->square()
                    ->size(48)
                    ->extraImgAttributes(['class' => 'rounded-lg']),

                TextColumn::make('nama')
                    ->label('Nama Produk')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->wrap()
                    ->tooltip(fn ($record) => $record->nama),

                TextColumn::make('kategori.nama')
                    ->label('Kategori')
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('harga')
                    ->label('Harga')
                    ->money('IDR', divideBy: 1)
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('totalStok')
                    ->label('Stok')
                    ->state(fn ($record) => $record->varians->sum('stok')) // Use loaded relation, not new query
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state === null || $state <= 0 => 'danger',
                        $state < 10 => 'warning',
                        default => 'success',
                    })
                    ->tooltip(fn ($record) => $record->varians->sum('stok') . ' unit tersedia dari ' . $record->varians->count() . ' varian'),

                TextColumn::make('aktif')
                    ->label('Status')
                    ->badge()
                    ->state(fn ($record) => $record->aktif ? 'Aktif' : 'Nonaktif')
                    ->color(fn ($record) => $record->aktif ? 'success' : 'danger'),
            ])
            ->defaultSort('urutan')
            ->filters([
                SelectFilter::make('kategori_id')
                    ->relationship('kategori', 'nama')
                    ->label('Kategori'),
                TernaryFilter::make('aktif')
                    ->label('Status')
                    ->trueLabel('Aktif')
                    ->falseLabel('Nonaktif'),
                TernaryFilter::make('unggulan')
                    ->label('Unggulan'),
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
