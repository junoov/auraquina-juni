<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
