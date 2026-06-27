<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kategori extends Model
{
    protected $fillable = ['nama', 'slug', 'deskripsi', 'gambar', 'urutan', 'aktif'];

    public function produks(): HasMany
    {
        return $this->hasMany(Produk::class, 'kategori_id');
    }
}
