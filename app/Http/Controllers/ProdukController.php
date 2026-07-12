<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\Review;
use App\Models\VarianProduk;
use App\Services\ProductImageVariantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProdukController extends Controller
{
    // Halaman shop (semua produk)
    public function index(Request $request)
    {
        $searchTerm = trim((string) $request->input('search', ''));
        $category = trim((string) $request->input('category', ''));
        $sizes = collect($request->input('size', []))->filter()->values();
        $prices = collect($request->input('price', []))->filter()->values();
        $colors = collect($request->input('color', []))->filter()->values();
        $sort = (string) $request->input('sort', 'featured');

        $produks = Produk::with(['gambarUtama', 'kategori', 'varians'])
            ->where('aktif', true)
            ->when($category !== '', function ($query) use ($category) {
                $query->whereHas('kategori', function ($kategoriQuery) use ($category) {
                    $kategoriQuery->where('nama', $category)->orWhere('slug', $category);
                });
            })
            ->when($sizes->isNotEmpty(), function ($query) use ($sizes) {
                $query->whereHas('varians', function ($varianQuery) use ($sizes) {
                    $varianQuery->whereIn('ukuran', $sizes)->where('stok', '>', 0);
                });
            })
            ->when($colors->isNotEmpty(), function ($query) use ($colors) {
                $query->whereHas('varians', function ($varianQuery) use ($colors) {
                    $varianQuery->whereIn('warna', $colors)->where('stok', '>', 0);
                });
            })
            ->when($prices->isNotEmpty(), function ($query) use ($prices) {
                $query->where(function ($priceQuery) use ($prices) {
                    foreach ($prices as $price) {
                        $priceQuery->orWhere(function ($rangeQuery) use ($price) {
                            match ($price) {
                                'under_300' => $rangeQuery->where('harga', '<', 300000),
                                '300_500' => $rangeQuery->whereBetween('harga', [300000, 500000]),
                                'over_500' => $rangeQuery->where('harga', '>', 500000),
                                default => $rangeQuery->whereRaw('1 = 0'),
                            };
                        });
                    }
                });
            });

        $this->applySearch($produks, $searchTerm);

        match ($sort) {
            'newest' => $produks->orderByDesc('created_at'),
            'price_asc' => $produks->orderBy('harga'),
            'price_desc' => $produks->orderByDesc('harga'),
            'name_asc' => $produks->orderBy('nama'),
            default => $produks->orderBy('urutan')->orderByDesc('created_at'),
        };

        $produks = $produks->get();

        $kategoris = Kategori::where('aktif', true)->orderBy('urutan')->get();
        $availableSizes = VarianProduk::where('stok', '>', 0)->distinct()->orderBy('ukuran')->pluck('ukuran');
        $availableColors = VarianProduk::where('stok', '>', 0)->distinct()->orderBy('warna')->pluck('warna');
        $filterState = compact('sizes', 'prices', 'colors', 'sort');
        $produkSaran = $produks->isEmpty()
            ? Produk::with(['gambarUtama', 'kategori'])
                ->where('aktif', true)
                ->where('unggulan', true)
                ->orderBy('urutan')
                ->limit(4)
                ->get()
            : collect();

        return view('shop', compact('produks', 'kategoris', 'availableSizes', 'availableColors', 'searchTerm', 'category', 'produkSaran', 'filterState'));
    }

    public function search(Request $request)
    {
        $searchTerm = trim((string) $request->input('q', ''));

        if (mb_strlen($searchTerm) < 2) {
            return response()->json(['items' => []]);
        }

        $produks = Produk::with(['gambarUtama', 'kategori'])
            ->where('aktif', true);

        $this->applySearch($produks, $searchTerm);

        $items = $produks
            ->orderBy('urutan')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get()
            ->map(fn ($produk) => [
                'id' => $produk->id,
                'nama' => $produk->nama,
                'slug' => $produk->slug,
                'url' => url('/shop/' . $produk->slug),
                'harga' => $produk->hargaFormatted(),
                'kategori' => $produk->kategori?->nama,
                'badge' => $produk->kategori?->nama,
                'excerpt' => $produk->deskripsi_singkat ?: str($produk->deskripsi)->limit(120)->toString(),
                'gambar' => $this->productImageUrl($produk->gambarUtama?->url, 'thumb'),
            ]);

        return response()->json(['items' => $items]);
    }

    private function productImageUrl(?string $path, string $variant): ?string
    {
        return app(ProductImageVariantService::class)->url($path, $variant);
    }

    private function applySearch($query, string $searchTerm): void
    {
        $keywords = collect(preg_split('/\s+/', $searchTerm, -1, PREG_SPLIT_NO_EMPTY))
            ->map(fn ($keyword) => trim($keyword))
            ->filter()
            ->take(5);

        foreach ($keywords as $keyword) {
            $query->where(function ($subQuery) use ($keyword) {
                $subQuery
                    ->where('nama', 'like', "%{$keyword}%")
                    ->orWhere('deskripsi', 'like', "%{$keyword}%")
                    ->orWhere('deskripsi_singkat', 'like', "%{$keyword}%")
                    ->orWhereHas('kategori', function ($kategoriQuery) use ($keyword) {
                        $kategoriQuery->where('nama', 'like', "%{$keyword}%");
                    })
                    ->orWhereHas('varians', function ($varianQuery) use ($keyword) {
                        $varianQuery->where('warna', 'like', "%{$keyword}%");
                    });
            });
        }
    }

    // Halaman detail produk
    public function show(string $slug)
    {
        $produk = Produk::with(['gambars', 'varians.gambarVarians', 'varians.gambarVarianUtama', 'kategori'])
            ->where('slug', $slug)
            ->where('aktif', true)
            ->firstOrFail();

        $reviews = $produk->reviews()
            ->with('user')
            ->where('status', 'approved')
            ->latest()
            ->get();

        $eligibleOrder = auth()->check()
            ? Pesanan::where('user_id', auth()->id())
                ->whereIn('status', [Pesanan::STATUS_DELIVERED, Pesanan::STATUS_COMPLETED])
                ->whereHas('items', fn ($query) => $query->where('produk_id', $produk->id))
                ->latest('id')
                ->first()
            : null;

        $existingReview = auth()->check()
            ? $produk->reviews()->where('user_id', auth()->id())->first()
            : null;

        // Produk terkait (kategori sama, exclude current)
        $terkait = Produk::with(['gambarUtama'])
            ->where('kategori_id', $produk->kategori_id)
            ->where('id', '!=', $produk->id)
            ->where('aktif', true)
            ->limit(4)
            ->get();

        // Kalau produk terkait kurang dari 4, ambil dari kategori lain
        if ($terkait->count() < 4) {
            $tambahan = Produk::with(['gambarUtama'])
                ->where('id', '!=', $produk->id)
                ->whereNotIn('id', $terkait->pluck('id'))
                ->where('aktif', true)
                ->limit(4 - $terkait->count())
                ->get();
            $terkait = $terkait->merge($tambahan);
        }

        $kategoris = Kategori::where('aktif', true)->orderBy('urutan')->get();

        return view('product-detail', compact('produk', 'terkait', 'kategoris', 'reviews', 'eligibleOrder', 'existingReview'));
    }

    // Homepage
    public function home()
    {
        $produkUnggulan = Produk::with(['gambarUtama', 'varians'])
            ->where('aktif', true)
            ->where('unggulan', true)
            ->orderBy('urutan')
            ->limit(8)
            ->get();

        if ($produkUnggulan->isEmpty()) {
            $produkUnggulan = Produk::with(['gambarUtama', 'varians'])
                ->where('aktif', true)
                ->orderBy('urutan')
                ->limit(8)
                ->get();
        }

        $kategoris = Kategori::where('aktif', true)->orderBy('urutan')->get();

        return view('index', compact('produkUnggulan', 'kategoris'));
    }

    public function storeReview(Request $request, string $slug)
    {
        $produk = Produk::where('slug', $slug)->where('aktif', true)->firstOrFail();

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string|min:20|max:1000',
            'photos' => 'nullable|array|max:4',
            'photos.*' => 'image|max:3072',
        ]);

        $eligibleOrder = Pesanan::where('user_id', $request->user()->id)
            ->whereIn('status', [Pesanan::STATUS_DELIVERED, Pesanan::STATUS_COMPLETED])
            ->whereHas('items', fn ($query) => $query->where('produk_id', $produk->id))
            ->latest('id')
            ->first();

        abort_unless($eligibleOrder, 403);

        $photos = collect($request->file('photos', []))
            ->map(fn ($photo) => $photo->store('reviews', 'public'))
            ->values()
            ->all();

        Review::updateOrCreate(
            [
                'produk_id' => $produk->id,
                'user_id' => $request->user()->id,
            ],
            [
                'pesanan_id' => $eligibleOrder->id,
                'rating' => $validated['rating'],
                'review' => trim($validated['review']),
                'photos' => $photos,
                'status' => Review::STATUS_PENDING,
            ]
        );

        $produk->forceFill([
            'rating_star' => round((float) $produk->reviews()->where('status', 'approved')->avg('rating'), 2),
        ])->save();

        return back()->with('status', 'Ulasan berhasil dikirim dan sedang ditinjau admin.');
    }
}
