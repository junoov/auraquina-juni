<?php

namespace Database\Seeders;

use App\Models\GambarProduk;
use App\Models\Kategori;
use App\Models\Produk;
use App\Models\VarianProduk;
use Illuminate\Database\Seeder;

class ProdukSeeder extends Seeder
{
    public function run(): void
    {
        // Kategori
        $abaya = Kategori::create(['nama' => 'Abaya', 'slug' => 'abaya', 'deskripsi' => 'Koleksi abaya premium', 'urutan' => 1]);
        $khimar = Kategori::create(['nama' => 'Khimar', 'slug' => 'khimar', 'deskripsi' => 'Koleksi khimar elegan', 'urutan' => 2]);
        $oneSet = Kategori::create(['nama' => 'One Set', 'slug' => 'one-set', 'deskripsi' => 'Koleksi one set praktis', 'urutan' => 3]);
        $aksesoris = Kategori::create(['nama' => 'Aksesoris', 'slug' => 'aksesoris', 'deskripsi' => 'Aksesoris pelengkap', 'urutan' => 4]);

        // === PRODUK 1: Nujayl Abaya - Black ===
        $p1 = Produk::create([
            'kategori_id' => $abaya->id,
            'nama' => 'Nujayl Abaya',
            'slug' => 'nujayl-abaya',
            'deskripsi' => 'Dibuat dari material premium berkualitas tinggi, Nujayl Abaya menghadirkan keanggunan klasik dengan gaya modest modern. Nyaman dipakai seharian dan mudah dipadukan untuk melengkapi koleksi busana Anda.',
            'deskripsi_singkat' => 'Abaya premium dengan gaya modest modern',
            'harga' => 489000,
            'sku' => 'AQ-NJL-001',
            'berat' => 450,
            'bahan' => 'Material: Jetblack Premium (Polyester blend). Berat: ±450 gram. Tekstur halus, jatuh sempurna. Tidak menerawang.',
            'perawatan' => 'Cuci dengan tangan atau mesin (mode gentle). Gunakan deterjen lembut. Jangan gunakan pemutih. Setrika suhu rendah. Jemur di tempat teduh.',
            'info_model' => 'Tinggi model: 165 cm. Model memakai ukuran M. Fit: Loose / Oversize.',
            'aktif' => true,
            'unggulan' => true,
            'badge' => 'baru',
        ]);

        // Gambar Nujayl Abaya
        GambarProduk::create(['produk_id' => $p1->id, 'url' => 'https://d2kchovjbwl1tk.cloudfront.net/vendor/292/product/WhatsApp_Image_2026-04-26_at_205006_1777252624783_resized256-jpeg.webp', 'alt' => 'Nujayl Abaya tampak depan', 'urutan' => 1, 'utama' => true]);
        GambarProduk::create(['produk_id' => $p1->id, 'url' => 'https://d2kchovjbwl1tk.cloudfront.net/vendor/292/product/WhatsApp_Image_2026-04-26_at_205006_2_1777253657392_resized256-jpeg.webp', 'alt' => 'Nujayl Abaya tampak samping', 'urutan' => 2, 'utama' => false]);
        GambarProduk::create(['produk_id' => $p1->id, 'url' => 'https://d2kchovjbwl1tk.cloudfront.net/vendor/292/product/WhatsApp_Image_2026-04-26_at_205038_1_1777252824266_resized256-jpeg.webp', 'alt' => 'Nujayl Abaya tampak belakang', 'urutan' => 3, 'utama' => false]);

        // Varian Nujayl Abaya (ukuran x warna)
        $ukurans = ['XS', 'S', 'M', 'L', 'XL'];
        $warnas = [
            ['warna' => 'Black', 'kode_warna' => '#201916'],
            ['warna' => 'Beige', 'kode_warna' => '#c4a882'],
        ];

        foreach ($warnas as $warna) {
            foreach ($ukurans as $ukuran) {
                VarianProduk::create([
                    'produk_id' => $p1->id,
                    'ukuran' => $ukuran,
                    'warna' => $warna['warna'],
                    'kode_warna' => $warna['kode_warna'],
                    'sku' => 'AQ-NJL-' . strtoupper(substr($warna['warna'], 0, 3)) . '-' . $ukuran,
                    'stok' => rand(5, 20),
                ]);
            }
        }

        // === PRODUK 2: Eshaal Abaya ===
        $p2 = Produk::create([
            'kategori_id' => $abaya->id,
            'nama' => 'Eshaal Abaya',
            'slug' => 'eshaal-abaya',
            'deskripsi' => 'Eshaal Abaya hadir dengan desain minimalis yang elegan. Material premium yang ringan dan breathable, cocok untuk aktivitas sehari-hari maupun acara formal.',
            'deskripsi_singkat' => 'Abaya minimalis elegan untuk segala acara',
            'harga' => 459000,
            'sku' => 'AQ-ESH-001',
            'berat' => 400,
            'bahan' => 'Material: Crinkle Airflow Premium. Berat: ±400 gram. Ringan, breathable, tidak mudah kusut.',
            'perawatan' => 'Cuci dengan tangan atau mesin (mode gentle). Gunakan deterjen lembut. Jangan gunakan pemutih. Setrika suhu rendah.',
            'info_model' => 'Tinggi model: 163 cm. Model memakai ukuran M. Fit: Loose.',
            'aktif' => true,
            'unggulan' => true,
            'badge' => 'terlaris',
        ]);

        GambarProduk::create(['produk_id' => $p2->id, 'url' => 'https://d2kchovjbwl1tk.cloudfront.net/vendors/292/assets/image/1769674814745-eshaal_abaya_1_resized2048-jpg.webp', 'alt' => 'Eshaal Abaya tampak depan', 'urutan' => 1, 'utama' => true]);

        foreach ($warnas as $warna) {
            foreach ($ukurans as $ukuran) {
                VarianProduk::create([
                    'produk_id' => $p2->id,
                    'ukuran' => $ukuran,
                    'warna' => $warna['warna'],
                    'kode_warna' => $warna['kode_warna'],
                    'sku' => 'AQ-ESH-' . strtoupper(substr($warna['warna'], 0, 3)) . '-' . $ukuran,
                    'stok' => rand(3, 15),
                ]);
            }
        }

        // === PRODUK 3: Thuwal Instan ===
        $p3 = Produk::create([
            'kategori_id' => $khimar->id,
            'nama' => 'Thuwal Instan',
            'slug' => 'thuwal-instan',
            'deskripsi' => 'Khimar instan dengan desain praktis dan elegan. Langsung pakai tanpa perlu peniti, cocok untuk aktivitas sehari-hari.',
            'deskripsi_singkat' => 'Khimar instan praktis dan elegan',
            'harga' => 289000,
            'sku' => 'AQ-THW-001',
            'berat' => 250,
            'bahan' => 'Material: Jersey Premium. Berat: ±250 gram. Stretch, nyaman, tidak panas.',
            'perawatan' => 'Cuci dengan tangan. Jangan diperas terlalu kuat. Jemur di tempat teduh.',
            'info_model' => 'Tinggi model: 160 cm. Panjang depan: 90 cm. Panjang belakang: 110 cm.',
            'aktif' => true,
            'unggulan' => true,
            'badge' => 'baru',
        ]);

        GambarProduk::create(['produk_id' => $p3->id, 'url' => 'https://d2kchovjbwl1tk.cloudfront.net/vendors/292/assets/image/1769674814745-eshaal_abaya_1_resized2048-jpg.webp', 'alt' => 'Thuwal Instan', 'urutan' => 1, 'utama' => true]);

        foreach (['Black', 'Mocca', 'Navy'] as $warna) {
            $hex = match ($warna) {
                'Black' => '#201916',
                'Mocca' => '#8B6F4E',
                'Navy' => '#1B2838',
            };
            foreach (['S', 'M', 'L'] as $ukuran) {
                VarianProduk::create([
                    'produk_id' => $p3->id,
                    'ukuran' => $ukuran,
                    'warna' => $warna,
                    'kode_warna' => $hex,
                    'sku' => 'AQ-THW-' . strtoupper(substr($warna, 0, 3)) . '-' . $ukuran,
                    'stok' => rand(5, 20),
                ]);
            }
        }

        // === PRODUK 4: Nayla One Set ===
        $p4 = Produk::create([
            'kategori_id' => $oneSet->id,
            'nama' => 'Nayla One Set',
            'slug' => 'nayla-one-set',
            'deskripsi' => 'One set yang terdiri dari atasan dan bawahan dengan desain matching. Praktis, stylish, dan nyaman untuk berbagai aktivitas.',
            'deskripsi_singkat' => 'One set matching atasan dan bawahan',
            'harga' => 459000,
            'sku' => 'AQ-NYL-001',
            'berat' => 550,
            'bahan' => 'Material: Crinkle Airflow Premium. Berat: ±550 gram (set). Ringan dan breathable.',
            'perawatan' => 'Cuci dengan tangan atau mesin (mode gentle). Gunakan deterjen lembut. Setrika suhu rendah.',
            'info_model' => 'Tinggi model: 165 cm. Model memakai ukuran M.',
            'aktif' => true,
            'unggulan' => true,
        ]);

        GambarProduk::create(['produk_id' => $p4->id, 'url' => 'https://d2kchovjbwl1tk.cloudfront.net/vendor/292/product/WhatsApp_Image_2026-04-26_at_205009_1777252590047_resized256-jpeg.webp', 'alt' => 'Nayla One Set', 'urutan' => 1, 'utama' => true]);

        foreach (['Cream', 'Black'] as $warna) {
            $hex = $warna === 'Cream' ? '#F5E6D3' : '#201916';
            foreach ($ukurans as $ukuran) {
                VarianProduk::create([
                    'produk_id' => $p4->id,
                    'ukuran' => $ukuran,
                    'warna' => $warna,
                    'kode_warna' => $hex,
                    'sku' => 'AQ-NYL-' . strtoupper(substr($warna, 0, 3)) . '-' . $ukuran,
                    'stok' => rand(5, 15),
                ]);
            }
        }
    }
}
