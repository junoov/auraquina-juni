<?php

namespace App\Filament\Admin\Resources\Pesanans\Tables;

use App\Filament\Admin\Resources\Pesanans\Actions\PesananActions;
use App\Filament\Admin\Resources\Pesanans\Schemas\PesananForm;
use App\Models\Pesanan;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\PaginationMode;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PesanansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->modifyQueryUsing(fn ($query) => $query->withCount('items'))
            ->paginationMode(PaginationMode::Simple)
            ->columns([
                TextColumn::make('kode_pesanan')
                    ->label('Kode')
                    ->searchable()
                    ->copyable()
                    ->weight('bold'),
                TextColumn::make('nama_penerima')
                    ->label('Nama Penerima')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('telepon')->label('Telepon')->searchable()->toggleable(),
                TextColumn::make('total')
                    ->label('Total')
                    ->money('IDR', divideBy: 1)
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => PesananForm::statusOptions()[$state] ?? $state)
                    ->color(fn ($state) => match ($state) {
                        'pending_payment' => 'warning',
                        'paid' => 'info',
                        'processing', 'packed' => 'primary',
                        'shipped' => 'info',
                        'delivered', 'completed' => 'success',
                        'cancelled', 'expired' => 'danger',
                        'return_requested' => 'warning',
                        'refunded' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('after_sales_status')
                    ->label('Bantuan pelanggan')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? (PesananForm::afterSalesStatusOptions()[$state] ?? $state) : '-')
                    ->color(fn ($state) => match ($state) {
                        'requested' => 'warning',
                        'in_review' => 'info',
                        'resolved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->toggleable(),
                TextColumn::make('after_sales_type')
                    ->label('Jenis bantuan')
                    ->formatStateUsing(fn ($state) => $state ? (PesananForm::afterSalesTypeOptions()[$state] ?? $state) : '-')
                    ->toggleable(),
                TextColumn::make('metode_pembayaran')->label('Metode Pembayaran')->toggleable(),
                TextColumn::make('metode_pengiriman')->label('Metode Pengiriman')->toggleable(),
                TextColumn::make('kurir_pengiriman')->label('Kurir')->toggleable(),
                TextColumn::make('nomor_resi')->label('Nomor Resi')->copyable()->toggleable(),
                TextColumn::make('dikirim_pada')->label('Dikirim Pada')->dateTime('d M Y H:i')->toggleable(),
                TextColumn::make('batas_bayar')->label('Batas Bayar')->dateTime('d M Y H:i')->toggleable(),
                TextColumn::make('dibayar_pada')->label('Dibayar Pada')->dateTime('d M Y H:i')->toggleable(),
                TextColumn::make('created_at')->label('Dibuat Pada')->dateTime('d M Y H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')->label('Status')->options(PesananForm::statusOptions()),
                Filter::make('siap_dikirim')
                    ->label('Siap Dikirim')
                    ->query(fn (Builder $query) => $query->whereIn('status', [Pesanan::STATUS_PAID, Pesanan::STATUS_PROCESSING, Pesanan::STATUS_PACKED])),
            ])
            ->recordActions([
                ViewAction::make()->label('Buka pesanan'),
                ActionGroup::make([
                    EditAction::make()
                        ->label('Ubah data khusus')
                        ->visible(fn () => auth()->user()?->can('update_pesanan')),
                    ...PesananActions::workflow(),
                ])->label('Kelola pesanan'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
