<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class GambarVarianProduk extends Model
{
    protected $fillable = ['varian_produk_id', 'url', 'alt', 'urutan', 'utama', 'shopee_image_id'];

    protected $casts = [
        'utama' => 'boolean',
    ];

    public function varian(): BelongsTo
    {
        return $this->belongsTo(VarianProduk::class, 'varian_produk_id');
    }

    public function getFullUrlAttribute(): ?string
    {
        if (!$this->url) return null;
        if (str_starts_with($this->url, 'http://') || str_starts_with($this->url, 'https://')) {
            return $this->url;
        }
        $disk = config('filesystems.disks.r2.bucket') ? 'r2' : 'public';

        return Storage::disk($disk)->url($this->url);
    }
}
