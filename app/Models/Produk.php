<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produk extends Model
{
    protected $fillable = [
        'kategori_id', 'nama', 'slug', 'deskripsi', 'deskripsi_singkat',
        'harga', 'harga_coret', 'sku', 'berat', 'bahan',
        'perawatan', 'info_model', 'aktif', 'unggulan', 'badge', 'urutan',
        'shopee_item_id', 'shopee_shop_id', 'shopee_url', 'rating_star',
        'stock_display', 'source_categories',
    ];

    protected $casts = [
        'aktif' => 'boolean',
        'unggulan' => 'boolean',
        'source_categories' => 'array',
        'rating_star' => 'decimal:2',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function varians(): HasMany
    {
        return $this->hasMany(VarianProduk::class, 'produk_id');
    }

    public function gambars(): HasMany
    {
        return $this->hasMany(GambarProduk::class, 'produk_id')->orderBy('urutan');
    }

    public function gambarUtama()
    {
        return $this->hasOne(GambarProduk::class, 'produk_id')->where('utama', true);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'produk_id');
    }

    // Helper: harga format rupiah
    public function hargaFormatted(): string
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }

    public function hargaCoretFormatted(): ?string
    {
        return $this->harga_coret ? 'Rp ' . number_format($this->harga_coret, 0, ',', '.') : null;
    }

    public function hasDiscount(): bool
    {
        return $this->harga_coret !== null && $this->harga_coret > $this->harga;
    }

    public function discountPercent(): ?int
    {
        if (! $this->hasDiscount()) return null;
        return (int) round((1 - $this->harga / $this->harga_coret) * 100);
    }

    // Helper: total stok dari semua varian
    public function totalStok(): int
    {
        return $this->varians()->sum('stok');
    }

    // Helper: daftar ukuran unik
    public function ukuranTersedia(): array
    {
        return $this->varians()->where('stok', '>', 0)->pluck('ukuran')->unique()->values()->toArray();
    }

    // Helper: daftar warna unik
    public function warnaTersedia(): array
    {
        return $this->varians()
            ->where('stok', '>', 0)
            ->select('warna', 'kode_warna')
            ->distinct()
            ->get()
            ->toArray();
    }
}
