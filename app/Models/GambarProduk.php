<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class GambarProduk extends Model
{
    protected $fillable = ['produk_id', 'url', 'alt', 'urutan', 'utama', 'shopee_image_id'];

    protected $casts = [
        'utama' => 'boolean',
    ];

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'produk_id');
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
