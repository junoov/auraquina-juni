<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchExperienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_api_returns_richer_product_suggestions(): void
    {
        $kategori = Kategori::create([
            'nama' => 'Khimar Premium',
            'slug' => 'khimar-premium',
            'aktif' => true,
        ]);

        Produk::create([
            'kategori_id' => $kategori->id,
            'nama' => 'Khimar Mocha Travel',
            'slug' => 'khimar-mocha-travel',
            'deskripsi_singkat' => 'Khimar travel warna mocha dengan bahan adem.',
            'harga' => 189000,
            'sku' => 'KHM-MOCHA',
            'aktif' => true,
        ]);

        $this->getJson(route('produk.search', ['q' => 'mocha']))
            ->assertOk()
            ->assertJsonPath('items.0.nama', 'Khimar Mocha Travel')
            ->assertJsonPath('items.0.badge', 'Khimar Premium')
            ->assertJsonPath('items.0.excerpt', 'Khimar travel warna mocha dengan bahan adem.');
    }

    public function test_search_overlay_contains_guided_states(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Pencarian Populer')
            ->assertSee('Terakhir dicari')
            ->assertSee('Coba: abaya mocha, khimar jersey, warna sage');
    }
}
