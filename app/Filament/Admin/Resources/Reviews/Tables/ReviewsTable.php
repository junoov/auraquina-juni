<?php

namespace App\Filament\Admin\Resources\Reviews\Tables;

use App\Models\Review;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\PaginationMode;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->paginationMode(PaginationMode::Simple)
            ->columns([
                TextColumn::make('produk.nama')->label('Produk')->searchable()->wrap(),
                TextColumn::make('user.name')->label('Pelanggan')->searchable(),
                TextColumn::make('rating')->label('Rating')->badge(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => Review::statusOptions()[$state] ?? $state)
                    ->color(fn (string $state) => match ($state) {
                        Review::STATUS_PENDING => 'warning',
                        Review::STATUS_APPROVED => 'success',
                        Review::STATUS_REJECTED => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('review')->label('Ulasan')->limit(80)->wrap(),
                TextColumn::make('created_at')->label('Dibuat')->dateTime('d M Y H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')->label('Status')->options(Review::statusOptions()),
            ])
            ->recordActions([
                Action::make('setujui')
                    ->label('Setujui')
                    ->color('success')
                    ->visible(fn (Review $record) => $record->status !== Review::STATUS_APPROVED && (auth()->user()?->can('update_review') ?? false))
                    ->action(function (Review $record) {
                        $record->update(['status' => Review::STATUS_APPROVED]);
                        Notification::make()->title('Ulasan disetujui')->success()->send();
                    }),
                Action::make('tolak')
                    ->label('Tolak')
                    ->color('danger')
                    ->visible(fn (Review $record) => $record->status !== Review::STATUS_REJECTED && (auth()->user()?->can('update_review') ?? false))
                    ->action(function (Review $record) {
                        $record->update(['status' => Review::STATUS_REJECTED]);
                        Notification::make()->title('Ulasan ditolak')->success()->send();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
