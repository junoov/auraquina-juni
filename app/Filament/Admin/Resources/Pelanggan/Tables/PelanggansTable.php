<?php

namespace App\Filament\Admin\Resources\Pelanggan\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\PaginationMode;
use Filament\Tables\Table;

class PelanggansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->paginationMode(PaginationMode::Simple)
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('email_verified_at')
                    ->label('Terverifikasi')
                    ->dateTime('d M Y H:i')
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Bergabung Pada')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([])
            ->recordActions([
                ViewAction::make()->label('Lihat Detail'),
                EditAction::make()->label('Ubah / Reset Sandi'),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
