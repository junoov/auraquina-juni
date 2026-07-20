<?php

namespace App\Models;

use App\Services\ProductImageVariantService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class GambarProduk extends Model
{
    protected $fillable = ['produk_id', 'url', 'alt', 'urutan', 'utama', 'shopee_image_id'];

    protected $casts = [
        'utama' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saved(function (self $image) {
            if ($image->url && ($image->wasRecentlyCreated || $image->wasChanged('url'))) {
                app(ProductImageVariantService::class)->generate($image->url);
            }
        });
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    public function getFullUrlAttribute(): ?string
    {
        if (! $this->url) {
            return null;
        }
        if (str_starts_with($this->url, 'http://') || str_starts_with($this->url, 'https://')) {
            return $this->url;
        }
        $disk = config('filesystems.disks.r2.bucket') ? 'r2' : 'public';

        return Storage::disk($disk)->url($this->url);
    }

    public function variantUrl(string $variant): ?string
    {
        return app(ProductImageVariantService::class)->url($this->url, $variant);
    }

    public function variantSrcset(array $variants): ?string
    {
        return app(ProductImageVariantService::class)->srcset($this->url, $variants);
    }
}
