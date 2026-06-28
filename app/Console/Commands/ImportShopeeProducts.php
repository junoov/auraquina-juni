<?php

namespace App\Console\Commands;

use App\Models\GambarProduk;
use App\Models\GambarVarianProduk;
use App\Models\Kategori;
use App\Models\Produk;
use App\Models\VarianProduk;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ImportShopeeProducts extends Command
{
    protected $signature = 'auraquina:import-shopee
                            {--catalog=D:\Scraping\output\pdp_v4\catalog.json : Path ke catalog.json}
                            {--images=D:\Scraping\output\pdp_v4 : Root folder gambar per produk}
                            {--dry-run : Preview tanpa insert ke DB}
                            {--limit=0 : Batasi jumlah produk yang diimport (0 = semua)}';

    protected $description = 'Import produk dari hasil scraping Shopee ke database Auraquina';

    private array $categoryCache = [];

    public function handle(): int
    {
        $catalogPath = $this->option('catalog');
        $imagesRoot = $this->option('images');
        $dryRun = (bool) $this->option('dry-run');

        if (! file_exists($catalogPath)) {
            $this->error("catalog.json tidak ditemukan: {$catalogPath}");
            return self::FAILURE;
        }

        $catalog = json_decode(file_get_contents($catalogPath), true);
        if (! is_array($catalog)) {
            $this->error('Gagal parse catalog.json');
            return self::FAILURE;
        }
        if (isset($catalog['products']) && is_array($catalog['products'])) {
            $catalog = $catalog['products'];
        }

        $limit = (int) $this->option('limit');
        if ($limit > 0) {
            $catalog = array_slice($catalog, 0, $limit);
        }

        $this->info('Ditemukan '.count($catalog).' produk yang akan diimport.');

        if ($dryRun) {
            $this->warn('[DRY RUN] Tidak ada perubahan ke DB atau storage.');
        } elseif (! file_exists(public_path('storage'))) {
            $this->call('storage:link');
        }

        if (! $dryRun) {
            $this->ensureShopCategories();
        }
        $this->categoryCache = Kategori::query()->get()->keyBy('slug')->all();

        DB::transaction(function () use ($catalog, $imagesRoot, $dryRun) {
            if (! $dryRun) {
                $this->info('Truncate gambar_varian_produks, gambar_produks, varian_produks, produks...');
                DB::table('gambar_varian_produks')->delete();
                DB::table('gambar_produks')->delete();
                DB::table('varian_produks')->delete();
                DB::table('produks')->delete();
            }

            $bar = $this->output->createProgressBar(count($catalog));
            $bar->start();

            $urutan = 1;
            foreach ($catalog as $item) {
                $this->importProduct($item, $imagesRoot, $dryRun, $urutan++);
                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
        });

        $this->info('Import selesai.');

        return self::SUCCESS;
    }

    private function importProduct(array $item, string $imagesRoot, bool $dryRun, int $urutan): void
    {
        $rawName = (string) ($item['name'] ?? '');
        $nama = $this->cleanName($rawName);
        $slug = $this->uniqueSlug(Str::slug($nama) ?: 'produk');
        $itemId = (int) ($item['itemid'] ?? 0);
        $sku = 'SHP-'.$itemId;
        $harga = (int) round((float) ($item['price_min'] ?? $item['price'] ?? 0));
        $hargaMax = (int) round((float) ($item['price_max'] ?? $harga));
        $deskripsi = trim((string) ($item['description'] ?? ''));
        $kategori = $this->resolveKategoriData($nama, $item['categories'] ?? []);

        $sourceFolder = $this->resolveSourceFolder($imagesRoot, $itemId);
        [$galleryFiles, $variantFilesByColor] = $this->partitionImageFiles($item, $sourceFolder);

        if ($dryRun) {
            $this->line("[DRY] {$sku} | {$nama} | kategori={$kategori['nama']} | gallery=".count($galleryFiles).' | warna='.count($variantFilesByColor));
            return;
        }

        $kategoriId = $this->getKategoriId($kategori['slug'], $kategori['nama'], $kategori['urutan']);

        $produk = Produk::create([
            'kategori_id' => $kategoriId,
            'nama' => $nama,
            'slug' => $slug,
            'deskripsi' => $deskripsi,
            'deskripsi_singkat' => $this->shortDesc($deskripsi),
            'harga' => $harga,
            'harga_coret' => $hargaMax > $harga ? $hargaMax : null,
            'sku' => $sku,
            'berat' => 0,
            'bahan' => $this->extractMaterial($deskripsi),
            'perawatan' => null,
            'info_model' => null,
            'aktif' => true,
            'unggulan' => false,
            'badge' => null,
            'urutan' => $urutan,
            'shopee_item_id' => $itemId ?: null,
            'shopee_shop_id' => (int) ($item['shopid'] ?? 0) ?: null,
            'shopee_url' => $item['url'] ?? null,
            'rating_star' => isset($item['rating_star']) ? round((float) $item['rating_star'], 2) : null,
            'stock_display' => $item['stock_display'] ?? null,
            'source_categories' => $item['categories'] ?? null,
        ]);

        $destDir = storage_path('app/public/produk/'.$slug);
        File::ensureDirectoryExists($destDir, 0755);

        $this->importGalleryImages($produk, $galleryFiles, $destDir);
        $this->importVariants($item, $produk, $itemId, $variantFilesByColor, $destDir);
    }

    private function importGalleryImages(Produk $produk, array $galleryFiles, string $destDir): void
    {
        $urutan = 1;

        foreach ($galleryFiles as $image) {
            $filename = 'gallery-'.str_pad((string) $urutan, 2, '0', STR_PAD_LEFT).'.jpg';
            $destination = $destDir.DIRECTORY_SEPARATOR.$filename;
            copy($image['file'], $destination);

            GambarProduk::create([
                'produk_id' => $produk->id,
                            'url' => 'produk/'.$produk->slug.'/'.$filename,
                'alt' => $produk->nama.' - gambar '.$urutan,
                'urutan' => $urutan,
                'utama' => $urutan === 1,
                'shopee_image_id' => $image['image_id'],
            ]);

            $urutan++;
        }
    }

    private function importVariants(array $item, Produk $produk, int $itemId, array $variantFilesByColor, string $destDir): void
    {
        $models = $item['models'] ?? [];

        if ($models === []) {
            $varian = VarianProduk::create([
                'produk_id' => $produk->id,
                'ukuran' => 'All Size',
                'warna' => 'Default',
                'kode_warna' => null,
                'sku' => 'SHP-'.$itemId.'-0',
                'stok' => 10,
                'penyesuaian_harga' => 0,
                'shopee_model_id' => null,
            ]);

            $this->attachVariantImages($varian, 'default', $variantFilesByColor['__fallback__'] ?? [], $destDir);
            return;
        }

        foreach ($models as $model) {
            $modelId = (int) ($model['model_id'] ?? 0);
            [$warna, $ukuran] = $this->parseModelName((string) ($model['name'] ?? 'Default'));
            $hargaVarian = (int) round((float) ($model['price'] ?? $produk->harga));
            $penyesuaian = $hargaVarian - $produk->harga;

            $varian = VarianProduk::create([
                'produk_id' => $produk->id,
                'ukuran' => $ukuran,
                'warna' => $warna,
                'kode_warna' => null,
                'sku' => 'SHP-'.$itemId.'-'.$modelId,
                'stok' => 10,
                'penyesuaian_harga' => $penyesuaian,
                'shopee_model_id' => $modelId ?: null,
            ]);

            $this->attachVariantImages($varian, $warna, $variantFilesByColor[$warna] ?? [], $destDir);
        }
    }

    private function attachVariantImages(VarianProduk $varian, string $warna, array $files, string $destDir): void
    {
        $urutan = 1;
        foreach ($files as $image) {
            $safeColor = Str::slug($warna) ?: 'variant';
            $filename = 'variant-'.$safeColor.'-'.str_pad((string) $urutan, 2, '0', STR_PAD_LEFT).'.jpg';
            $destination = $destDir.DIRECTORY_SEPARATOR.$filename;
            copy($image['file'], $destination);

            GambarVarianProduk::create([
                'varian_produk_id' => $varian->id,
                'url' => 'produk/'.$varian->produk->slug.'/'.$filename,
                'alt' => $varian->produk->nama.' - '.$warna.' - gambar '.$urutan,
                'urutan' => $urutan,
                'utama' => $urutan === 1,
                'shopee_image_id' => $image['image_id'],
            ]);

            $urutan++;
        }
    }

    private function resolveSourceFolder(string $imagesRoot, int $itemId): ?string
    {
        foreach (glob($imagesRoot.DIRECTORY_SEPARATOR.'*-'.$itemId, GLOB_ONLYDIR) as $dir) {
            return $dir;
        }

        return null;
    }

    private function partitionImageFiles(array $item, ?string $sourceFolder): array
    {
        if (! $sourceFolder) {
            return [[], []];
        }

        $allFiles = glob($sourceFolder.DIRECTORY_SEPARATOR.'image-*.jpg') ?: [];
        sort($allFiles, SORT_NATURAL);

        $imageIds = $item['image_ids'] ?? [];
        $fileByImageId = [];
        foreach ($allFiles as $index => $file) {
            if (isset($imageIds[$index])) {
                $fileByImageId[(string) $imageIds[$index]] = [
                    'file' => $file,
                    'image_id' => (string) $imageIds[$index],
                ];
            }
        }

        $variantImageIds = $item['variant_image_ids'] ?? [];
        $variantFilesByColor = [];
        $usedVariantImageIds = [];
        foreach ($variantImageIds as $color => $imageId) {
            $color = trim((string) $color);
            $imageId = (string) $imageId;
            if ($color === '' || $imageId === '') {
                continue;
            }
            $usedVariantImageIds[] = $imageId;
            if (isset($fileByImageId[$imageId])) {
                $variantFilesByColor[$color] = [$fileByImageId[$imageId]];
            }
        }

        $galleryFiles = [];
        foreach ($imageIds as $index => $imageId) {
            if (in_array($imageId, $usedVariantImageIds, true)) {
                continue;
            }
            if (isset($allFiles[$index])) {
                $galleryFiles[] = [
                    'file' => $allFiles[$index],
                    'image_id' => (string) $imageId,
                ];
            }
        }

        if ($galleryFiles === [] && $allFiles !== []) {
            $galleryFiles[] = [
                'file' => $allFiles[0],
                'image_id' => isset($imageIds[0]) ? (string) $imageIds[0] : null,
            ];
        }

        return [$galleryFiles, $variantFilesByColor];
    }

    private function parseModelName(string $name): array
    {
        $name = trim($name) ?: 'Default';
        $parts = array_map('trim', explode(',', $name));

        if (count($parts) < 2) {
            return [$name, 'All Size'];
        }

        $size = array_pop($parts) ?: 'All Size';
        $color = trim(implode(',', $parts)) ?: 'Default';

        return [$color, $size];
    }

    private function ensureShopCategories(): void
    {
        Kategori::query()->update(['aktif' => false]);

        foreach ([
            ['nama' => 'Abaya', 'slug' => 'abaya', 'urutan' => 1],
            ['nama' => 'Khimar', 'slug' => 'khimar', 'urutan' => 2],
            ['nama' => 'One Set', 'slug' => 'one-set', 'urutan' => 3],
            ['nama' => 'Aksesoris', 'slug' => 'aksesoris', 'urutan' => 4],
        ] as $data) {
            Kategori::query()->updateOrCreate(
                ['slug' => $data['slug']],
                ['nama' => $data['nama'], 'urutan' => $data['urutan'], 'aktif' => true]
            );
        }
    }

    private function resolveKategoriData(string $nama, array $sourceCategories): array
    {
        $nameHaystack = strtolower($nama);
        $sourceHaystack = strtolower(implode(' ', $sourceCategories));

        if (preg_match('/gamis|dress|abaya/i', $nameHaystack)) {
            return ['slug' => 'abaya', 'nama' => 'Abaya', 'urutan' => 1];
        }
        if (preg_match('/khimar|hijab|bergo|pashmina/i', $nameHaystack)) {
            return ['slug' => 'khimar', 'nama' => 'Khimar', 'urutan' => 2];
        }
        if (preg_match('/set|setelan|oneset|one\s*set/i', $nameHaystack)) {
            return ['slug' => 'one-set', 'nama' => 'One Set', 'urutan' => 3];
        }

        if (preg_match('/gamis|dress|abaya/i', $sourceHaystack)) {
            return ['slug' => 'abaya', 'nama' => 'Abaya', 'urutan' => 1];
        }
        if (preg_match('/khimar|hijab|bergo|pashmina/i', $sourceHaystack)) {
            return ['slug' => 'khimar', 'nama' => 'Khimar', 'urutan' => 2];
        }
        if (preg_match('/set|setelan|oneset|one\s*set/i', $sourceHaystack)) {
            return ['slug' => 'one-set', 'nama' => 'One Set', 'urutan' => 3];
        }

        return ['slug' => 'aksesoris', 'nama' => 'Aksesoris', 'urutan' => 4];
    }

    private function getKategoriId(string $slug, ?string $name = null, int $urutan = 99): int
    {
        if (isset($this->categoryCache[$slug])) {
            return $this->categoryCache[$slug]->id;
        }

        $kategori = Kategori::query()->where('slug', $slug)->first();
        if (! $kategori) {
            $kategori = Kategori::create([
                'nama' => $name ?: Str::title(str_replace('-', ' ', $slug)),
                'slug' => $slug,
                'aktif' => true,
                'urutan' => $urutan,
            ]);
        }

        $this->categoryCache[$slug] = $kategori;

        return $kategori->id;
    }

    private function cleanName(string $name): string
    {
        $name = preg_replace('/^Auraquina\s*[.\-]\s*/i', '', $name);
        $name = preg_replace('/\s*\|.*$/', '', $name);
        $name = preg_replace('/\s{2,}/', ' ', $name);
        return trim($name, " .\t\n\r\0\x0B");
    }

    private function shortDesc(string $desc): string
    {
        if ($desc === '') {
            return '';
        }

        $parts = preg_split('/\r?\n\r?\n|\r?\n/', $desc);
        $first = trim((string) ($parts[0] ?? ''));
        return Str::limit($first, 160);
    }

    private function extractMaterial(string $desc): ?string
    {
        if (preg_match('/(?:material|bahan)\s*[:\-]?\s*([^\n\.]+)/i', $desc, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }

    private function uniqueSlug(string $base): string
    {
        $slug = $base;
        $count = 1;
        while (Produk::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$count++;
        }
        return $slug;
    }
}
