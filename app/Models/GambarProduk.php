<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
