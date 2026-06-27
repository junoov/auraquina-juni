<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    public const TYPE_FIXED = 'fixed';
    public const TYPE_PERCENT = 'percent';
    public const TYPE_FREE_SHIPPING = 'free_shipping';

    protected $fillable = [
        'code',
        'name',
        'type',
        'value',
        'min_subtotal',
        'max_discount',
        'usage_limit',
        'used_count',
        'starts_at',
        'ends_at',
        'active',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'active' => 'boolean',
    ];

    public function isAvailableFor(int $subtotal): bool
    {
        if (! $this->active || $subtotal < $this->min_subtotal) {
            return false;
        }

        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return false;
        }

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }

        return ! ($this->ends_at && $this->ends_at->isPast());
    }

    public function discountFor(int $subtotal, int $shipping): int
    {
        if (! $this->isAvailableFor($subtotal)) {
            return 0;
        }

        $discount = match ($this->type) {
            self::TYPE_FIXED => $this->value,
            self::TYPE_PERCENT => (int) floor($subtotal * ($this->value / 100)),
            self::TYPE_FREE_SHIPPING => $shipping,
            default => 0,
        };

        if ($this->max_discount !== null) {
            $discount = min($discount, $this->max_discount);
        }

        return max(0, min($discount, $subtotal + $shipping));
    }
}
