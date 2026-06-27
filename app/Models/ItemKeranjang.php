<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemKeranjang extends Model
{
    protected $fillable = ['session_id', 'user_id', 'produk_id', 'varian_id', 'jumlah'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
