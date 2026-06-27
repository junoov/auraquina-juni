<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemPesanan extends Model
{
    protected $fillable = [
        'pesanan_id',
        'produk_id',
        'varian_id',
        'nama_produk',
        'varian_label',
        'harga',
        'jumlah',
        'gambar_url',
    ];

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    public function varian(): BelongsTo
    {
        return $this->belongsTo(VarianProduk::class, 'varian_id');
    }
}
