# Konteks Project: Auraquina E-commerce + Shopee Scraping

## Background
Lagi bangun ecommerce buat client (toko Shopee `auraquina`, shopid=17019889,
~41 produk fashion muslim — abaya, gamis, khimar, oneset).
Sebelum build catalog manual, mau import data + gambar dari Shopee toko client.

## Lokasi Project
- Scraping toolkit: `D:\Scraping\` (Python + Playwright + bookmarklet)
- Ecommerce target: `D:\Coding\auraquina\` (Laravel 13.8 + Tailwind CSS 4 + SQLite)

## Status Scraping (UDAH SELESAI)
Setelah berbagai pendekatan automation gagal (Shopee 2026 multi-layer detection:
TLS fingerprinting, signed header `af-ac-enc-dat`, CDP detection, dll),
solusi yang JALAN adalah:

**Bookmarklet manual di Chrome asli yang udah login.**
1. `D:\Scraping\shopee_bookmarklet.js` — interceptor fetch/XHR + tombol
   "Auto Fetch Detail" yang loop fetch `/api/v4/pdp/get_pc` per produk
   dengan jitter 1.8-3.2 detik.
2. `D:\Scraping\extract_pdp_v4.py` — parser response endpoint baru Shopee
   (schema snake_case: `item_id`, `shop_id`, `data.product_images.images`,
   `first_tier_variations`).

Hasil tersimpan di `D:\Scraping\output\pdp_v4\`:
- 42 folder produk, masing-masing punya `data.json` + `description.txt` +
  `image-NN.jpg` (HD ~1MB+ per gambar)
- `catalog.json` — semua produk dalam 1 file
- Total 720 gambar, 564 MB

## Schema data hasil extract (per produk)
```json
{
  "itemid": 23615938680,
  "shopid": 17019889,
  "name": "Auraquina . Mahyra Dress . Gamis Rayon Homedress",
  "brand": "Auraquina",
  "categories": ["Fashion Muslim", "Pakaian Muslim Wanita", "Dress", "Gamis"],
  "description": "MAHYRA DRESS\n\n...",
  "price": 228000,
  "price_min": 228000,
  "price_max": 241000,
  "rating_star": 4.92,
  "image_ids": [...],
  "image_urls": ["https://down-id.img.susercontent.com/file/..."],
  "models": [
    {"model_id": 262603537085, "name": "Cloudy", "price": 228000, "stock": null},
    ...
  ],
  "tier_variations": [{"name": "Warna", "options": ["Cloudy", "Folia Blue", ...]}]
}
```

## Schema Laravel Auraquina (yang harus di-isi)
Tables: `kategoris`, `produks`, `varian_produks`, `gambar_produks`, `item_keranjangs`.

**produks**: kategori_id, nama, slug, deskripsi, deskripsi_singkat, harga,
harga_coret, sku (unique), berat, bahan, perawatan, info_model, aktif,
unggulan, badge (enum: baru/terlaris/terbatas/preorder), urutan.

**varian_produks**: produk_id, ukuran (XS/S/M/L/XL), warna, kode_warna (hex),
sku (unique), stok, penyesuaian_harga.

**gambar_produks**: produk_id, url, alt, urutan, utama (boolean).

**kategoris**: udah ada 4 di seeder — Abaya, Khimar, One Set, Aksesoris.

Models Eloquent (`app/Models/`): Produk, VarianProduk, GambarProduk, Kategori,
ItemKeranjang. Relationships udah set up (HasMany/BelongsTo).

ProdukSeeder existing (`database/seeders/ProdukSeeder.php`) bikin 4 produk demo
dengan pattern: Kategori::create() → Produk::create() → GambarProduk::create()
loop + VarianProduk::create() nested loop (warna x ukuran).

## Yang dibutuhin
Bikin importer (Artisan command atau seeder) yang:
1. Baca `D:\Scraping\output\pdp_v4\catalog.json`
2. Loop tiap produk → mapping ke schema Auraquina
3. Copy gambar dari `D:\Scraping\output\pdp_v4\<folder>\image-NN.jpg` ke
   `storage/app/public/produk/<slug>/` (Laravel storage)
4. Insert ke `produks`, `varian_produks`, `gambar_produks`

## Decisions yang udah dibuat
- **Image storage**: copy ke `storage/app/public/produk/<slug>/` (permanent,
  gak gantung Shopee CDN). Run `php artisan storage:link` kalo belum.
- **Data existing**: truncate produks/varian_produks/gambar_produks dulu,
  replace total. Kategori existing dipertahanin.
- **Category mapping**: auto-rules berdasarkan nama:
  - mengandung "Gamis/Dress/Abaya" → Abaya
  - "Khimar/Hijab/Bergo/Pashmina" → Khimar
  - "Set/Setelan/Oneset" → One Set
  - sisanya → Aksesoris
- **Default stok**: 10 per varian (biar toko langsung jalan, client edit manual nanti).
- **Ukuran**: Shopee gak ekspose ukuran, default "All Size".
- **kode_warna**: null (Shopee gak ekspose hex), client isi manual nanti.
- **SKU produk**: `SHP-<itemid>` (e.g. SHP-23615938680).
- **SKU varian**: `SHP-<itemid>-<model_id>`.
- **Cleanup nama**: strip prefix "Auraquina ." dan suffix berlebih.

## Yang harus dikerjain
1. Bikin Artisan command `php artisan auraquina:import-shopee` di
   `app/Console/Commands/ImportShopeeProducts.php`
2. Pake DB transaction biar atomic
3. Run command → verifikasi data masuk lewat tinker atau bikin route test
4. Kalo storage:link belum, jalanin dulu
