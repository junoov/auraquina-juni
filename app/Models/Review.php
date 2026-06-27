<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'produk_id',
        'user_id',
        'pesanan_id',
        'rating',
        'review',
        'status',
    ];

    protected static function booted(): void
    {
        static::saved(function (self $review) {
            $review->refreshProductRating();
        });

        static::deleted(function (self $review) {
            $review->refreshProductRating();
        });
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }

    public static function statusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'Menunggu Tinjauan',
            self::STATUS_APPROVED => 'Disetujui',
            self::STATUS_REJECTED => 'Ditolak',
        ];
    }

    private function refreshProductRating(): void
    {
        if (! $this->produk) {
            return;
        }

        $average = $this->produk->reviews()
            ->where('status', self::STATUS_APPROVED)
            ->avg('rating');

        $this->produk->forceFill([
            'rating_star' => $average === null ? 0 : round((float) $average, 2),
        ])->save();
    }
}
