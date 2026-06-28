<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

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

    public function getFullGambarUrlAttribute(): ?string
    {
        if (!$this->gambar_url) return null;
        if (str_starts_with($this->gambar_url, 'http://') || str_starts_with($this->gambar_url, 'https://')) {
            return $this->gambar_url;
        }
        return Storage::disk('public')->url($this->gambar_url);
    }
}
