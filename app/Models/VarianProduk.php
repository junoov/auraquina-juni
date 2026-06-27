<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VarianProduk extends Model
{
    protected $fillable = [
        'produk_id', 'ukuran', 'warna', 'kode_warna', 'sku', 'stok', 'penyesuaian_harga',
        'shopee_model_id',
    ];

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    public function gambarVarians(): HasMany
    {
        return $this->hasMany(GambarVarianProduk::class, 'varian_produk_id')->orderBy('urutan');
    }

    public function gambarVarianUtama()
    {
        return $this->hasOne(GambarVarianProduk::class, 'varian_produk_id')->where('utama', true);
    }
}
