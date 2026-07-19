<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class LoyaltyVoucher extends Model
{
    public const VALUE = 15000;

    protected $fillable = [
        'user_id',
        'milestone',
        'code',
        'value',
        'used_at',
        'pesanan_id',
    ];

    protected $casts = [
        'used_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class);
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->whereNull('used_at');
    }

    public static function awardForUser(User $user): ?self
    {
        return DB::transaction(function () use ($user): ?self {
            User::whereKey($user->id)->lockForUpdate()->firstOrFail();

            $qualifiedOrders = Pesanan::query()
                ->where('user_id', $user->id)
                ->where('status', Pesanan::STATUS_COMPLETED)
                ->whereHas('reviews', fn (Builder $query) => $query->whereIn(
                    'produk_id',
                    ItemPesanan::query()
                        ->select('produk_id')
                        ->whereColumn('pesanan_id', 'reviews.pesanan_id')
                ))
                ->count();

            $earnedMilestone = intdiv($qualifiedOrders, 3) * 3;
            $latest = null;

            for ($milestone = 3; $milestone <= $earnedMilestone; $milestone += 3) {
                $latest = self::firstOrCreate(
                    ['user_id' => $user->id, 'milestone' => $milestone],
                    ['code' => self::generateCode($milestone), 'value' => self::VALUE]
                );
            }

            return $latest;
        });
    }

    public static function generateCode(int $milestone): string
    {
        do {
            $code = 'AQ15-' . $milestone . '-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
        } while (self::where('code', $code)->exists());

        return $code;
    }
}
