<?php

namespace Tests\Feature;

use App\Models\ItemPesanan;
use App\Models\Kategori;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_eligible_customer_can_create_or_update_review(): void
    {
        $user = User::factory()->create();
        $produk = $this->createProduct();
        $pesanan = $this->createDeliveredOrder($user);

        ItemPesanan::create([
            'pesanan_id' => $pesanan->id,
            'produk_id' => $produk->id,
            'varian_id' => null,
            'nama_produk' => $produk->nama,
            'varian_label' => 'Default',
            'harga' => $produk->harga,
            'jumlah' => 1,
        ]);

        $this->actingAs($user)
            ->from(route('produk.detail', $produk->slug))
            ->post(route('produk.reviews.store', $produk->slug), [
                'rating' => 5,
                'review' => 'Produk sangat nyaman dipakai, jahitan rapi, dan warna sesuai foto yang ditampilkan.',
            ])
            ->assertRedirect(route('produk.detail', $produk->slug));

        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'produk_id' => $produk->id,
            'rating' => 5,
            'status' => 'approved',
        ]);

        $this->actingAs($user)
            ->from(route('produk.detail', $produk->slug))
            ->post(route('produk.reviews.store', $produk->slug), [
                'rating' => 4,
                'review' => 'Setelah dicoba lagi tetap bagus, hanya ukuran terasa sedikit lebih longgar dari ekspektasi.',
            ])
            ->assertRedirect(route('produk.detail', $produk->slug))
            ->assertSessionHas('error');

        $this->assertSame(1, Review::count());
        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'produk_id' => $produk->id,
            'rating' => 5,
        ]);
    }

    public function test_eligible_customer_can_upload_review_photos(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $produk = $this->createProduct();
        $pesanan = $this->createDeliveredOrder($user);

        ItemPesanan::create([
            'pesanan_id' => $pesanan->id,
            'produk_id' => $produk->id,
            'varian_id' => null,
            'nama_produk' => $produk->nama,
            'varian_label' => 'Default',
            'harga' => $produk->harga,
            'jumlah' => 1,
        ]);

        $this->actingAs($user)
            ->from(route('produk.detail', $produk->slug))
            ->post(route('produk.reviews.store', $produk->slug), [
                'rating' => 5,
                'review' => 'Bahannya jatuh dengan cantik dan foto ini menunjukkan warna aslinya.',
                'photos' => [UploadedFile::fake()->image('review.jpg', 900, 1200)],
            ])
            ->assertRedirect(route('produk.detail', $produk->slug));

        $review = Review::firstOrFail();

        $this->assertCount(1, $review->photos);
        Storage::disk('public')->assertExists($review->photos[0]);
    }


    public function test_product_detail_displays_review_photos(): void
    {
        $user = User::factory()->create();
        $produk = $this->createProduct();

        Review::create([
            'produk_id' => $produk->id,
            'user_id' => $user->id,
            'pesanan_id' => $this->createDeliveredOrder($user)->id,
            'rating' => 5,
            'review' => 'Foto pelanggan memperlihatkan warna produk dengan jelas.',
            'photos' => ['reviews/sample.jpg'],
            'status' => Review::STATUS_APPROVED,
        ]);

        $this->get(route('produk.detail', $produk->slug))
            ->assertOk()
            ->assertSee('Foto dari pelanggan')
            ->assertSee('reviews/sample.jpg');
    }

    public function test_ineligible_customer_cannot_review_product(): void
    {
        $user = User::factory()->create();
        $produk = $this->createProduct();

        $this->actingAs($user)
            ->post(route('produk.reviews.store', $produk->slug), [
                'rating' => 5,
                'review' => 'Ulasan ini seharusnya ditolak karena belum ada pesanan yang selesai untuk produk terkait.',
            ])
            ->assertForbidden();

        $this->assertSame(0, Review::count());
    }

    private function createProduct(): Produk
    {
        $suffix = str_replace('.', '', uniqid('', true));
        $kategori = Kategori::create([
            'nama' => 'Kategori '.$suffix,
            'slug' => 'kategori-'.$suffix,
            'aktif' => true,
        ]);

        return Produk::create([
            'kategori_id' => $kategori->id,
            'nama' => 'Produk '.$suffix,
            'slug' => 'produk-'.$suffix,
            'harga' => 199000,
            'sku' => 'PRD-'.$suffix,
            'aktif' => true,
        ]);
    }

    private function createDeliveredOrder(User $user): Pesanan
    {
        return Pesanan::create([
            'kode_pesanan' => Pesanan::generateKode(),
            'session_id' => 'test-session-'.$user->id,
            'user_id' => $user->id,
            'status' => Pesanan::STATUS_DELIVERED,
            'nama_penerima' => $user->name,
            'telepon' => '081234567890',
            'kota' => 'Malang',
            'alamat_lengkap' => 'Jl. Tenang No. 1',
            'metode_pengiriman' => 'JNE Reguler',
            'metode_pembayaran' => 'QRIS',
            'subtotal' => 199000,
            'ongkir' => 11500,
            'diskon' => 0,
            'total' => 210500,
            'batas_bayar' => now()->addHour(),
        ]);
    }
}
