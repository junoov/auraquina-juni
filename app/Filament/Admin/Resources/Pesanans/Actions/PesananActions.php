<?php

namespace App\Filament\Admin\Resources\Pesanans\Actions;

use App\Filament\Admin\Resources\Pesanans\Schemas\PesananForm;
use App\Models\Pesanan;
use DomainException;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;

/**
 * Factory untuk action workflow pesanan.
 *
 * Dipakai bersama oleh:
 * - ListPesanans (record actions di table)
 * - ViewPesanan (header actions di halaman lihat)
 *
 * Tujuannya: SATU sumber kebenaran untuk semua tombol workflow
 * (Konfirmasi Bayar, Proses, Kemas, Kirim, Batalkan, Refund,
 * Tinjau After-Sales, Ubah Alamat) biar gak ada duplikat & inkonsisten.
 */
class PesananActions
{
    /**
     * Konfirmasi pembayaran manual (mis. untuk COD / transfer manual).
     */
    public static function confirmPayment(): Action
    {
        return Action::make('confirmPayment')
            ->label('Konfirmasi Bayar')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->requiresConfirmation()
            ->modalDescription('Tandai pesanan ini sudah dibayar oleh pelanggan?')
            ->visible(fn (Pesanan $record): bool => $record->status === Pesanan::STATUS_PENDING_PAYMENT
                && auth()->user()?->can('process_pesanan'))
            ->action(fn (Pesanan $record) => self::transition($record, Pesanan::STATUS_PAID));
    }

    /**
     * Proses pesanan yang sudah dibayar.
     */
    public static function process(): Action
    {
        return Action::make('process')
            ->label('Proses')
            ->icon('heroicon-o-cog-6-tooth')
            ->color('primary')
            ->requiresConfirmation()
            ->visible(fn (Pesanan $record): bool => $record->status === Pesanan::STATUS_PAID
                && auth()->user()?->can('process_pesanan'))
            ->action(fn (Pesanan $record) => self::transition($record, Pesanan::STATUS_PROCESSING));
    }

    /**
     * Kemas pesanan yang sedang diproses.
     */
    public static function pack(): Action
    {
        return Action::make('pack')
            ->label('Kemas')
            ->icon('heroicon-o-cube')
            ->color('primary')
            ->requiresConfirmation()
            ->visible(fn (Pesanan $record): bool => $record->status === Pesanan::STATUS_PROCESSING
                && auth()->user()?->can('process_pesanan'))
            ->action(fn (Pesanan $record) => self::transition($record, Pesanan::STATUS_PACKED));
    }

    /**
     * Kirim pesanan — perlu input nomor resi + kurir.
     */
    public static function ship(): Action
    {
        return Action::make('ship')
            ->label('Kirim')
            ->icon('heroicon-o-truck')
            ->color('info')
            ->visible(fn (Pesanan $record): bool => $record->status === Pesanan::STATUS_PACKED
                && auth()->user()?->can('process_pesanan'))
            ->schema([
                TextInput::make('awb')
                    ->label('Nomor Resi')
                    ->required(),
            ])
            ->action(function (Pesanan $record, array $data): void {
                $kurir = match (true) {
                    str_contains(strtolower($record->metode_pengiriman), 'jne') => 'JNE',
                    str_contains(strtolower($record->metode_pengiriman), 'j&t') || str_contains(strtolower($record->metode_pengiriman), 'jnt') => 'J&T',
                    str_contains(strtolower($record->metode_pengiriman), 'sicepat') => 'SiCepat',
                    str_contains(strtolower($record->metode_pengiriman), 'anteraja') => 'AnterAja',
                    str_contains(strtolower($record->metode_pengiriman), 'gosend') => 'GoSend',
                    default => $record->metode_pengiriman,
                };

                $record->forceFill([
                    'kurir_pengiriman' => $kurir,
                    'nomor_resi' => $data['awb'],
                    'dikirim_pada' => now(),
                ])->save();

                if (! self::transition($record, Pesanan::STATUS_SHIPPED, ['kurir' => $kurir, 'awb' => $data['awb']])) {
                    return;
                }

                Notification::make()->title('Pesanan dikirim')->success()->send();
            });
    }

    /**
     * Batalkan pesanan (sebelum dikirim).
     */
    public static function cancel(): Action
    {
        return Action::make('cancel')
            ->label('Batalkan')
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->requiresConfirmation()
            ->visible(fn (Pesanan $record): bool => in_array($record->status, [
                    Pesanan::STATUS_PENDING_PAYMENT,
                    Pesanan::STATUS_PAID,
                    Pesanan::STATUS_PROCESSING,
                ], true) && auth()->user()?->can('cancel_pesanan'))
            ->schema([
                Textarea::make('alasan')
                    ->label('Alasan Pembatalan')
                    ->required(),
            ])
            ->action(fn (Pesanan $record, array $data) => self::transition(
                $record,
                Pesanan::STATUS_CANCELLED,
                ['alasan' => $data['alasan']],
            ));
    }

    /**
     * Kembalikan dana (refund).
     */
    public static function refund(): Action
    {
        return Action::make('refund')
            ->label('Kembalikan Dana')
            ->icon('heroicon-o-banknotes')
            ->color('gray')
            ->requiresConfirmation()
            ->modalDescription('Dana akan dikembalikan ke pelanggan. Aksi ini tercatat di log.')
            ->visible(fn (Pesanan $record): bool => in_array($record->status, [
                    Pesanan::STATUS_PAID,
                    Pesanan::STATUS_SHIPPED,
                    Pesanan::STATUS_DELIVERED,
                    Pesanan::STATUS_RETURN_REQUESTED,
                ], true) && auth()->user()?->can('refund_pesanan'))
            ->action(fn (Pesanan $record) => self::transition($record, Pesanan::STATUS_REFUNDED));
    }

    /**
     * Tinjau request after-sales (return / refund / komplain).
     */
    public static function reviewAfterSales(): Action
    {
        return Action::make('reviewAfterSales')
            ->label('Tinjau After-Sales')
            ->icon('heroicon-o-chat-bubble-left-right')
            ->color('warning')
            ->visible(fn (Pesanan $record): bool => in_array($record->after_sales_status, ['requested', 'in_review'], true)
                && auth()->user()?->can('update_pesanan'))
            ->schema([
                Select::make('after_sales_status')
                    ->label('Status After-Sales')
                    ->options(PesananForm::afterSalesStatusOptions())
                    ->default(fn (Pesanan $record) => $record->after_sales_status ?: 'requested')
                    ->required(),
                Textarea::make('after_sales_reason')
                    ->label('Catatan Admin')
                    ->default(fn (Pesanan $record) => $record->after_sales_reason)
                    ->rows(4),
            ])
            ->action(function (Pesanan $record, array $data): void {
                $record->forceFill([
                    'after_sales_status' => $data['after_sales_status'],
                    'after_sales_reason' => $data['after_sales_reason'],
                    'after_sales_resolved_at' => in_array($data['after_sales_status'], ['resolved', 'rejected'], true) ? now() : null,
                ])->save();

                Notification::make()->title('Status after-sales diperbarui')->success()->send();
            });
    }

    /**
     * Ubah alamat pengiriman (hanya sebelum dikirim, tercatat di log).
     */
    public static function editAddress(): Action
    {
        return Action::make('editAddress')
            ->label('Ubah Alamat')
            ->icon('heroicon-o-map-pin')
            ->color('gray')
            ->visible(fn (Pesanan $record): bool => $record->status === Pesanan::STATUS_PENDING_PAYMENT
                    || $record->status === Pesanan::STATUS_PAID
                    || $record->status === Pesanan::STATUS_PROCESSING
                    || $record->status === Pesanan::STATUS_PACKED
                && auth()->user()?->can('update_pesanan'))
            ->schema([
                TextInput::make('nama_penerima')->label('Nama Penerima')->required(),
                TextInput::make('telepon')->label('Telepon')->required(),
                TextInput::make('kota')->label('Kota')->required(),
                Textarea::make('alamat_lengkap')->label('Alamat Lengkap')->required()->rows(3),
            ])
            ->fillForm(fn (Pesanan $record): array => [
                'nama_penerima' => $record->nama_penerima,
                'telepon' => $record->telepon,
                'kota' => $record->kota,
                'alamat_lengkap' => $record->alamat_lengkap,
            ])
            ->action(function (Pesanan $record, array $data): void {
                $old = $record->only(['nama_penerima', 'telepon', 'kota', 'alamat_lengkap']);

                $record->forceFill($data)->save();

                activity('pesanan')
                    ->performedOn($record)
                    ->causedBy(auth()->user())
                    ->withProperties([
                        'actor' => 'admin',
                        'field' => 'shipping_address',
                        'old' => $old,
                        'new' => $data,
                    ])
                    ->log('address_changed');

                Notification::make()->title('Alamat pengiriman diperbarui')->success()->send();
            });
    }

    /**
     * Helper: jalankan transisi status dengan notifikasi error yang ramah.
     */
    public static function transition(Pesanan $record, string $status, array $meta = []): bool
    {
        try {
            $record->transitionTo($status, 'admin', $meta);

            Notification::make()
                ->title('Status pesanan: ' . (PesananForm::statusOptions()[$status] ?? $status))
                ->success()
                ->send();

            return true;
        } catch (DomainException $exception) {
            Notification::make()
                ->title($exception->getMessage())
                ->danger()
                ->send();

            return false;
        }
    }

    /**
     * Semua action workflow, siap dipasang di header ViewRecord.
     * Urutan = alur fulfillment alami.
     */
    public static function workflow(): array
    {
        return [
            static::confirmPayment(),
            static::process(),
            static::pack(),
            static::ship(),
            static::refund(),
            static::reviewAfterSales(),
            static::cancel(),
        ];
    }
}
