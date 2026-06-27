<?php

namespace App\Filament\Admin\Resources\Roles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\PaginationMode;
use Filament\Tables\Table;

class RolesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->paginationMode(PaginationMode::Simple)
            ->columns([
                TextColumn::make('name')->label('Nama Peran')->searchable()->sortable()->badge()->color('primary'),
                TextColumn::make('guard_name')->label('Guard Akses')->toggleable(),
                TextColumn::make('permissions_count')
                    ->counts('permissions')
                    ->label('Hak Akses'),
                TextColumn::make('users_count')
                    ->counts('users')
                    ->label('Pengguna'),
                TextColumn::make('created_at')->label('Dibuat Pada')->dateTime()->sortable()->toggleable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->visible(fn ($record) => ! in_array($record->name, ['owner', 'admin'])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
