<?php

namespace Tests\Feature;

use App\Models\GambarProduk;
use App\Models\GambarVarianProduk;
use App\Models\Kategori;
use App\Models\Produk;
use App\Models\VarianProduk;
use App\Services\ProductImageVariantService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductImageControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_card_image_returns_cached_webp_thumbnail(): void
    {
        config(['filesystems.disks.r2.bucket' => null]);
        Storage::fake('public');

        Storage::disk('public')->put('produk/test/main.jpg', $this->jpegImage(900, 1200));

        $response = $this->get(route('image.product-card', ['path' => 'produk/test/main.jpg']));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'image/webp');
        $this->assertStringContainsString('public', $response->headers->get('Cache-Control', ''));
        $this->assertStringContainsString('max-age=31536000', $response->headers->get('Cache-Control', ''));
        $this->assertStringContainsString('immutable', $response->headers->get('Cache-Control', ''));
        $this->assertFileExists(storage_path('app/public/image-cache/product-cards/'.sha1('produk/test/main.jpg').'.webp'));
    }

    public function test_product_card_image_rejects_remote_or_missing_paths(): void
    {
        config(['filesystems.disks.r2.bucket' => null]);
        Storage::fake('public');

        $this->get(route('image.product-card', ['path' => 'https://example.com/product.jpg']))->assertNotFound();
        $this->get(route('image.product-card', ['path' => 'produk/test/missing.jpg']))->assertNotFound();
    }

    public function test_product_detail_uses_optimized_image_urls_for_gallery_and_color_swatches(): void
    {
        config(['filesystems.disks.r2.bucket' => null]);
        config(['filesystems.disks.public.url' => 'https://cdn.test']);

        $kategori = Kategori::create(['nama' => 'Abaya', 'slug' => 'abaya', 'urutan' => 1, 'aktif' => true]);
        $produk = Produk::create([
            'kategori_id' => $kategori->id,
            'nama' => 'Produk Test',
            'slug' => 'produk-test',
            'deskripsi' => 'Deskripsi produk test',
            'deskripsi_singkat' => 'Singkat',
            'harga' => 100000,
            'sku' => 'TEST-001',
            'berat' => 100,
            'aktif' => true,
            'unggulan' => true,
            'urutan' => 1,
        ]);
        GambarProduk::create(['produk_id' => $produk->id, 'url' => 'produk/test/main.jpg', 'alt' => 'main', 'urutan' => 1, 'utama' => true]);
        $related = Produk::create([
            'kategori_id' => $kategori->id,
            'nama' => 'Produk Terkait Test',
            'slug' => 'produk-terkait-test',
            'deskripsi' => 'Deskripsi produk terkait test',
            'deskripsi_singkat' => 'Singkat',
            'harga' => 120000,
            'sku' => 'TEST-RELATED-001',
            'berat' => 100,
            'aktif' => true,
            'unggulan' => true,
            'urutan' => 2,
        ]);
        GambarProduk::create(['produk_id' => $related->id, 'url' => 'produk/test/related.jpg', 'alt' => 'related', 'urutan' => 1, 'utama' => true]);
        $varian = VarianProduk::create(['produk_id' => $produk->id, 'ukuran' => 'M', 'warna' => 'Ryn', 'kode_warna' => null, 'sku' => 'TEST-RYN-M', 'stok' => 5]);
        GambarVarianProduk::create(['varian_produk_id' => $varian->id, 'url' => 'produk/test/ryn.jpg', 'alt' => 'ryn', 'urutan' => 1, 'utama' => true]);

        $response = $this->get('/shop/produk-test');

        $response->assertOk();
        $response->assertSee('https://cdn.test/products/test/detail/main.webp', false);
        $response->assertSee('https://cdn.test/products/test/swatch/ryn.webp', false);
        $response->assertSee('https://cdn.test/products/test/card/related.webp', false);
        $response->assertDontSee('/image/product-card/produk/test/main.jpg', false);
        $response->assertDontSee('/storage/produk/test/main.jpg', false);
    }

    public function test_search_results_use_optimized_product_image_urls(): void
    {
        config(['filesystems.disks.r2.bucket' => null]);
        config(['filesystems.disks.public.url' => 'https://cdn.test']);

        $kategori = Kategori::create(['nama' => 'Abaya', 'slug' => 'abaya', 'urutan' => 1, 'aktif' => true]);
        $produk = Produk::create([
            'kategori_id' => $kategori->id,
            'nama' => 'Produk Search Test',
            'slug' => 'produk-search-test',
            'deskripsi' => 'Deskripsi produk search test',
            'deskripsi_singkat' => 'Singkat',
            'harga' => 100000,
            'sku' => 'TEST-SEARCH-001',
            'berat' => 100,
            'aktif' => true,
            'unggulan' => true,
            'urutan' => 1,
        ]);
        GambarProduk::create(['produk_id' => $produk->id, 'url' => 'produk/test/search.jpg', 'alt' => 'search', 'urutan' => 1, 'utama' => true]);

        $response = $this->getJson('/api/search?q=Produk');

        $response->assertOk();
        $response->assertJsonPath('items.0.gambar', 'https://cdn.test/products/test/thumb/search.webp');
    }

    public function test_product_image_variant_service_generates_predictable_cdn_urls(): void
    {
        config(['filesystems.disks.r2.bucket' => null]);
        config(['filesystems.disks.public.url' => 'https://cdn.test']);

        $service = app(ProductImageVariantService::class);

        $this->assertSame('https://cdn.test/products/test/thumb/main.webp', $service->url('produk/test/main.jpg', 'thumb'));
        $this->assertSame('https://cdn.test/products/test/swatch/main.webp', $service->url('produk/test/main.jpg', 'swatch'));
        $this->assertSame('https://cdn.test/products/test/card/main.webp', $service->url('produk/test/main.jpg', 'card'));
        $this->assertSame('https://cdn.test/products/test/detail/main.webp', $service->url('produk/test/main.jpg', 'detail'));
        $this->assertSame('https://cdn.test/products/test/zoom/main.webp', $service->url('produk/test/main.jpg', 'zoom'));
        $this->assertSame(
            'https://cdn.test/products/test/card/main.webp 600w, https://cdn.test/products/test/detail/main.webp 1200w',
            $service->srcset('produk/test/main.jpg', ['card' => 600, 'detail' => 1200]),
        );
        $this->assertSame('https://example.com/image.jpg', $service->url('https://example.com/image.jpg', 'card'));
        $this->assertNull($service->url(null, 'card'));
    }

    public function test_regenerate_product_images_command_writes_webp_variants(): void
    {
        config(['filesystems.disks.r2.bucket' => null]);
        Storage::fake('public');

        $kategori = Kategori::create(['nama' => 'Abaya', 'slug' => 'abaya', 'urutan' => 1, 'aktif' => true]);
        $produk = Produk::create([
            'kategori_id' => $kategori->id,
            'nama' => 'Produk Generate Test',
            'slug' => 'produk-generate-test',
            'deskripsi' => 'Deskripsi produk generate test',
            'deskripsi_singkat' => 'Singkat',
            'harga' => 100000,
            'sku' => 'TEST-GENERATE-001',
            'berat' => 100,
            'aktif' => true,
            'unggulan' => true,
            'urutan' => 1,
        ]);
        GambarProduk::create(['produk_id' => $produk->id, 'url' => 'produk/generate/main.jpg', 'alt' => 'main', 'urutan' => 1, 'utama' => true]);
        $varian = VarianProduk::create(['produk_id' => $produk->id, 'ukuran' => 'M', 'warna' => 'Ryn', 'kode_warna' => null, 'sku' => 'TEST-GENERATE-RYN-M', 'stok' => 5]);
        GambarVarianProduk::create(['varian_produk_id' => $varian->id, 'url' => 'produk/generate/variant.jpg', 'alt' => 'variant', 'urutan' => 1, 'utama' => true]);

        Storage::disk('public')->put('produk/generate/main.jpg', $this->jpegImage(900, 1200));
        Storage::disk('public')->put('produk/generate/variant.jpg', $this->jpegImage(900, 1200));

        $this->artisan('images:regenerate-products', ['--product' => 'produk-generate-test'])
            ->assertExitCode(0);

        foreach (['swatch', 'thumb', 'card', 'detail', 'zoom'] as $variant) {
            Storage::disk('public')->assertExists("products/generate/{$variant}/main.webp");
            Storage::disk('public')->assertExists("products/generate/{$variant}/variant.webp");
        }
    }

    public function test_product_image_variants_are_generated_when_product_image_is_saved(): void
    {
        config(['filesystems.disks.r2.bucket' => null]);
        Storage::fake('public');

        $kategori = Kategori::create(['nama' => 'Abaya', 'slug' => 'abaya', 'urutan' => 1, 'aktif' => true]);
        $produk = Produk::create([
            'kategori_id' => $kategori->id,
            'nama' => 'Produk Auto Generate Test',
            'slug' => 'produk-auto-generate-test',
            'deskripsi' => 'Deskripsi produk auto generate test',
            'deskripsi_singkat' => 'Singkat',
            'harga' => 100000,
            'sku' => 'TEST-AUTO-GENERATE-001',
            'berat' => 100,
            'aktif' => true,
            'unggulan' => true,
            'urutan' => 1,
        ]);

        Storage::disk('public')->put('produk/auto/main.jpg', $this->jpegImage(900, 1200));

        GambarProduk::create(['produk_id' => $produk->id, 'url' => 'produk/auto/main.jpg', 'alt' => 'main', 'urutan' => 1, 'utama' => true]);

        foreach (['swatch', 'thumb', 'card', 'detail', 'zoom'] as $variant) {
            Storage::disk('public')->assertExists("products/auto/{$variant}/main.webp");
        }
    }

    public function test_product_image_variants_are_generated_when_variant_image_is_saved(): void
    {
        config(['filesystems.disks.r2.bucket' => null]);
        Storage::fake('public');

        $kategori = Kategori::create(['nama' => 'Abaya', 'slug' => 'abaya', 'urutan' => 1, 'aktif' => true]);
        $produk = Produk::create([
            'kategori_id' => $kategori->id,
            'nama' => 'Produk Auto Variant Test',
            'slug' => 'produk-auto-variant-test',
            'deskripsi' => 'Deskripsi produk auto variant test',
            'deskripsi_singkat' => 'Singkat',
            'harga' => 100000,
            'sku' => 'TEST-AUTO-VARIANT-001',
            'berat' => 100,
            'aktif' => true,
            'unggulan' => true,
            'urutan' => 1,
        ]);
        $varian = VarianProduk::create(['produk_id' => $produk->id, 'ukuran' => 'M', 'warna' => 'Ryn', 'kode_warna' => null, 'sku' => 'TEST-AUTO-VARIANT-RYN-M', 'stok' => 5]);

        Storage::disk('public')->put('produk/auto/variant.jpg', $this->jpegImage(900, 1200));

        GambarVarianProduk::create(['varian_produk_id' => $varian->id, 'url' => 'produk/auto/variant.jpg', 'alt' => 'variant', 'urutan' => 1, 'utama' => true]);

        foreach (['swatch', 'thumb', 'card', 'detail', 'zoom'] as $variant) {
            Storage::disk('public')->assertExists("products/auto/{$variant}/variant.webp");
        }
    }

    public function test_regenerate_product_images_command_skips_missing_images(): void
    {
        config(['filesystems.disks.r2.bucket' => null]);
        Storage::fake('public');

        $kategori = Kategori::create(['nama' => 'Abaya', 'slug' => 'abaya', 'urutan' => 1, 'aktif' => true]);
        $produk = Produk::create([
            'kategori_id' => $kategori->id,
            'nama' => 'Produk Missing Image Test',
            'slug' => 'produk-missing-image-test',
            'deskripsi' => 'Deskripsi produk missing image test',
            'deskripsi_singkat' => 'Singkat',
            'harga' => 100000,
            'sku' => 'TEST-MISSING-001',
            'berat' => 100,
            'aktif' => true,
            'unggulan' => true,
            'urutan' => 1,
        ]);
        GambarProduk::create(['produk_id' => $produk->id, 'url' => 'produk/missing/main.jpg', 'alt' => 'main', 'urutan' => 1, 'utama' => true]);

        $this->artisan('images:regenerate-products', ['--product' => 'produk-missing-image-test'])
            ->assertExitCode(0);

        Storage::disk('public')->assertMissing('products/missing/card/main.webp');
    }

    private function jpegImage(int $width, int $height): string
    {
        $image = imagecreatetruecolor($width, $height);
        $fill = imagecolorallocate($image, 210, 188, 166);
        imagefill($image, 0, 0, $fill);

        ob_start();
        imagejpeg($image, null, 82);
        $contents = (string) ob_get_clean();
        imagedestroy($image);

        return $contents;
    }
}
