<?php

namespace App\Console\Commands;

use App\Models\GambarProduk;
use App\Models\GambarVarianProduk;
use App\Models\Produk;
use App\Services\ProductImageVariantService;
use Illuminate\Console\Command;

class RegenerateProductImages extends Command
{
    protected $signature = 'images:regenerate-products
        {--product= : Product slug to regenerate}
        {--path= : Generate variants for one source image path}
        {--from-r2-prefix= : Generate variants for original image paths under this storage prefix}
        {--force : Overwrite existing variants}';

    protected $description = 'Generate optimized WebP image variants for product images';

    public function handle(ProductImageVariantService $images): int
    {
        $productSlug = $this->option('product');
        $path = $this->option('path');
        $prefix = $this->option('from-r2-prefix');
        $force = (bool) $this->option('force');

        if ($path) {
            $paths = collect([$path]);

            return $this->generatePaths($paths, $images, $force);
        }

        if ($prefix) {
            $paths = collect(\Illuminate\Support\Facades\Storage::disk($images->diskName())->allFiles($prefix))
                ->reject(fn (string $file) => str_starts_with($file, 'products/'))
                ->filter(fn (string $file) => preg_match('/\.(jpe?g|png|webp)$/i', $file))
                ->values();

            return $this->generatePaths($paths, $images, $force);
        }

        $productIds = Produk::query()
            ->when($productSlug, fn ($query) => $query->where('slug', $productSlug))
            ->pluck('id');

        if ($productIds->isEmpty()) {
            $this->warn('No products matched.');
            return self::SUCCESS;
        }

        $paths = GambarProduk::query()
            ->whereIn('produk_id', $productIds)
            ->pluck('url')
            ->merge(
                GambarVarianProduk::query()
                    ->whereHas('varian', fn ($query) => $query->whereIn('produk_id', $productIds))
                    ->pluck('url')
            )
            ->filter()
            ->unique()
            ->values();

        return $this->generatePaths($paths, $images, $force);
    }

    private function generatePaths($paths, ProductImageVariantService $images, bool $force): int
    {
        $written = 0;

        $this->withProgressBar($paths, function (string $path) use ($images, $force, &$written) {
            $written += $images->generate($path, $force);
        });

        $this->newLine();

        $this->info("Processed {$paths->count()} source images and generated {$written} product image variants.");

        return self::SUCCESS;
    }
}
