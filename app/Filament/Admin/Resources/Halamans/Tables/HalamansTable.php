<?php

namespace App\Filament\Admin\Resources\Halamans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\PaginationMode;
use Filament\Tables\Table;

class HalamansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->paginationMode(PaginationMode::Simple)
            ->columns([
                TextColumn::make('title')->label('Judul')->searchable()->sortable()->wrap(),
                TextColumn::make('slug')->label('Slug')->searchable()->copyable(),
                TextColumn::make('eyebrow')->label('Label Kecil')->toggleable(),
                TextColumn::make('urutan')->label('Urutan')->numeric()->sortable(),
                IconColumn::make('aktif')->label('Aktif')->boolean(),
                TextColumn::make('updated_at')->label('Diubah')->dateTime('d M Y H:i')->sortable()->toggleable(),
            ])
            ->defaultSort('urutan')
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
