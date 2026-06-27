<?php

namespace App\Filament\Admin\Resources\Vouchers\Tables;

use App\Models\Voucher;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\PaginationMode;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class VouchersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->paginationMode(PaginationMode::Simple)
            ->columns([
                TextColumn::make('code')->label('Kode')->searchable()->sortable()->copyable()->weight('bold'),
                TextColumn::make('name')->label('Nama')->searchable()->wrap(),
                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        Voucher::TYPE_PERCENT => 'Persentase',
                        Voucher::TYPE_FIXED => 'Potongan Tetap',
                        Voucher::TYPE_FREE_SHIPPING => 'Gratis Ongkir',
                        default => $state,
                    }),
                TextColumn::make('value')->label('Nilai')->numeric(),
                TextColumn::make('min_subtotal')->label('Min. Belanja')->money('IDR', divideBy: 1),
                TextColumn::make('used_count')->label('Dipakai')->numeric()->sortable(),
                TextColumn::make('usage_limit')->label('Batas')->numeric()->toggleable(),
                IconColumn::make('active')->label('Aktif')->boolean(),
                TextColumn::make('starts_at')->label('Mulai')->dateTime('d M Y H:i')->toggleable(),
                TextColumn::make('ends_at')->label('Berakhir')->dateTime('d M Y H:i')->toggleable(),
                TextColumn::make('created_at')->label('Dibuat')->dateTime('d M Y H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TernaryFilter::make('active')->label('Aktif'),
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
