<?php

namespace App\Filament\Admin\Resources\Produks\Tables;

use App\Services\ProductImageVariantService;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
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
            ->modifyQueryUsing(fn ($query) => $query
                ->select(['id', 'kategori_id', 'nama', 'harga', 'aktif', 'urutan'])
                ->with([
                    'gambarUtama:id,produk_id,url',
                    'kategori:id,nama',
                ])
                ->withSum('varians', 'stok')
                ->withCount('varians')
            )
            ->paginationMode(PaginationMode::Simple)
            ->columns([
                ImageColumn::make('gambarUtama.url')
                    ->label('')
                    ->getStateUsing(fn ($record) => app(ProductImageVariantService::class)
                        ->url($record->gambarUtama?->url, 'thumb'))
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
                    ->state(fn ($record) => (int) $record->varians_sum_stok)
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state === null || $state <= 0 => 'danger',
                        $state < 10 => 'warning',
                        default => 'success',
                    })
                    ->tooltip(fn ($record) => $record->varians_sum_stok.' unit tersedia dari '.$record->varians_count.' varian'),

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
