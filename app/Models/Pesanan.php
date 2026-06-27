<?php

namespace App\Models;

use App\Mail\OrderPlacedMail;
use App\Mail\OrderStatusUpdatedMail;
use DomainException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Throwable;

class Pesanan extends Model
{
    public const STATUS_PENDING_PAYMENT = 'pending_payment';
    public const STATUS_PAID = 'paid';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_PACKED = 'packed';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_RETURN_REQUESTED = 'return_requested';
    public const STATUS_REFUNDED = 'refunded';

    protected $fillable = [
        'kode_pesanan',
        'session_id',
        'user_id',
        'status',
        'after_sales_status',
        'after_sales_type',
        'after_sales_reason',
        'after_sales_requested_at',
        'after_sales_resolved_at',
        'nama_penerima',
        'telepon',
        'email',
        'kota',
        'alamat_lengkap',
        'metode_pengiriman',
        'kurir_pengiriman',
        'nomor_resi',
        'dikirim_pada',
        'metode_pembayaran',
        'subtotal',
        'ongkir',
        'diskon',
        'voucher_id',
        'voucher_code',
        'total',
        'batas_bayar',
        'dibayar_pada',
        'stock_reserved_at',
    ];

    protected $casts = [
        'batas_bayar' => 'datetime',
        'dibayar_pada' => 'datetime',
        'dikirim_pada' => 'datetime',
        'stock_reserved_at' => 'datetime',
        'after_sales_requested_at' => 'datetime',
        'after_sales_resolved_at' => 'datetime',
    ];

    public function canRequestAfterSales(): bool
    {
        return in_array($this->status, [
            self::STATUS_DELIVERED,
            self::STATUS_COMPLETED,
        ], true) && $this->after_sales_status === null;
    }

    public function items(): HasMany
    {
        return $this->hasMany(ItemPesanan::class, 'pesanan_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }

    public static function allowedTransitions(): array
    {
        return [
            self::STATUS_PENDING_PAYMENT => [
                self::STATUS_PAID,
                self::STATUS_CANCELLED,
                self::STATUS_EXPIRED,
            ],
            self::STATUS_PAID => [
                self::STATUS_PROCESSING,
                self::STATUS_CANCELLED,
                self::STATUS_REFUNDED,
            ],
            self::STATUS_PROCESSING => [
                self::STATUS_PACKED,
                self::STATUS_CANCELLED,
                self::STATUS_REFUNDED,
            ],
            self::STATUS_PACKED => [
                self::STATUS_SHIPPED,
                self::STATUS_CANCELLED,
                self::STATUS_REFUNDED,
            ],
            self::STATUS_SHIPPED => [
                self::STATUS_DELIVERED,
                self::STATUS_REFUNDED,
            ],
            self::STATUS_DELIVERED => [
                self::STATUS_COMPLETED,
                self::STATUS_RETURN_REQUESTED,
                self::STATUS_REFUNDED,
            ],
            self::STATUS_COMPLETED => [
                self::STATUS_RETURN_REQUESTED,
            ],
            self::STATUS_RETURN_REQUESTED => [
                self::STATUS_REFUNDED,
            ],
        ];
    }

    public function canTransitionTo(string $status): bool
    {
        return in_array($status, self::allowedTransitions()[$this->status] ?? [], true);
    }

    public function transitionTo(string $status, string $actor = 'system', array $meta = []): void
    {
        if ($this->status === $status) {
            return;
        }

        if (! $this->canTransitionTo($status)) {
            throw new DomainException("Transisi pesanan {$this->status} ke {$status} tidak diizinkan.");
        }

        $fromStatus = $this->status;
        $changes = ['status' => $status];

        if ($status === self::STATUS_PAID && ! $this->dibayar_pada) {
            $changes['dibayar_pada'] = now();
        }

        DB::transaction(function () use ($status, &$changes) {
            if ($this->shouldRestoreStockFor($status)) {
                $this->restoreReservedStock();
                $changes['stock_reserved_at'] = null;
            }

            $this->forceFill($changes)->save();
        });

        activity('pesanan')
            ->performedOn($this)
            ->causedBy(auth()->user())
            ->withProperties(array_merge($meta, [
                'actor' => $actor,
                'from_status' => $fromStatus,
                'to_status' => $status,
            ]))
            ->log('status_changed');

        $this->sendStatusUpdateNotification();
    }

    private function shouldRestoreStockFor(string $status): bool
    {
        return $this->stock_reserved_at !== null
            && in_array($status, [
                self::STATUS_CANCELLED,
                self::STATUS_EXPIRED,
                self::STATUS_REFUNDED,
            ], true);
    }

    private function restoreReservedStock(): void
    {
        $this->items()
            ->whereNotNull('varian_id')
            ->get()
            ->each(function (ItemPesanan $item) {
                $variant = VarianProduk::whereKey($item->varian_id)->lockForUpdate()->first();

                if (! $variant) {
                    return;
                }

                $variant->forceFill(['stok' => $variant->stok + $item->jumlah])->save();
            });
    }

    public function expireIfOverdue(): bool
    {
        if ($this->status !== self::STATUS_PENDING_PAYMENT || ! $this->batas_bayar?->isPast()) {
            return false;
        }

        $this->transitionTo(self::STATUS_EXPIRED, 'system', ['reason' => 'payment_deadline_passed']);

        return true;
    }

    /**
     * Generate unique order code: AQ + YYMMDD + random 4 chars
     */
    public static function generateKode(): string
    {
        do {
            $kode = 'AQ' . now()->format('ymd') . strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));
        } while (static::where('kode_pesanan', $kode)->exists());

        return $kode;
    }

    public function customerEmail(): ?string
    {
        return $this->email ?: $this->user?->email;
    }

    public function signedOrderUrl(): string
    {
        return URL::temporarySignedRoute('pesanan.show', now()->addDays(30), [
            'kode' => $this->kode_pesanan,
        ]);
    }

    public function signedInvoiceUrl(): string
    {
        return URL::temporarySignedRoute('pesanan.invoice', now()->addDays(30), [
            'kode' => $this->kode_pesanan,
        ]);
    }

    public function sendOrderPlacedNotification(): void
    {
        $recipient = $this->customerEmail();

        if (! $recipient) {
            return;
        }

        try {
            Mail::to($recipient)->send(new OrderPlacedMail($this, $this->signedOrderUrl(), $this->signedInvoiceUrl()));
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    public function sendStatusUpdateNotification(): void
    {
        $recipient = $this->customerEmail();

        if (! $recipient) {
            return;
        }

        try {
            Mail::to($recipient)->send(new OrderStatusUpdatedMail($this, $this->signedOrderUrl(), $this->signedInvoiceUrl()));
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
