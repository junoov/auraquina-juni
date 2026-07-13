<?php

namespace App\Filament\Admin\Resources\Pesanans\Tables;

use App\Filament\Admin\Resources\Pesanans\Schemas\PesananForm;
use App\Models\Pesanan;
use DomainException;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
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
                    ->label('After-Sales')
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
                    ->label('Jenis After-Sales')
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
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn () => auth()->user()?->can('update_pesanan')),
                Action::make('confirmPayment')
                    ->label('Konfirmasi Bayar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'pending_payment' && auth()->user()?->can('process_pesanan'))
                    ->requiresConfirmation()
                    ->action(fn ($record) => self::transitionOrder($record, Pesanan::STATUS_PAID)),
                Action::make('process')
                    ->label('Proses')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->visible(fn ($record) => $record->status === 'paid' && auth()->user()?->can('process_pesanan'))
                    ->action(fn ($record) => self::transitionOrder($record, Pesanan::STATUS_PROCESSING)),
                Action::make('pack')
                    ->label('Kemas')
                    ->icon('heroicon-o-cube')
                    ->visible(fn ($record) => $record->status === 'processing' && auth()->user()?->can('process_pesanan'))
                    ->action(fn ($record) => self::transitionOrder($record, Pesanan::STATUS_PACKED)),
                 Action::make('ship')
                    ->label('Kirim')
                    ->icon('heroicon-o-truck')
                    ->visible(fn ($record) => $record->status === 'packed' && auth()->user()?->can('process_pesanan'))
                    ->schema([
                        TextInput::make('awb')->label('Nomor Resi')->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $kurir = match (true) {
                            $record && str_contains(strtolower($record->metode_pengiriman), 'jne') => 'JNE',
                            $record && (str_contains(strtolower($record->metode_pengiriman), 'j&t') || str_contains(strtolower($record->metode_pengiriman), 'jnt')) => 'J&T',
                            $record && str_contains(strtolower($record->metode_pengiriman), 'sicepat') => 'SiCepat',
                            $record && str_contains(strtolower($record->metode_pengiriman), 'anteraja') => 'AnterAja',
                            $record && str_contains(strtolower($record->metode_pengiriman), 'gosend') => 'GoSend',
                            default => $record ? $record->metode_pengiriman : null,
                        };

                        $record->forceFill([
                            'kurir_pengiriman' => $kurir,
                            'nomor_resi' => $data['awb'],
                            'dikirim_pada' => now(),
                        ])->save();
                        $transitioned = self::transitionOrder($record, Pesanan::STATUS_SHIPPED, [
                            'kurir' => $kurir,
                            'awb' => $data['awb'],
                        ]);

                        if (! $transitioned) {
                            return;
                        }

                        Notification::make()->title('Pesanan dikirim')->success()->send();
                    }),
                Action::make('cancel')
                    ->label('Batalkan')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => in_array($record->status, ['pending_payment', 'paid', 'processing']) && auth()->user()?->can('cancel_pesanan'))
                    ->schema([
                        Textarea::make('alasan')->label('Alasan')->required(),
                    ])
                    ->requiresConfirmation()
                    ->action(fn ($record, array $data) => self::transitionOrder($record, Pesanan::STATUS_CANCELLED, ['alasan' => $data['alasan']])),
                Action::make('refund')
                    ->label('Kembalikan Dana')
                    ->icon('heroicon-o-banknotes')
                    ->color('gray')
                    ->visible(fn ($record) => in_array($record->status, ['paid', 'shipped', 'delivered', 'return_requested']) && auth()->user()?->can('refund_pesanan'))
                    ->requiresConfirmation()
                    ->action(fn ($record) => self::transitionOrder($record, Pesanan::STATUS_REFUNDED)),
                Action::make('reviewAfterSales')
                    ->label('Tinjau After-Sales')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('warning')
                    ->visible(fn ($record) => in_array($record->after_sales_status, ['requested', 'in_review'], true) && auth()->user()?->can('update_pesanan'))
                    ->schema([
                        Select::make('after_sales_status')
                            ->label('Status After-Sales')
                            ->options(PesananForm::afterSalesStatusOptions())
                            ->default(fn ($record) => $record->after_sales_status ?: 'requested')
                            ->required(),
                        Textarea::make('after_sales_reason')
                            ->label('Catatan Admin')
                            ->default(fn ($record) => $record->after_sales_reason)
                            ->rows(4),
                    ])
                    ->action(function ($record, array $data) {
                        $record->forceFill([
                            'after_sales_status' => $data['after_sales_status'],
                            'after_sales_reason' => $data['after_sales_reason'],
                            'after_sales_resolved_at' => in_array($data['after_sales_status'], ['resolved', 'rejected'], true) ? now() : null,
                        ])->save();

                        Notification::make()->title('Status after-sales diperbarui')->success()->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    private static function transitionOrder(Pesanan $record, string $status, array $meta = []): bool
    {
        try {
            $record->transitionTo($status, 'admin', $meta);

            return true;
        } catch (DomainException $exception) {
            Notification::make()
                ->title($exception->getMessage())
                ->danger()
                ->send();

            return false;
        }
    }
}
