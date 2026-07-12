<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Auraquina - Detail Produk</title>
    <meta name="description" content="Detail produk Auraquina" />
    <link rel="icon" href="{{ asset('images/logo.png') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,500&family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>
  <body class="min-h-screen overflow-x-hidden bg-[var(--warm)] text-[var(--text)] antialiased [text-rendering:geometricPrecision]">
    @php
      $containerClass = 'mx-auto w-[min(1184px,calc(100vw-32px))] max-lg:w-[calc(100vw-28px)]';

	      $imageVariants = app(\App\Services\ProductImageVariantService::class);
	      $productImageUrl = fn (?string $path, string $variant = 'detail') => $imageVariants->url($path, $variant);
	      $productImageSrcset = fn (?string $path) => $imageVariants->srcset($path, ['card' => 600, 'detail' => 1200]);
	      $productThumbUrl = fn (?string $url) => $url ? str_replace('/detail/', '/thumb/', $url) : '';
	      $images = $produk->gambars->pluck('url')->map(fn ($path) => $productImageUrl($path, 'detail'))->filter()->values()->toArray();
      $sizes = $produk->varians->pluck('ukuran')->unique()->values()->toArray();
      $variantGalleries = $produk->varians->mapWithKeys(fn($varian) => [
          $varian->id => $varian->gambarVarians->pluck('url')->map(fn ($path) => $productImageUrl($path, 'detail'))->filter()->values()->toArray(),
      ])->toArray();
      $colors = $produk->varians->unique('warna')->map(fn($v) => [
          'name' => $v->warna,
          'hex' => $v->kode_warna,
          'image' => $productImageUrl(
              $produk->varians
                  ->where('warna', $v->warna)
	                  ->flatMap(fn ($varian) => $varian->gambarVarians->pluck('url'))
	                  ->filter()
	                  ->first(),
	              'swatch'
	          ),
	      ])->values()->toArray();
      $defaultVarian = $produk->varians->firstWhere('warna', $colors[0]['name'] ?? null) ?? $produk->varians->first();
      $initialVariantImages = $defaultVarian ? ($variantGalleries[$defaultVarian->id] ?? []) : [];
      $initialImages = array_values(array_unique(array_merge($initialVariantImages, $images)));
      $initialImageIndex = 0;
      $blankImage = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';
      $approvedReviews = $reviews ?? collect();
      $ratingCount = $approvedReviews->count();
      $ratingAverage = $ratingCount > 0
          ? round((float) $approvedReviews->avg('rating'), 1)
          : ($produk->rating_star ? round((float) $produk->rating_star, 1) : 0);
      $filledStars = (int) round($ratingAverage);
    @endphp

    @include('components.site-header', ['kategoris' => $kategoris, 'backHref' => '/shop'])

    <main style="max-width:1184px;margin:0 auto;padding:100px 16px 0;">
      @if (session('status'))
        <div style="margin-bottom:20px;border:1px solid #D1E7DD;background:#ECFDF3;color:#166534;border-radius:6px;padding:12px 16px;font-size:13px;font-weight:700;">{{ session('status') }}</div>
      @endif

      @if ($errors->any())
        <div style="margin-bottom:20px;border:1px solid #FECACA;background:#FEF2F2;color:#B91C1C;border-radius:6px;padding:12px 16px;font-size:13px;font-weight:700;">{{ $errors->first() }}</div>
      @endif

      {{-- Product Section --}}
      <div id="product-grid" style="display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1fr);gap:28px;padding-bottom:64px;align-items:start;">
        {{-- Main Image + Thumbnails below (desktop) --}}
        <div class="product-main-img" style="min-width:0;">
          <div class="product-main-img__zoom" style="position:relative;overflow:hidden;background:#F5F0EA;border-radius:4px;max-width:600px;margin:0 auto;min-height:520px;display:flex;align-items:center;justify-content:center;">
            <img id="main-img" src="{{ $initialImages[$initialImageIndex] ?? $initialImages[0] ?? '' }}" alt="{{ $produk->nama }}" loading="eager" fetchpriority="high" decoding="async" style="width:100%;max-height:740px;height:auto;object-fit:contain;display:block;" />
            <div id="main-img-error" style="display:none;position:absolute;inset:0;align-items:center;justify-content:center;text-align:center;padding:28px;color:#83513D;font-size:13px;line-height:1.7;background:#F5F0EA;">
              Gambar produk belum bisa dimuat dari R2. Coba refresh halaman atau buka ulang beberapa saat lagi.
            </div>
            <button type="button" onclick="stepImage(-1)" aria-label="Gambar sebelumnya" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);width:42px;height:42px;border:0;border-radius:999px;background:rgba(255,255,255,0.86);color:#201916;box-shadow:0 6px 18px rgba(32,25,22,0.14);cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background 0.15s;" onmouseover="this.style.background='rgba(255,255,255,0.96)'" onmouseout="this.style.background='rgba(255,255,255,0.86)'"><svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg></button>
            <button type="button" onclick="stepImage(1)" aria-label="Gambar berikutnya" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);width:42px;height:42px;border:0;border-radius:999px;background:rgba(255,255,255,0.86);color:#201916;box-shadow:0 6px 18px rgba(32,25,22,0.14);cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background 0.15s;" onmouseover="this.style.background='rgba(255,255,255,0.96)'" onmouseout="this.style.background='rgba(255,255,255,0.86)'"><svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg></button>
          </div>
          {{-- Thumbnails below main image --}}
	          <div class="product-thumbs" style="display:flex;flex-wrap:wrap;gap:6px;margin-top:12px;">
	            @foreach ($initialImages as $i => $img)
	              <button type="button" onclick="switchImage({{ $i }})" style="width:56px;height:56px;border:2px solid {{ $i === $initialImageIndex ? '#83513D' : 'rgba(211,192,172,0.58)' }};border-radius:4px;overflow:hidden;cursor:pointer;padding:0;background:none;flex-shrink:0;">
	                <img src="{{ $productThumbUrl($img) }}" loading="eager" fetchpriority="low" decoding="async" alt="" width="56" height="56" style="width:100%;height:100%;object-fit:cover;display:block;background:#F5F0EA;" />
	              </button>
	            @endforeach
	          </div>
        </div>

        {{-- Mobile Gallery (hidden on desktop) --}}
        <div class="product-mobile-gallery" style="display:none;">
          <div id="mobile-gallery" style="display:flex;overflow-x:auto;scroll-snap-type:x mandatory;-webkit-overflow-scrolling:touch;width:100%;">
            @foreach ($initialImages as $i => $img)
              <div class="product-mobile-gallery__slide" style="width:100%;min-width:100%;max-width:100%;flex:0 0 100%;scroll-snap-align:start;flex-shrink:0;">
                <img src="{{ $i === $initialImageIndex ? $img : $blankImage }}" @if ($i !== $initialImageIndex) data-src="{{ $img }}" @endif loading="lazy" decoding="async" alt="{{ $produk->nama }}" style="width:100%;max-width:100%;height:auto;object-fit:contain;object-position:center;display:block;background:#F5F0EA;" />
              </div>
            @endforeach
          </div>
          <div id="mobile-dots" style="display:flex;justify-content:center;gap:6px;padding:12px 0;">
            @foreach ($initialImages as $i => $img)
              <span class="dot" style="width:{{ $i === 0 ? '20px' : '7px' }};height:7px;border-radius:4px;background:{{ $i === 0 ? '#83513D' : '#D3C0AC' }};transition:all 0.2s;"></span>
            @endforeach
          </div>
        </div>

        {{-- Product Info --}}
        <div class="product-info" style="padding-left:32px;min-width:0;">
          <p style="font-size:10px;text-transform:uppercase;letter-spacing:0.18em;color:#D3C0AC;margin-bottom:6px;font-weight:700;">AURAQUINA</p>
          <h1 style="font-size:30px;font-weight:500;line-height:1.15;margin-bottom:10px;color:#201916;font-family:'Plus Jakarta Sans',system-ui,sans-serif;letter-spacing:-0.01em;">{{ $produk->nama }}</h1>
          <div style="display:flex;align-items:center;gap:4px;margin-bottom:14px;">
            @for ($i = 0; $i < 5; $i++)
              <svg viewBox="0 0 20 20" style="width:13px;height:13px;fill:{{ $i < $filledStars ? '#83513D' : '#D3C0AC' }};"><path d="M10 1l2.39 4.84 5.34.78-3.87 3.77.91 5.32L10 13.27l-4.77 2.51.91-5.32L2.27 6.69l5.34-.78L10 1z"/></svg>
            @endfor
            <span style="font-size:11px;color:#71665d;margin-left:4px;">{{ $ratingCount > 0 ? number_format($ratingAverage, 1) . ' · ' . $ratingCount . ' ulasan' : 'Belum ada ulasan' }}</span>
          </div>
          <p style="font-size:22px;font-weight:600;margin-bottom:28px;color:#201916;font-family:'Plus Jakarta Sans',system-ui,sans-serif;letter-spacing:-0.01em;">{{ $produk->hargaFormatted() }}</p>

          {{-- Divider --}}
          <div style="height:1px;background:rgba(211,192,172,0.4);margin-bottom:24px;"></div>

          {{-- Size --}}
          <div style="margin-bottom:22px;">
            <p style="font-size:13px;margin-bottom:10px;color:#71665d;">Ukuran: <strong id="sel-size" style="color:#201916;">{{ $sizes[0] ?? '' }}</strong></p>
            <div data-size-options style="display:flex;gap:8px;flex-wrap:wrap;">
              @foreach ($sizes as $i => $size)
                <button type="button" onclick="selectSize(this,'{{ $size }}')" style="min-width:64px;height:40px;padding:0 16px;border:1.5px solid {{ $i === 0 ? '#201916' : 'rgba(211,192,172,0.58)' }};background:{{ $i === 0 ? '#FFFFFF' : '#FFFFFF' }};color:#201916;font-size:13px;font-weight:{{ $i === 0 ? '700' : '400' }};cursor:pointer;border-radius:4px;display:flex;align-items:center;justify-content:center;transition:all 0.15s;">{{ $size }}</button>
              @endforeach
            </div>
            <button type="button" onclick="openSizeChartModal()" style="display:inline-flex;align-items:center;gap:4px;margin-top:10px;font-size:12px;color:#83513D;text-decoration:none;background:none;border:none;padding:0;cursor:pointer;font-family:inherit;">Tabel Ukuran <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:none;stroke:currentColor;stroke-width:1.8;"><path d="M9 6l6 6-6 6"/></svg></button>
          </div>

          {{-- Color --}}
          <div style="margin-bottom:28px;">
            <p style="font-size:13px;margin-bottom:10px;color:#71665d;">Warna: <strong id="sel-color" style="color:#201916;">{{ $colors[0]['name'] ?? '' }}</strong></p>
	            <div data-color-options style="display:flex;gap:10px;flex-wrap:wrap;max-width:100%;">
	              @foreach ($colors as $i => $color)
	                @php
	                  $fallbackHue = crc32($color['name']) % 360;
	                  $fallbackBackground = $color['hex']
	                      ?: "linear-gradient(135deg, hsl({$fallbackHue}, 28%, 92%), hsl({$fallbackHue}, 24%, 78%))";
	                  $fallbackLabel = mb_strtoupper(mb_substr($color['name'], 0, 1));
	                @endphp
	                <button type="button" title="{{ $color['name'] }}" data-color-name="{{ $color['name'] }}" onclick="selectColor(this,'{{ $color['name'] }}')" style="width:40px;height:40px;padding:0;border-radius:999px;border:1.5px solid {{ $i === 0 ? '#83513D' : 'rgba(211,192,172,0.58)' }};cursor:pointer;background:#FFFFFF;transition:border-color 0.15s, transform 0.15s;overflow:hidden;flex-shrink:0;">
	                  @if (! empty($color['image']))
	                    <img src="{{ $color['image'] }}" alt="{{ $color['name'] }}" loading="eager" fetchpriority="low" decoding="async" width="40" height="40" style="width:100%;height:100%;object-fit:cover;display:block;background:#F5F0EA;" />
	                  @else
	                    <span aria-hidden="true" style="display:flex;width:100%;height:100%;align-items:center;justify-content:center;background:{{ $fallbackBackground }};color:#83513D;font-size:10px;font-weight:800;letter-spacing:0.02em;text-transform:uppercase;box-shadow:inset 0 0 0 1px rgba(131,81,61,0.08);">{{ $fallbackLabel }}</span>
	                  @endif
	                </button>
	              @endforeach
	            </div>
          </div>

          {{-- Qty + Cart --}}
          <div style="margin-bottom:32px;">
            <label class="qty-label" for="qty-val">Quantity</label>
            <div class="qty-box">
              <button type="button" onclick="changeQty(-1)" aria-label="Kurangi jumlah">−</button>
              <span id="qty-val">1</span>
              <button type="button" onclick="changeQty(1)" aria-label="Tambah jumlah">+</button>
            </div>
          </div>
          <div style="display:grid;gap:10px;">
            <button type="button" class="action-btn action-btn-outline" onclick="tambahKeKeranjang()">Add to Cart</button>
            <button type="button" class="action-btn action-btn-solid" onclick="beliSekarang()">Buy It Now</button>
          </div>

          {{-- Accordions --}}
          <div style="border-top:1px solid rgba(211,192,172,0.4);">
            <details open>
              <summary style="display:flex;align-items:center;justify-content:space-between;padding:16px 0;font-size:12px;font-weight:700;cursor:pointer;list-style:none;color:#201916;letter-spacing:0.06em;text-transform:uppercase;">Deskripsi<svg viewBox="0 0 24 24" style="width:15px;height:15px;fill:none;stroke:#71665d;stroke-width:1.5;flex-shrink:0;"><path d="m6 9 6 6 6-6"/></svg></summary>
              <p style="padding-bottom:20px;font-size:13px;line-height:1.85;color:#71665d;overflow-wrap:anywhere;text-align:justify;white-space:pre-line;">{{ $produk->deskripsi }}</p>
            </details>
          </div>
        </div>
      </div>

      <section style="max-width:780px;margin:0 auto 56px;padding-top:8px;">
        <div style="display:flex;align-items:end;justify-content:space-between;gap:16px;margin-bottom:22px;flex-wrap:wrap;">
          <div>
            <p style="font-size:10px;text-transform:uppercase;letter-spacing:0.18em;color:#A7745E;margin:0 0 6px;font-weight:700;">Customer Reviews</p>
            <h2 style="margin:0;font-size:28px;color:#201916;font-family:'Plus Jakarta Sans',system-ui,sans-serif;font-weight:500;letter-spacing:-0.02em;">Ulasan Produk</h2>
          </div>
          <div style="font-size:13px;color:#71665d;">{{ $ratingCount > 0 ? number_format($ratingAverage, 1) . ' / 5 dari ' . $ratingCount . ' ulasan' : 'Jadilah yang pertama memberi ulasan' }}</div>
        </div>

        @if (auth()->check() && $eligibleOrder)
          <form method="POST" action="{{ route('produk.reviews.store', $produk->slug) }}" enctype="multipart/form-data" style="margin-bottom:24px;border:1px solid rgba(211,192,172,0.4);border-radius:8px;background:#FFFFFF;padding:18px 18px 16px;">
            @csrf
            <div style="margin-bottom:12px;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#201916;">{{ $existingReview ? 'Perbarui Ulasan Anda' : 'Tulis Ulasan' }}</div>
            <div style="margin-bottom:12px;display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
              <label for="rating" style="font-size:13px;color:#71665d;">Rating</label>
              <select id="rating" name="rating" style="height:40px;border:1.5px solid rgba(211,192,172,0.58);border-radius:6px;padding:0 12px;background:#FFFFFF;color:#201916;font-size:13px;outline:none;" required>
                @for ($i = 5; $i >= 1; $i--)
                  <option value="{{ $i }}" {{ (int) old('rating', $existingReview?->rating) === $i ? 'selected' : '' }}>{{ $i }} Bintang</option>
                @endfor
              </select>
            </div>
            <textarea name="review" rows="4" style="width:100%;border:1.5px solid rgba(211,192,172,0.58);border-radius:6px;padding:12px 14px;background:#FFFFFF;color:#201916;font-size:13px;line-height:1.7;outline:none;resize:vertical;" placeholder="Ceritakan pengalaman Anda memakai produk ini." required>{{ old('review', $existingReview?->review) }}</textarea>
            <label style="display:block;margin-top:12px;">
              <span style="display:block;margin-bottom:8px;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#201916;">Foto dari pelanggan</span>
              <input type="file" name="photos[]" accept="image/*" multiple style="width:100%;border:1.5px dashed rgba(211,192,172,0.8);border-radius:6px;padding:12px;background:#FFFDF9;color:#71665d;font-size:12px;" />
            </label>
            <div style="margin-top:12px;display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;">
              <span style="font-size:12px;color:#71665d;">Hanya pelanggan dengan pesanan diterima/selesai yang bisa memberi ulasan.</span>
              <button type="submit" style="height:42px;padding:0 18px;border:none;border-radius:6px;background:#83513D;color:#FFFFFF;font-size:11px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;cursor:pointer;">Simpan Ulasan</button>
            </div>
          </form>
        @elseif(auth()->check())
          <div style="margin-bottom:24px;border:1px solid rgba(211,192,172,0.4);border-radius:8px;background:#FFFFFF;padding:16px 18px;font-size:13px;color:#71665d;line-height:1.7;">Ulasan tersedia setelah pesanan untuk produk ini berstatus diterima atau selesai.</div>
        @else
          <div style="margin-bottom:24px;border:1px solid rgba(211,192,172,0.4);border-radius:8px;background:#FFFFFF;padding:16px 18px;font-size:13px;color:#71665d;line-height:1.7;">Silakan masuk ke akun Anda untuk menulis ulasan setelah menyelesaikan pembelian.</div>
        @endif

        @if ($approvedReviews->isEmpty())
          <div style="border:1px solid rgba(211,192,172,0.4);border-radius:8px;background:#FFFFFF;padding:18px;font-size:13px;color:#71665d;">Belum ada ulasan untuk produk ini.</div>
        @else
          <div style="display:grid;gap:14px;">
            @foreach ($approvedReviews as $review)
              <article style="border:1px solid rgba(211,192,172,0.4);border-radius:8px;background:#FFFFFF;padding:18px;">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:8px;">
                  <strong style="font-size:14px;color:#201916;">{{ $review->user?->name ?? 'Pelanggan Auraquina' }}</strong>
                  <span style="font-size:12px;color:#71665d;">{{ $review->created_at->translatedFormat('d M Y') }}</span>
                </div>
                <div style="display:flex;gap:4px;margin-bottom:10px;">
                  @for ($i = 0; $i < 5; $i++)
                    <svg viewBox="0 0 20 20" style="width:13px;height:13px;fill:{{ $i < $review->rating ? '#83513D' : '#D3C0AC' }};"><path d="M10 1l2.39 4.84 5.34.78-3.87 3.77.91 5.32L10 13.27l-4.77 2.51.91-5.32L2.27 6.69l5.34-.78L10 1z"/></svg>
                  @endfor
                </div>
                <p style="margin:0;font-size:13px;line-height:1.8;color:#71665d;">{{ $review->review }}</p>
                @if (! empty($review->photos))
                  <div style="margin-top:12px;">
                    <p style="margin:0 0 8px;font-size:11px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#83513D;">Foto dari pelanggan</p>
                    <div style="display:flex;gap:8px;flex-wrap:wrap;">
                      @foreach ($review->photos as $photo)
                        <img src="{{ str_starts_with($photo, 'http') ? $photo : Storage::disk('public')->url($photo) }}" alt="Foto ulasan pelanggan" style="width:74px;height:92px;object-fit:cover;border-radius:6px;background:#F5F0EA;border:1px solid rgba(211,192,172,0.4);" loading="lazy" />
                      @endforeach
                    </div>
                  </div>
                @endif
              </article>
            @endforeach
          </div>
        @endif
      </section>

      {{-- Related Products --}}
      <section class="related-edit" aria-labelledby="related-heading">
        <div class="related-edit__intro">
          <span class="related-edit__eyebrow">Pilihan Lembut</span>
          <h2 id="related-heading">Anda Mungkin Suka</h2>
          <p>Potongan serupa dengan nuansa warna yang tetap kalem untuk dipadukan sehari-hari.</p>
        </div>

        <div class="related-edit__rail" aria-label="Koleksi produk terkait">
          @foreach ($terkait as $item)
            @php
              $relatedImage = $productImageUrl($item->gambarUtama?->url, 'card') ?? '';
              $relatedSrcset = $productImageSrcset($item->gambarUtama?->url);
            @endphp
            <a class="related-edit__item" href="/shop/{{ $item->slug }}">
              <figure class="related-edit__frame">
                <img src="{{ $blankImage }}" data-src="{{ $relatedImage }}" @if ($relatedSrcset) data-srcset="{{ $relatedSrcset }}" sizes="(max-width: 640px) 55vw, 220px" @endif alt="{{ $item->nama }}" loading="lazy" decoding="async" />
              </figure>
              <div class="related-edit__details">
                <span class="related-edit__category">{{ $item->kategori->nama }}</span>
                <span class="related-edit__name">{{ $item->nama }}</span>
                <span class="related-edit__price">{{ $item->hargaFormatted() }}</span>
              </div>
            </a>
          @endforeach
        </div>
      </section>
    </main>

    {{-- Footer --}}
    @include('components.site-footer')

    {{-- WhatsApp FAB --}}
    <a class="fixed right-[22px] bottom-[18px] z-[90] flex h-11 w-11 items-center justify-center rounded-xl bg-[var(--brown)] text-[var(--white)] max-lg:right-3 max-lg:bottom-3" href="https://wa.me/6287711516373" aria-label="WhatsApp">
      <svg aria-hidden="true" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
        <path d="M20.52 3.48A11.93 11.93 0 0 0 12 0C5.37 0 0 5.37 0 12a11.93 11.93 0 0 0 1.64 6.06L0 24l6.16-1.61A11.93 11.93 0 0 0 12 24c6.63 0 12-5.37 12-12 0-3.19-1.25-6.2-3.48-8.52zM12 21.8a9.78 9.78 0 0 1-5-1.37l-.36-.21-3.66.96.98-3.57-.23-.37A9.8 9.8 0 1 1 21.8 12 9.8 9.8 0 0 1 12 21.8zm5.36-7.34c-.29-.15-1.74-.86-2-.96s-.46-.15-.66.15-.76.96-.93 1.16-.34.22-.63.07a8.06 8.06 0 0 1-2.36-1.46 8.86 8.86 0 0 1-1.63-2.04c-.17-.29 0-.45.13-.6s.29-.34.43-.5a2 2 0 0 0 .29-.5.55.55 0 0 0 0-.5c-.07-.15-.66-1.6-.91-2.18s-.48-.5-.66-.5h-.57a1.1 1.1 0 0 0-.8.37 3.36 3.36 0 0 0-1.05 2.5 5.83 5.83 0 0 0 1.22 3.1 13.34 13.34 0 0 0 5.13 4.53c.71.31 1.27.5 1.7.64a4.13 4.13 0 0 0 1.88.12 3.07 3.07 0 0 0 2-1.42 2.5 2.5 0 0 0 .17-1.42c-.07-.12-.27-.2-.56-.34z"/>
      </svg>
    </a>

    <style>
      /* Qty / Action */
      .qty-label {
        display: block;
        margin-bottom: 8px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        color: var(--ink);
      }
      .qty-box {
        display: flex;
        align-items: center;
        width: 120px;
        height: 44px;
        border: 1px solid var(--border);
        border-radius: 2px;
        background: #fff;
      }
      .qty-box button {
        width: 40px;
        height: 100%;
        border: none;
        background: none;
        font-size: 16px;
        color: var(--ink);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color 0.15s;
      }
      .qty-box button:hover { color: var(--brown); }
      .qty-box span {
        flex: 1;
        text-align: center;
        font-size: 14px;
        font-weight: 700;
        color: var(--ink);
        pointer-events: none;
      }
      @media (max-width: 480px) {
        .qty-box { width: 100%; }
        .product-action-buttons { grid-template-columns: 1fr !important; }
      }
      .action-btn {
        display: block;
        width: 100%;
        height: 50px;
        border: 1px solid var(--brown);
        border-radius: 2px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        cursor: pointer;
        transition: all 0.2s;
      }
      .action-btn-outline {
        background: transparent;
        color: var(--brown);
      }
      .action-btn-outline:hover {
        background: var(--brown);
        color: #fff;
      }
      .action-btn-solid {
        background: var(--brown);
        color: #fff;
        border-color: var(--brown);
      }
      .action-btn-solid:hover {
        opacity: 0.92;
      }
      .related-edit {
        position: relative;
        margin: 40px auto 0;
        padding: clamp(40px, 6vw, 64px) 0 clamp(48px, 8vw, 80px);
        border-top: 1px solid rgba(211, 192, 172, 0.4);
        overflow: hidden;
      }

      .related-edit::before {
        content: '';
        position: absolute;
        top: 0;
        left: 50%;
        width: 100%;
        max-width: 1184px;
        height: 1px;
        transform: translateX(-50%);
        background: linear-gradient(90deg, transparent, rgba(131, 81, 61, 0.2), transparent);
      }

      .related-edit__intro {
        max-width: 500px;
        margin: 0 auto 40px;
        text-align: center;
      }

      .related-edit__eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        color: #A7745E;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.25em;
        line-height: 1;
        text-transform: uppercase;
        margin-bottom: 8px;
      }

      .related-edit__eyebrow::before,
      .related-edit__eyebrow::after {
        content: '';
        width: 18px;
        height: 1px;
        background: currentColor;
        opacity: 0.35;
      }

      .related-edit h2 {
        margin: 8px 0 10px;
        color: #201916;
        font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
        font-size: clamp(28px, 4vw, 36px);
        font-weight: 600;
        letter-spacing: -0.02em;
        line-height: 1.1;
      }

      .related-edit__intro p {
        margin: 0;
        color: #8C7E74;
        font-size: 13px;
        line-height: 1.7;
      }

      .related-edit__rail {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        justify-content: center;
        align-items: stretch;
        gap: 24px;
      }

      .related-edit__item {
        color: #201916;
        text-decoration: none;
        background: #FFFFFF;
        border: 1px solid rgba(211, 192, 172, 0.25);
        border-radius: 12px;
        padding: 12px;
        display: flex;
        flex-direction: column;
        transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.4s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.4s ease;
        box-shadow: 0 4px 20px rgba(131, 81, 61, 0.02);
      }

      .related-edit__item:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 38px rgba(131, 81, 61, 0.08);
        border-color: rgba(131, 81, 61, 0.25);
      }

      .related-edit__frame {
        position: relative;
        aspect-ratio: 3/4;
        margin: 0 0 14px;
        overflow: hidden;
        background: #F5F0EA;
        border-radius: 8px;
        transform: translateZ(0);
      }

      .related-edit__frame::after {
        content: '';
        position: absolute;
        inset: 10px;
        border: 1px solid rgba(255, 255, 255, 0.4);
        border-radius: inherit;
        pointer-events: none;
      }

      .related-edit__frame img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
      }

      .related-edit__item:hover .related-edit__frame img {
        transform: scale(1.05);
      }

      .related-edit__details {
        display: flex;
        flex-direction: column;
        flex-grow: 1;
        text-align: left;
        padding: 4px 4px 8px;
      }

      .related-edit__category {
        display: block;
        font-size: 9px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #A7745E;
        margin-bottom: 6px;
      }

      .related-edit__name {
        display: -webkit-box;
        min-height: 36px;
        max-height: 36px;
        overflow: hidden;
        color: #201916;
        font-size: 13px;
        font-weight: 500;
        line-height: 1.4;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
        margin-bottom: 8px;
        transition: color 0.2s ease;
      }

      .related-edit__item:hover .related-edit__name {
        color: #83513D;
      }

      .related-edit__price {
        display: block;
        color: #83513D;
        font-size: 13px;
        font-weight: 700;
        margin-top: auto;
      }

      .product-info [data-color-options] {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(40px, 1fr));
        gap: 10px;
        width: 100%;
        max-width: 460px;
      }

      .product-info [data-color-options] button {
        width: 100%;
        aspect-ratio: 1;
        height: auto;
        transition: transform 0.15s, border-color 0.15s;
      }

      .product-info [data-color-options] button:hover {
        transform: scale(1.06);
      }

      .product-info [data-color-options] button img,
      .product-info [data-color-options] button > span:first-child {
        border-radius: 999px !important;
      }

      .related-mobile { scrollbar-width: none; -ms-overflow-style: none; }
      .related-mobile::-webkit-scrollbar { display: none; }
      #mobile-gallery {
        scrollbar-width: none;
        -ms-overflow-style: none;
        transition: opacity 0.2s ease;
      }
      #mobile-gallery::-webkit-scrollbar { display: none; }
      #mobile-gallery.is-swapping { opacity: 0; }
      .product-mobile-gallery__slide {
        min-height: 0;
        overflow: hidden;
        background: #fff;
        margin: 0 auto;
        flex: 0 0 100%;
        width: 100%;
        max-width: 100%;
      }
      .product-mobile-gallery__slide img {
        width: 100%;
        max-width: 100%;
        height: auto;
        object-fit: contain;
        object-position: center;
      }

      /* Zoom-on-hover untuk gambar produk utama (desktop) */
      .product-main-img__zoom {
        cursor: zoom-in;
      }
      .product-main-img__zoom #main-img {
        transition: transform 0.18s ease-out;
        will-change: transform;
        pointer-events: none;
      }
      .product-main-img__zoom.is-zooming {
        cursor: zoom-out;
      }
      @media (max-width: 1023px) {
        .product-main-img__zoom { cursor: default; }
      }

      @media (max-width: 1023px) {
        main {
          padding: 0 16px 0 !important;
        }
        #product-grid {
          display: flex !important;
          flex-direction: column !important;
          gap: 0 !important;
          padding-bottom: 40px !important;
        }
        .product-thumbs { display: none !important; }
        .product-main-img { display: none !important; }
        .product-mobile-gallery { display: block !important; }
        .product-mobile-gallery {
          position: relative;
          background: #F5F0EA;
          margin-left: -16px;
          margin-right: -16px;
        }
        #mobile-gallery {
          background: #F5F0EA;
        }
        #mobile-dots {
          position: absolute;
          right: 0;
          bottom: 12px;
          left: 0;
          padding: 0 !important;
          pointer-events: none;
        }
        #mobile-dots .dot {
          box-shadow: 0 1px 6px rgba(32, 25, 22, 0.16);
        }
        .product-info {
          position: relative;
          z-index: 2;
          margin-top: 0;
          padding: 24px 18px 0 !important;
          border-radius: 18px 18px 0 0;
          background: var(--warm);
          box-shadow: 0 -4px 20px rgba(32, 25, 22, 0.04);
        }
        .product-info > p:first-child {
          margin-bottom: 8px !important;
          color: #A7745E !important;
        }
        .product-info h1 {
          max-width: 310px;
          font-size: 25px !important;
          line-height: 1.08 !important;
          margin-bottom: 11px !important;
        }
        .product-info h1 + div {
          margin-bottom: 16px !important;
        }
        .product-info h1 + div + p {
          font-size: 24px !important;
          margin-bottom: 24px !important;
        }
        .product-info [data-size-options] button,
        .product-info [data-color-options] button {
          min-height: 44px;
        }
        .product-info [data-color-options] {
          grid-template-columns: repeat(auto-fill, minmax(38px, 1fr));
          gap: 8px !important;
        }
        .product-info [data-color-options] button {
          height: auto !important;
          padding: 0 !important;
        }
        .product-info details summary {
          min-height: 48px;
        }
        .related-edit {
          margin-top: 0;
          padding: 34px 0 52px 14px;
        }
        .related-edit__intro {
          margin-right: 14px;
          margin-bottom: 22px;
        }
        .related-edit__intro p {
          display: none;
        }
        .related-edit__rail {
          display: flex;
          justify-content: flex-start;
          gap: 16px;
          overflow-x: auto;
          padding: 0 14px 10px 0;
          scroll-snap-type: x mandatory;
          -webkit-overflow-scrolling: touch;
          scrollbar-width: none;
        }
        .related-edit__rail::-webkit-scrollbar {
          display: none;
        }
        .related-edit__item {
          min-width: 60vw;
          max-width: 60vw;
          scroll-snap-align: start;
        }
      }
      .product-action-buttons {
        grid-template-columns: 1fr 1fr !important;
      }
      .product-action-buttons button {
        height: 46px !important;
        font-size: 10px !important;
      }
      @media (min-width: 1024px) {
        .product-mobile-gallery { display: none !important; }
        .related-mobile { display: none !important; }
      }
    </style>

    <script>
      if ('scrollRestoration' in history) history.scrollRestoration = 'manual';
      window.scrollTo(0, 0);

      const defaultImages = @json($images);
	      const colorOrder = @json(array_column($colors, 'name'));
	      const variantGalleries = @json($variantGalleries);
	      const produkId = {{ $produk->id }};
	      const varians = @json($produk->varians);
	      const blankImage = @json($blankImage);
	      let images = @json($initialImages);
		      let activeImageIndex = {{ $initialImageIndex }};
		      let qty = 1;
		      let selectedSize = '{{ $sizes[0] ?? '' }}';
		      let selectedColor = '{{ $colors[0]['name'] ?? '' }}';
	
	      function variantsForColor(colorName) {
        return varians.filter(v => v.warna === colorName);
      }

      function firstVariantForColor(colorName) {
        return variantsForColor(colorName)[0] || null;
      }

      function variantForSelection(colorName = selectedColor, size = selectedSize) {
        return varians.find(v => v.warna === colorName && v.ukuran === size) || null;
      }

      function ensureValidSizeForColor(colorName) {
        if (variantForSelection(colorName, selectedSize)) {
          return selectedSize;
        }

        const fallbackVariant = firstVariantForColor(colorName);
        selectedSize = fallbackVariant?.ukuran || '';
        return selectedSize;
      }

      function syncSizeButtons() {
        const sizeButtons = document.querySelectorAll('[data-size-options] button');
        const validSizes = new Set(variantsForColor(selectedColor).map((variant) => variant.ukuran));

        sizeButtons.forEach((button) => {
          const size = button.textContent.trim();
          const isAvailable = validSizes.has(size);
          const isSelected = size === selectedSize;

          button.disabled = !isAvailable;
          button.style.borderColor = isSelected ? '#201916' : 'rgba(211,192,172,0.58)';
          button.style.fontWeight = isSelected ? '700' : '400';
          button.style.opacity = isAvailable ? '1' : '0.45';
          button.style.cursor = isAvailable ? 'pointer' : 'not-allowed';
        });
      }

      function getVarianId() {
        return variantForSelection()?.id ?? null;
      }

      function buildPayload() {
        const varianId = getVarianId();
        return {
          produk_id: produkId,
          varian_id: varianId,
          jumlah: qty,
        };
      }

      async function postCommerce(url) {
        const response = await fetch(url, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
          },
          body: JSON.stringify(buildPayload()),
        });

        return response.json();
      }

      function showToast(msg) {
        let t = document.getElementById('aq-toast');
        if (!t) {
          t = document.createElement('div');
          t.id = 'aq-toast';
          t.setAttribute('role', 'status');
          t.setAttribute('aria-live', 'polite');
          t.setAttribute('aria-atomic', 'true');
          t.style.cssText = 'position:fixed;bottom:24px;left:50%;transform:translateX(-50%) translateY(20px);background:var(--brown);color:#fff;padding:10px 20px;border-radius:4px;font-size:12px;font-weight:600;letter-spacing:0.04em;z-index:99999;opacity:0;transition:opacity 0.25s,transform 0.25s;pointer-events:none;font-family:inherit;';
          document.body.appendChild(t);
        }
        t.textContent = msg;
        t.style.opacity = '1';
        t.style.transform = 'translateX(-50%) translateY(0)';
        clearTimeout(t._timer);
        t._timer = setTimeout(() => {
          t.style.opacity = '0';
          t.style.transform = 'translateX(-50%) translateY(20px)';
        }, 2500);
      }

      var _cartOpenTimer = null;

      function tambahKeKeranjang() {
        const btn = document.querySelector('.action-btn-outline');
        const origText = btn?.textContent;

        // Quick visual feedback on button
        if (btn) {
          btn.textContent = 'Added ✓';
          btn.disabled = true;
          btn.style.opacity = '0.7';
        }

        postCommerce('/keranjang/tambah')
          .then(data => {
            if (data.success) {
              window.dispatchEvent(new CustomEvent('cart:changed', { detail: { totalItem: data.total_item } }));

              // Auto-open cart after brief delay (cancellable if user closes manually)
              clearTimeout(_cartOpenTimer);
              _cartOpenTimer = setTimeout(() => {
                if (typeof openCart === 'function') openCart();
              }, 600);

              // Reset button after cart opens
              setTimeout(() => {
                if (btn) {
                  btn.textContent = origText;
                  btn.disabled = false;
                  btn.style.opacity = '';
                }
              }, 1500);
              return;
            }

            // Error — reset button immediately
            if (btn) {
              btn.textContent = origText;
              btn.disabled = false;
              btn.style.opacity = '';
            }
            showToast(data.error || 'Gagal menambahkan ke keranjang');
          })
          .catch(() => {
            if (btn) {
              btn.textContent = origText;
              btn.disabled = false;
              btn.style.opacity = '';
            }
            showToast('Terjadi kesalahan, coba lagi');
          });
      }

      function beliSekarang() {
        const btn = document.querySelector('.action-btn-solid');
        const origText = btn?.textContent;

        if (btn) {
          btn.textContent = 'Processing...';
          btn.disabled = true;
          btn.style.opacity = '0.7';
        }

        postCommerce('/checkout/buy-now')
          .then(data => {
            if (data.success && data.redirect) {
              window.location.href = data.redirect;
              return;
            }

            if (btn) {
              btn.textContent = origText;
              btn.disabled = false;
              btn.style.opacity = '';
            }
            showToast(data.error || 'Checkout gagal diproses');
          })
          .catch(() => {
            if (btn) {
              btn.textContent = origText;
              btn.disabled = false;
              btn.style.opacity = '';
            }
            showToast('Terjadi kesalahan, coba lagi');
          });
      }

      function switchImage(i) {
        if (!images.length) return;

        activeImageIndex = ((i % images.length) + images.length) % images.length;

        const thumbButtons = document.querySelectorAll('.product-thumbs button');
        hydrateDeferredImages(thumbButtons[activeImageIndex]);
        updateMainImage(images[activeImageIndex]);

        thumbButtons.forEach((button, idx) => {
          button.style.borderColor = idx === activeImageIndex ? '#83513D' : 'rgba(211,192,172,0.58)';
        });

        const mobileGallery = document.getElementById('mobile-gallery');
        if (mobileGallery) {
          mobileGallery.scrollTo({ left: mobileGallery.clientWidth * activeImageIndex, behavior: 'smooth' });
        }

        document.querySelectorAll('#mobile-dots .dot').forEach((dot, idx) => {
          dot.style.width = idx === activeImageIndex ? '20px' : '7px';
          dot.style.background = idx === activeImageIndex ? '#83513D' : '#D3C0AC';
        });
      }

      function activeVariantImages() {
        const activeVariant = variantForSelection(selectedColor, selectedSize) || firstVariantForColor(selectedColor);
        return activeVariant ? [...new Set((variantGalleries[activeVariant.id] || []).filter(Boolean))] : [];
      }

      function navigableImageCount() {
        return images.length;
      }

      function stepImage(direction) {
        if (!images.length) return;

        const maxNavigableIndex = navigableImageCount() - 1;
        const effectiveIndex = Math.min(activeImageIndex, maxNavigableIndex);
        let nextIndex = effectiveIndex + direction;

        if (nextIndex > maxNavigableIndex) {
          if (stepColor(direction)) {
            return;
          }

          nextIndex = 0;
        } else if (nextIndex < 0) {
          if (stepColor(direction)) {
            return;
          }

          nextIndex = maxNavigableIndex;
        }

        switchImage(nextIndex);
      }

	      function galleryForVariant(varianId, includeDefaultImages = true) {
	        const variantImages = variantGalleries[varianId] || [];
	        const merged = includeDefaultImages ? [...variantImages, ...defaultImages] : [...variantImages];
	        return [...new Set(merged)].filter(Boolean);
	      }

      function thumbForImage(src) {
        return src ? src.replace('/detail/', '/thumb/') : '';
      }

      function hydrateDeferredImages(root = document) {
        if (!root) return;

        root.querySelectorAll('img[data-src]').forEach((img) => {
          if (img.dataset.srcset) {
            img.srcset = img.dataset.srcset;
            img.removeAttribute('data-srcset');
          }
          img.src = img.dataset.src;
          img.removeAttribute('data-src');
        });
      }

      function hydrateSoon(root = document, delay = 0) {
        if (!root) return;

        const run = () => hydrateDeferredImages(root);
        window.setTimeout(() => {
          if ('requestIdleCallback' in window) {
            requestIdleCallback(run, { timeout: 1600 });
            return;
          }

          run();
        }, delay);
      }

      function observeDeferredImages(selector) {
        const imagesToObserve = document.querySelectorAll(selector);
        if (!imagesToObserve.length) return;

        if (!('IntersectionObserver' in window)) {
          imagesToObserve.forEach((img) => hydrateDeferredImages(img.parentElement || document));
          return;
        }

        const observer = new IntersectionObserver((entries) => {
          entries.forEach((entry) => {
            if (!entry.isIntersecting) return;

            const img = entry.target;
            if (img.dataset.srcset) {
              img.srcset = img.dataset.srcset;
              img.removeAttribute('data-srcset');
            }
            if (img.dataset.src) {
              img.src = img.dataset.src;
              img.removeAttribute('data-src');
            }
            observer.unobserve(img);
          });
        }, { rootMargin: '300px 0px' });

        imagesToObserve.forEach((img) => observer.observe(img));
      }

      function renderGallery(nextImages, startIndex = 0) {
        images = nextImages.length ? nextImages : defaultImages;
        activeImageIndex = startIndex;

        updateMainImage(images[startIndex] || '');

	        const thumbs = document.querySelector('.product-thumbs');
	        if (thumbs) {
		          thumbs.innerHTML = images.map((img, i) => `
		            <button type="button" onclick="switchImage(${i})" style="width:56px;height:56px;border:2px solid ${i === startIndex ? '#83513D' : 'rgba(211,192,172,0.58)'};border-radius:4px;overflow:hidden;cursor:pointer;padding:0;background:none;flex-shrink:0;">
			              <img src="${thumbForImage(img)}" loading="eager" fetchpriority="low" decoding="async" alt="" width="56" height="56" style="width:100%;height:100%;object-fit:cover;display:block;background:#F5F0EA;" />
		            </button>
		          `).join('');
	        }

        const mobileGallery = document.getElementById('mobile-gallery');
        if (mobileGallery) {
          // Ganti warna di mobile: crossfade halus, bukan lompat instan.
          const swapToken = (mobileGallery._swapToken || 0) + 1;
          mobileGallery._swapToken = swapToken;
          const isSwap = images.length && mobileGallery.children.length;

		          const doSwap = () => {
		            mobileGallery.innerHTML = images.map((img) => `
		              <div class="product-mobile-gallery__slide" style="width:100%;min-width:100%;max-width:100%;flex:0 0 100%;scroll-snap-align:start;flex-shrink:0;">
			                <img src="${blankImage}" data-src="${img}" loading="lazy" decoding="async" alt="{{ $produk->nama }}" style="width:100%;max-width:100%;height:auto;object-fit:contain;object-position:center;display:block;background:#F5F0EA;" />
		              </div>
		            `).join('');

            if (window.matchMedia('(max-width: 1023px)').matches) {
              hydrateDeferredImages(mobileGallery);
            }

			            // Reset posisi dulu tanpa animasi (masih invisible), lalu fade-in.
		            mobileGallery.scrollTo({ left: mobileGallery.clientWidth * startIndex, behavior: 'auto' });
		            mobileGallery.classList.remove('is-swapping');
		          };

          if (isSwap) {
            mobileGallery.classList.add('is-swapping');
            setTimeout(() => {
              if (mobileGallery._swapToken !== swapToken) return; // dipencet lagi, skip
              doSwap();
            }, 180);
          } else {
            doSwap();
          }
        }

        const dots = document.getElementById('mobile-dots');
        if (dots) {
          dots.innerHTML = images.map((_, i) => `
            <span class="dot" style="width:${i === startIndex ? '20px' : '7px'};height:7px;border-radius:4px;background:${i === startIndex ? '#83513D' : '#D3C0AC'};transition:all 0.2s;"></span>
          `).join('');
        }
      }

      function renderSelectedVariantGallery(includeDefaultImages = true, startIndex = 0) {
        const resolvedVariant = variantForSelection(selectedColor, selectedSize) || firstVariantForColor(selectedColor);
        renderGallery(resolvedVariant ? galleryForVariant(resolvedVariant.id, includeDefaultImages) : defaultImages, startIndex);
      }

      function colorButtonByName(colorName) {
        return document.querySelector(`[data-color-name="${CSS.escape(colorName)}"]`);
      }

      function stepColor(direction) {
        if (colorOrder.length <= 1) {
          return false;
        }

        const currentColorIndex = colorOrder.indexOf(selectedColor);
        const safeCurrentIndex = currentColorIndex >= 0 ? currentColorIndex : 0;
        const nextColorIndex = (safeCurrentIndex + direction + colorOrder.length) % colorOrder.length;
        const nextColor = colorOrder[nextColorIndex];
        const nextButton = colorButtonByName(nextColor);

        if (!nextColor || !nextButton) {
          return false;
        }

        const resolvedVariant = variantForSelection(nextColor, selectedSize) || firstVariantForColor(nextColor);
        const gallery = resolvedVariant ? galleryForVariant(resolvedVariant.id, false) : defaultImages;
        const targetIndex = direction > 0 ? 0 : gallery.length - 1;

        selectColor(nextButton, nextColor, false, targetIndex);

        return true;
      }

      function selectSize(btn, size) {
        if (!variantForSelection(selectedColor, size)) {
          return;
        }

        selectedSize = size;
        syncSizeButtons();
        document.getElementById('sel-size').textContent = size;
        renderSelectedVariantGallery();
      }

      function selectColor(btn, name, includeDefaultImages = true, targetImageIndex = 0) {
        selectedColor = name;

        const activeButton = btn || colorButtonByName(name);
        if (!activeButton) {
          return;
        }

        hydrateDeferredImages(activeButton);

        activeButton.parentElement.querySelectorAll('button').forEach((button) => {
          button.style.borderColor = 'rgba(211,192,172,0.58)';
        });

        activeButton.style.borderColor = '#83513D';
        document.getElementById('sel-color').textContent = name;

        ensureValidSizeForColor(name);
        syncSizeButtons();
        document.getElementById('sel-size').textContent = selectedSize;
        renderSelectedVariantGallery(includeDefaultImages, targetImageIndex);
      }

      function changeQty(delta) {
        qty = Math.max(1, Math.min(99, qty + delta));
        document.getElementById('qty-val').textContent = qty;
      }

      function setMainImageError(isError) {
        const mainImg = document.getElementById('main-img');
        const errorBox = document.getElementById('main-img-error');
        if (!mainImg || !errorBox) return;

        mainImg.style.opacity = isError ? '0' : '1';
        errorBox.style.display = isError ? 'flex' : 'none';
      }

      function resolveImageSrc(src) {
        if (!src) return '';

	        try {
          return new URL(src, window.location.href).href;
        } catch (_) {
          return src;
        }
      }

      function updateMainImage(nextSrc) {
        const mainImg = document.getElementById('main-img');
        if (!mainImg) return;

        mainImg.dataset.requestedSrc = resolveImageSrc(nextSrc);
        setMainImageError(false);
        mainImg.src = nextSrc || '';

        if (mainImg.complete) {
          setMainImageError(mainImg.naturalWidth === 0);
        }
      }

      const mainImage = document.getElementById('main-img');
      if (mainImage) {
        mainImage.dataset.requestedSrc = resolveImageSrc(mainImage.currentSrc || mainImage.src);

        mainImage.addEventListener('load', () => {
          if (mainImage.currentSrc !== mainImage.dataset.requestedSrc && mainImage.src !== mainImage.dataset.requestedSrc) return;
          setMainImageError(false);
        });

        mainImage.addEventListener('error', () => {
          if (mainImage.currentSrc !== mainImage.dataset.requestedSrc && mainImage.src !== mainImage.dataset.requestedSrc) return;
          setMainImageError(true);
        });

        if (mainImage.complete && mainImage.naturalWidth === 0) {
          setMainImageError(true);
        }
      }

      document.querySelectorAll('[data-color-options] button').forEach((button) => {
        button.addEventListener('mouseenter', () => hydrateDeferredImages(button), { once: true });
        button.addEventListener('focus', () => hydrateDeferredImages(button), { once: true });
      });

      function bindThumbnailHydration(root = document) {
        root.querySelectorAll('.product-thumbs button').forEach((button) => {
          button.addEventListener('mouseenter', () => hydrateDeferredImages(button), { once: true });
          button.addEventListener('focus', () => hydrateDeferredImages(button), { once: true });
        });
      }

      bindThumbnailHydration();

      observeDeferredImages('.related-edit img[data-src]');

      ensureValidSizeForColor(selectedColor);
      syncSizeButtons();
      document.getElementById('sel-size').textContent = selectedSize;

      // Mobile gallery
      const gallery = document.getElementById('mobile-gallery');

      // Mobile gallery dots - scroll based
      if (gallery) {
        hydrateDeferredImages(gallery);
        let touchStartX = 0;
        let touchStartY = 0;

        gallery.addEventListener('touchstart', (event) => {
          const touch = event.touches[0];
          touchStartX = touch.clientX;
          touchStartY = touch.clientY;
        }, { passive: true });

        gallery.addEventListener('touchend', (event) => {
          const touch = event.changedTouches[0];
          const deltaX = touchStartX - touch.clientX;
          const deltaY = touchStartY - touch.clientY;

          if (Math.abs(deltaX) < 42 || Math.abs(deltaX) < Math.abs(deltaY)) return;

          if (deltaX > 0 && activeImageIndex >= images.length - 1) {
            stepImage(1);
          } else if (deltaX < 0 && activeImageIndex <= 0) {
            stepImage(-1);
          }
        }, { passive: true });

	        gallery.addEventListener('scroll', () => {
	          const scrollLeft = gallery.scrollLeft;
		          const width = gallery.offsetWidth;
		          const activeIdx = Math.max(0, Math.min(images.length - 1, Math.round(scrollLeft / width)));
		          activeImageIndex = activeIdx;
		          document.querySelectorAll('#mobile-dots .dot').forEach((dot, i) => {
	            dot.style.width = i === activeIdx ? '20px' : '7px';
	            dot.style.background = i === activeIdx ? '#83513D' : '#D3C0AC';
          });
        });
      }

      // Zoom-on-hover untuk gambar utama (desktop)
      const zoomContainer = document.querySelector('.product-main-img__zoom');
      if (zoomContainer && window.matchMedia('(min-width: 1024px)').matches) {
        const ZOOM_SCALE = 2;
        const mainImg = document.getElementById('main-img');

        const applyZoom = (clientX, clientY) => {
          const rect = zoomContainer.getBoundingClientRect();
          const relX = Math.min(Math.max((clientX - rect.left) / rect.width, 0), 1);
          const relY = Math.min(Math.max((clientY - rect.top) / rect.height, 0), 1);
          const originX = relX * 100;
          const originY = relY * 100;
          mainImg.style.transformOrigin = `${originX}% ${originY}%`;
          mainImg.style.transform = `scale(${ZOOM_SCALE})`;
        };

        const resetZoom = () => {
          zoomContainer.classList.remove('is-zooming');
          mainImg.style.transform = 'scale(1)';
        };

        zoomContainer.addEventListener('mouseenter', (e) => {
          zoomContainer.classList.add('is-zooming');
          applyZoom(e.clientX, e.clientY);
        });

        zoomContainer.addEventListener('mousemove', (e) => {
          if (!zoomContainer.classList.contains('is-zooming')) return;
          applyZoom(e.clientX, e.clientY);
        });

        zoomContainer.addEventListener('mouseleave', resetZoom);
        zoomContainer.addEventListener('dragstart', (e) => e.preventDefault());
      }

      window.addEventListener('pageshow', () => {
        const buyBtn = document.querySelector('.action-btn-solid');
        if (buyBtn) {
          buyBtn.textContent = 'Buy It Now';
          buyBtn.disabled = false;
          buyBtn.style.opacity = '';
        }
        const cartBtn = document.querySelector('.action-btn-outline');
        if (cartBtn) {
          cartBtn.textContent = 'Add to Cart';
          cartBtn.disabled = false;
          cartBtn.style.opacity = '';
        }
      });

      function openSizeChartModal() {
        const modal = document.getElementById('size-chart-modal');
        if (modal) {
          modal.style.display = 'flex';
          document.body.style.overflow = 'hidden';
        }
      }

      function closeSizeChartModal() {
        const modal = document.getElementById('size-chart-modal');
        if (modal) {
          modal.style.display = 'none';
          document.body.style.overflow = '';
        }
      }

      window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
          closeSizeChartModal();
        }
      });
    </script>

    <!-- Size Chart Modal -->
    <div id="size-chart-modal" style="display:none; position:fixed; inset:0; z-index:99999; align-items:center; justify-content:center; padding:16px;">
      <!-- Backdrop -->
      <div onclick="closeSizeChartModal()" style="position:absolute; inset:0; background:rgba(0,0,0,0.45); backdrop-filter:blur(2px); transition:opacity 0.25s ease;"></div>
      <!-- Modal Content Card -->
      <div style="position:relative; background:#FFFFFF; width:100%; max-width:550px; border-radius:12px; box-shadow:0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04); overflow:hidden; display:flex; flex-direction:column; animation:modalFadeIn 0.25s ease-out; font-family:'Plus Jakarta Sans',system-ui,sans-serif; border:1px solid rgba(211,192,172,0.4);">
        
        <!-- Header -->
        <div style="display:flex; justify-content:space-between; align-items:center; padding:16px 20px; border-bottom:1px solid rgba(211,192,172,0.3); background:#FCF8F3;">
          <h3 style="font-size:16px; font-weight:700; color:#201916; margin:0;">Panduan & Tabel Ukuran</h3>
          <button type="button" onclick="closeSizeChartModal()" style="background:none; border:none; padding:4px; cursor:pointer; color:#71665d; display:flex; align-items:center; justify-content:center;" onmouseover="this.style.color='#201916'" onmouseout="this.style.color='#71665d'">
            <svg viewBox="0 0 24 24" style="width:20px; height:20px; fill:none; stroke:currentColor; stroke-width:2; stroke-linecap:round; stroke-linejoin:round;">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </button>
        </div>

        <!-- Body -->
        <div style="padding:20px; overflow-y:auto; max-height:calc(90vh - 100px); display:flex; flex-direction:column; gap:20px;">
          @php
            $categorySlug = $produk->kategori->slug ?? 'default';

            $sizeChartTemplates = [
                'abaya' => [
                    'S' => ['ld' => '96 cm', 'pb' => '135 cm', 'pl' => '56 cm'],
                    'M' => ['ld' => '100 cm', 'pb' => '138 cm', 'pl' => '57 cm'],
                    'L' => ['ld' => '104 cm', 'pb' => '140 cm', 'pl' => '58 cm'],
                    'XL' => ['ld' => '110 cm', 'pb' => '142 cm', 'pl' => '59 cm'],
                    'XXL' => ['ld' => '120 cm', 'pb' => '142 cm', 'pl' => '60 cm'],
                    'All Size' => ['ld' => '104 cm', 'pb' => '138 cm', 'pl' => '58 cm'],
                ],
                'one-set' => [
                    'S' => ['ld' => '96 cm', 'pb' => '65 cm', 'pc' => '92 cm'],
                    'M' => ['ld' => '100 cm', 'pb' => '68 cm', 'pc' => '94 cm'],
                    'L' => ['ld' => '104 cm', 'pb' => '70 cm', 'pc' => '96 cm'],
                    'XL' => ['ld' => '110 cm', 'pb' => '72 cm', 'pc' => '98 cm'],
                    'XXL' => ['ld' => '120 cm', 'pb' => '74 cm', 'pc' => '100 cm'],
                    'All Size' => ['ld' => '104 cm', 'pb' => '70 cm', 'pc' => '96 cm'],
                ],
                'daster' => [
                    'S' => ['ld' => '100 cm', 'pb' => '110 cm'],
                    'M' => ['ld' => '105 cm', 'pb' => '112 cm'],
                    'L' => ['ld' => '110 cm', 'pb' => '115 cm'],
                    'XL' => ['ld' => '120 cm', 'pb' => '118 cm'],
                    'XXL' => ['ld' => '130 cm', 'pb' => '120 cm'],
                    'All Size' => ['ld' => '110 cm', 'pb' => '115 cm'],
                ],
                'khimar' => [
                    'M' => ['pd' => '75 cm', 'pb' => '90 cm', 'lm' => '52 cm'],
                    'L' => ['pd' => '85 cm', 'pb' => '105 cm', 'lm' => '54 cm'],
                    'XL' => ['pd' => '95 cm', 'pb' => '115 cm', 'lm' => '56 cm'],
                    'All Size' => ['pd' => '85 cm', 'pb' => '105 cm', 'lm' => '54 cm'],
                ],
                'mukena' => [
                    'Dewasa Standar' => ['pda' => '115 cm', 'pba' => '125 cm', 'pr' => '115 cm'],
                    'Dewasa Jumbo' => ['pda' => '125 cm', 'pba' => '135 cm', 'pr' => '120 cm'],
                    'Standard' => ['pda' => '115 cm', 'pba' => '125 cm', 'pr' => '115 cm'],
                    'Jumbo' => ['pda' => '125 cm', 'pba' => '135 cm', 'pr' => '120 cm'],
                    'All Size' => ['pda' => '120 cm', 'pba' => '130 cm', 'pr' => '117 cm'],
                ]
            ];

            $currentTemplate = $sizeChartTemplates[$categorySlug] ?? [];
          @endphp

          @if ($categorySlug === 'abaya')
            <!-- Sizing Diagram Gamis / Dress (Inline SVG) -->
            <div style="display:flex; justify-content:center; align-items:center; gap:20px; padding:12px; background:#FAFAFA; border-radius:8px; border:1px dashed rgba(211,192,172,0.6);">
              <svg width="100" height="100" viewBox="0 0 120 120" style="flex-shrink:0;">
                <!-- Dress Silhouette -->
                <path d="M45 15 C52 20, 68 20, 75 15 L95 25 L88 38 L80 34 L80 110 L40 110 L40 34 L32 38 L25 25 Z" fill="#F2EAE1" stroke="#83513D" stroke-width="2" stroke-linejoin="round"/>
                <!-- Line Lingkar Dada -->
                <line x1="42" y1="45" x2="78" y2="45" stroke="#D27C2C" stroke-width="1.5" stroke-dasharray="3 3"/>
                <polygon points="42,45 47,42 47,48" fill="#D27C2C"/>
                <polygon points="78,45 73,42 73,48" fill="#D27C2C"/>
                <!-- Line Panjang -->
                <line x1="60" y1="20" x2="60" y2="105" stroke="#4A6B82" stroke-width="1.5" stroke-dasharray="3 3"/>
                <polygon points="60,20 57,25 63,25" fill="#4A6B82"/>
                <polygon points="60,105 57,100 63,100" fill="#4A6B82"/>
                <!-- Texts -->
                <text x="60" y="42" font-size="7" font-weight="700" fill="#D27C2C" text-anchor="middle">Lingkar Dada</text>
                <text x="66" y="80" font-size="7" font-weight="700" fill="#4A6B82" text-anchor="start">Panjang</text>
              </svg>
              <div style="font-size:12px; color:#554c44; line-height:1.6;">
                <p style="margin:0 0 6px 0;"><strong style="color:#D27C2C;">■ Lingkar Dada (LD):</strong> Lingkar sekeliling dada di bawah ketiak.</p>
                <p style="margin:0;"><strong style="color:#4A6B82;">■ Panjang Gamis (PB):</strong> Diukur vertikal dari pundak sampai ujung bawah gamis.</p>
              </div>
            </div>

            <!-- Sizing Table Gamis -->
            <table style="width:100%; border-collapse:collapse; text-align:center; font-size:13px; border-radius:6px; overflow:hidden;">
              <thead>
                <tr style="background:#83513D; color:#FFFFFF;">
                  <th style="padding:10px; font-weight:600; border:1px solid #83513D;">Ukuran</th>
                  <th style="padding:10px; font-weight:600; border:1px solid #83513D;">Lingkar Dada (LD)</th>
                  <th style="padding:10px; font-weight:600; border:1px solid #83513D;">Panjang Badan (PB)</th>
                  <th style="padding:10px; font-weight:600; border:1px solid #83513D;">Panjang Lengan</th>
                </tr>
              </thead>
              <tbody style="color:#201916;">
                @foreach ($sizes as $size)
                  @php
                    $row = $currentTemplate[$size] ?? ['ld' => '-', 'pb' => '-', 'pl' => '-'];
                  @endphp
                  <tr style="background: {{ $loop->even ? '#FAFAFA' : '#FFFFFF' }};">
                    <td style="padding:10px; font-weight:700; border:1px solid rgba(211,192,172,0.4); background:#FCF8F3;">{{ $size }}</td>
                    <td style="padding:10px; border:1px solid rgba(211,192,172,0.4);">{{ $row['ld'] }}</td>
                    <td style="padding:10px; border:1px solid rgba(211,192,172,0.4);">{{ $row['pb'] }}</td>
                    <td style="padding:10px; border:1px solid rgba(211,192,172,0.4);">{{ $row['pl'] }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>

          @elseif ($categorySlug === 'one-set')
            <!-- Sizing Diagram One Set (Inline SVG) -->
            <div style="display:flex; justify-content:center; align-items:center; gap:20px; padding:12px; background:#FAFAFA; border-radius:8px; border:1px dashed rgba(211,192,172,0.6);">
              <svg width="100" height="100" viewBox="0 0 120 120" style="flex-shrink:0;">
                <!-- Shirt Top -->
                <path d="M40 10 C46 15, 64 15, 70 10 L86 20 L80 32 L73 29 L73 60 L37 60 L37 29 L30 32 L24 20 Z" fill="#F2EAE1" stroke="#83513D" stroke-width="1.5" stroke-linejoin="round"/>
                <!-- Pants Bottom -->
                <path d="M42 65 L68 65 L72 110 L57 110 L55 85 L53 85 L51 110 L36 110 Z" fill="#F2EAE1" stroke="#83513D" stroke-width="1.5" stroke-linejoin="round"/>
                <!-- Line LD -->
                <line x1="39" y1="35" x2="69" y2="35" stroke="#D27C2C" stroke-width="1.5" stroke-dasharray="3 3"/>
                <polygon points="39,35 43,32 43,38" fill="#D27C2C"/>
                <polygon points="69,35 65,32 65,38" fill="#D27C2C"/>
                <!-- Line PJ Celana -->
                <line x1="80" y1="65" x2="80" y2="110" stroke="#4A6B82" stroke-width="1.5" stroke-dasharray="3 3"/>
                <polygon points="80,65 77,70 83,70" fill="#4A6B82"/>
                <polygon points="80,110 77,105 83,105" fill="#4A6B82"/>
                <!-- Texts -->
                <text x="54" y="32" font-size="7" font-weight="700" fill="#D27C2C" text-anchor="middle">LD</text>
                <text x="85" y="90" font-size="7" font-weight="700" fill="#4A6B82" text-anchor="start">Celana</text>
              </svg>
              <div style="font-size:12px; color:#554c44; line-height:1.6;">
                <p style="margin:0 0 6px 0;"><strong style="color:#D27C2C;">■ Lingkar Dada (LD):</strong> Lingkar sekeliling dada baju atasan.</p>
                <p style="margin:0;"><strong style="color:#4A6B82;">■ Panjang Celana (PC):</strong> Diukur dari pinggang karet sampai ujung celana.</p>
              </div>
            </div>

            <!-- Sizing Table One Set -->
            <table style="width:100%; border-collapse:collapse; text-align:center; font-size:13px; border-radius:6px; overflow:hidden;">
              <thead>
                <tr style="background:#83513D; color:#FFFFFF;">
                  <th style="padding:10px; font-weight:600; border:1px solid #83513D;">Ukuran</th>
                  <th style="padding:10px; font-weight:600; border:1px solid #83513D;">Lingkar Dada (LD)</th>
                  <th style="padding:10px; font-weight:600; border:1px solid #83513D;">Panjang Baju</th>
                  <th style="padding:10px; font-weight:600; border:1px solid #83513D;">Panjang Celana</th>
                </tr>
              </thead>
              <tbody style="color:#201916;">
                @foreach ($sizes as $size)
                  @php
                    $row = $currentTemplate[$size] ?? ['ld' => '-', 'pb' => '-', 'pc' => '-'];
                  @endphp
                  <tr style="background: {{ $loop->even ? '#FAFAFA' : '#FFFFFF' }};">
                    <td style="padding:10px; font-weight:700; border:1px solid rgba(211,192,172,0.4); background:#FCF8F3;">{{ $size }}</td>
                    <td style="padding:10px; border:1px solid rgba(211,192,172,0.4);">{{ $row['ld'] }}</td>
                    <td style="padding:10px; border:1px solid rgba(211,192,172,0.4);">{{ $row['pb'] }}</td>
                    <td style="padding:10px; border:1px solid rgba(211,192,172,0.4);">{{ $row['pc'] }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>

          @elseif ($categorySlug === 'daster')
            <!-- Sizing Diagram Daster (Inline SVG) -->
            <div style="display:flex; justify-content:center; align-items:center; gap:20px; padding:12px; background:#FAFAFA; border-radius:8px; border:1px dashed rgba(211,192,172,0.6);">
              <svg width="100" height="100" viewBox="0 0 120 120" style="flex-shrink:0;">
                <!-- Daster Silhouette -->
                <path d="M40 15 C48 20, 68 20, 76 15 L90 28 L82 40 L76 36 L74 95 L42 95 L40 36 L34 40 L26 28 Z" fill="#F2EAE1" stroke="#83513D" stroke-width="1.8" stroke-linejoin="round"/>
                <!-- Line LD -->
                <line x1="41" y1="45" x2="75" y2="45" stroke="#D27C2C" stroke-width="1.5" stroke-dasharray="3 3"/>
                <polygon points="41,45 45,42 45,48" fill="#D27C2C"/>
                <polygon points="75,45 71,42 71,48" fill="#D27C2C"/>
                <!-- Line Panjang -->
                <line x1="58" y1="20" x2="58" y2="92" stroke="#4A6B82" stroke-width="1.5" stroke-dasharray="3 3"/>
                <polygon points="58,20 55,25 61,25" fill="#4A6B82"/>
                <polygon points="58,92 55,87 61,87" fill="#4A6B82"/>
                <!-- Texts -->
                <text x="58" y="42" font-size="7" font-weight="700" fill="#D27C2C" text-anchor="middle">Lingkar Dada</text>
                <text x="64" y="70" font-size="7" font-weight="700" fill="#4A6B82" text-anchor="start">Panjang</text>
              </svg>
              <div style="font-size:12px; color:#554c44; line-height:1.6;">
                <p style="margin:0 0 6px 0;"><strong style="color:#D27C2C;">■ Lingkar Dada (LD):</strong> Lingkar sekeliling dada (desain longgar).</p>
                <p style="margin:0;"><strong style="color:#4A6B82;">■ Panjang Daster (PB):</strong> Diukur dari pundak sampai ujung bawah daster.</p>
              </div>
            </div>

            <!-- Sizing Table Daster -->
            <table style="width:100%; border-collapse:collapse; text-align:center; font-size:13px; border-radius:6px; overflow:hidden;">
              <thead>
                <tr style="background:#83513D; color:#FFFFFF;">
                  <th style="padding:10px; font-weight:600; border:1px solid #83513D;">Ukuran</th>
                  <th style="padding:10px; font-weight:600; border:1px solid #83513D;">Lingkar Dada (LD)</th>
                  <th style="padding:10px; font-weight:600; border:1px solid #83513D;">Panjang Badan (PB)</th>
                </tr>
              </thead>
              <tbody style="color:#201916;">
                @foreach ($sizes as $size)
                  @php
                    $row = $currentTemplate[$size] ?? ['ld' => '-', 'pb' => '-'];
                  @endphp
                  <tr style="background: {{ $loop->even ? '#FAFAFA' : '#FFFFFF' }};">
                    <td style="padding:10px; font-weight:700; border:1px solid rgba(211,192,172,0.4); background:#FCF8F3;">{{ $size }}</td>
                    <td style="padding:10px; border:1px solid rgba(211,192,172,0.4);">{{ $row['ld'] }}</td>
                    <td style="padding:10px; border:1px solid rgba(211,192,172,0.4);">{{ $row['pb'] }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>

          @elseif ($categorySlug === 'khimar')
            <!-- Sizing Diagram Khimar (Inline SVG) -->
            <div style="display:flex; justify-content:center; align-items:center; gap:20px; padding:12px; background:#FAFAFA; border-radius:8px; border:1px dashed rgba(211,192,172,0.6);">
              <svg width="100" height="100" viewBox="0 0 120 120" style="flex-shrink:0;">
                <!-- Hijab Shape -->
                <path d="M60 10 C40 10, 25 35, 25 70 C25 95, 45 105, 60 112 C75 105, 95 95, 95 70 C95 35, 80 10, 60 10 Z" fill="#F2EAE1" stroke="#83513D" stroke-width="1.8" stroke-linejoin="round"/>
                <!-- Face Hole -->
                <path d="M60 22 C52 22, 48 32, 48 44 C48 56, 52 64, 60 64 C68 64, 72 56, 72 44 C72 32, 68 22, 60 22 Z" fill="#E6DED6" stroke="#83513D" stroke-width="1"/>
                <!-- Line PD -->
                <line x1="60" y1="65" x2="60" y2="110" stroke="#D27C2C" stroke-width="1.5" stroke-dasharray="3 3"/>
                <polygon points="60,65 57,70 63,70" fill="#D27C2C"/>
                <polygon points="60,110 57,105 63,105" fill="#D27C2C"/>
                <!-- Line LW -->
                <line x1="48" y1="44" x2="72" y2="44" stroke="#4A6B82" stroke-width="1.5" stroke-dasharray="3 3"/>
                <polygon points="48,44 52,41 52,47" fill="#4A6B82"/>
                <polygon points="72,44 68,41 68,47" fill="#4A6B82"/>
                <!-- Texts -->
                <text x="60" y="85" font-size="7" font-weight="700" fill="#D27C2C" text-anchor="middle">PD</text>
                <text x="60" y="41" font-size="6" font-weight="700" fill="#4A6B82" text-anchor="middle">LM</text>
              </svg>
              <div style="font-size:12px; color:#554c44; line-height:1.6;">
                <p style="margin:0 0 6px 0;"><strong style="color:#D27C2C;">■ Panjang Depan (PD):</strong> Diukur dari bawah dagu ke ujung depan khimar.</p>
                <p style="margin:0;"><strong style="color:#4A6B82;">■ Lingkar Muka (LM):</strong> Lingkar kepala bagian wajah hijab.</p>
              </div>
            </div>

            <!-- Sizing Table Khimar -->
            <table style="width:100%; border-collapse:collapse; text-align:center; font-size:13px; border-radius:6px; overflow:hidden;">
              <thead>
                <tr style="background:#83513D; color:#FFFFFF;">
                  <th style="padding:10px; font-weight:600; border:1px solid #83513D;">Ukuran</th>
                  <th style="padding:10px; font-weight:600; border:1px solid #83513D;">Panjang Depan (PD)</th>
                  <th style="padding:10px; font-weight:600; border:1px solid #83513D;">Panjang Belakang (PB)</th>
                  <th style="padding:10px; font-weight:600; border:1px solid #83513D;">Lingkar Muka (LM)</th>
                </tr>
              </thead>
              <tbody style="color:#201916;">
                @foreach ($sizes as $size)
                  @php
                    $row = $currentTemplate[$size] ?? ['pd' => '-', 'pb' => '-', 'lm' => '-'];
                  @endphp
                  <tr style="background: {{ $loop->even ? '#FAFAFA' : '#FFFFFF' }};">
                    <td style="padding:10px; font-weight:700; border:1px solid rgba(211,192,172,0.4); background:#FCF8F3;">{{ $size }}</td>
                    <td style="padding:10px; border:1px solid rgba(211,192,172,0.4);">{{ $row['pd'] }}</td>
                    <td style="padding:10px; border:1px solid rgba(211,192,172,0.4);">{{ $row['pb'] }}</td>
                    <td style="padding:10px; border:1px solid rgba(211,192,172,0.4);">{{ $row['lm'] }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>

          @elseif ($categorySlug === 'mukena')
            <!-- Sizing Diagram Mukena (Inline SVG) -->
            <div style="display:flex; justify-content:center; align-items:center; gap:20px; padding:12px; background:#FAFAFA; border-radius:8px; border:1px dashed rgba(211,192,172,0.6);">
              <svg width="100" height="100" viewBox="0 0 120 120" style="flex-shrink:0;">
                <!-- Mukena Top -->
                <path d="M60 10 C42 10, 20 30, 20 65 L60 85 L100 65 C100 30, 78 10, 60 10 Z" fill="#F2EAE1" stroke="#83513D" stroke-width="1.5" stroke-linejoin="round"/>
                <!-- Mukena Bottom -->
                <path d="M40 85 L80 85 L75 115 L45 115 Z" fill="#F2EAE1" stroke="#83513D" stroke-width="1.5" stroke-linejoin="round"/>
                <!-- Line PDA -->
                <line x1="60" y1="20" x2="60" y2="82" stroke="#D27C2C" stroke-width="1.5" stroke-dasharray="3 3"/>
                <polygon points="60,20 57,25 63,25" fill="#D27C2C"/>
                <polygon points="60,82 57,77 63,77" fill="#D27C2C"/>
                <!-- Line Rok -->
                <line x1="82" y1="85" x2="82" y2="115" stroke="#4A6B82" stroke-width="1.5" stroke-dasharray="3 3"/>
                <polygon points="82,85 79,90 85,90" fill="#4A6B82"/>
                <polygon points="82,115 79,110 85,110" fill="#4A6B82"/>
                <!-- Texts -->
                <text x="60" y="55" font-size="7" font-weight="700" fill="#D27C2C" text-anchor="middle">Atasan</text>
                <text x="87" y="100" font-size="7" font-weight="700" fill="#4A6B82" text-anchor="start">Rok</text>
              </svg>
              <div style="font-size:12px; color:#554c44; line-height:1.6;">
                <p style="margin:0 0 6px 0;"><strong style="color:#D27C2C;">■ Panjang Atasan:</strong> Diukur dari dahi sampai ujung mukena atasan.</p>
                <p style="margin:0;"><strong style="color:#4A6B82;">■ Panjang Rok:</strong> Diukur dari karet pinggang sampai bawah rok bawahan.</p>
              </div>
            </div>

            <!-- Sizing Table Mukena -->
            <table style="width:100%; border-collapse:collapse; text-align:center; font-size:13px; border-radius:6px; overflow:hidden;">
              <thead>
                <tr style="background:#83513D; color:#FFFFFF;">
                  <th style="padding:10px; font-weight:600; border:1px solid #83513D;">Ukuran</th>
                  <th style="padding:10px; font-weight:600; border:1px solid #83513D;">Panjang Atasan Depan</th>
                  <th style="padding:10px; font-weight:600; border:1px solid #83513D;">Panjang Atasan Belakang</th>
                  <th style="padding:10px; font-weight:600; border:1px solid #83513D;">Panjang Rok Bawahan</th>
                </tr>
              </thead>
              <tbody style="color:#201916;">
                @foreach ($sizes as $size)
                  @php
                    $row = $currentTemplate[$size] ?? ['pda' => '-', 'pba' => '-', 'pr' => '-'];
                  @endphp
                  <tr style="background: {{ $loop->even ? '#FAFAFA' : '#FFFFFF' }};">
                    <td style="padding:10px; font-weight:700; border:1px solid rgba(211,192,172,0.4); background:#FCF8F3;">{{ $size }}</td>
                    <td style="padding:10px; border:1px solid rgba(211,192,172,0.4);">{{ $row['pda'] }}</td>
                    <td style="padding:10px; border:1px solid rgba(211,192,172,0.4);">{{ $row['pba'] }}</td>
                    <td style="padding:10px; border:1px solid rgba(211,192,172,0.4);">{{ $row['pr'] }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>

          @else
            <!-- Sizing Diagram Default (Inline SVG) -->
            <div style="display:flex; justify-content:center; align-items:center; gap:20px; padding:12px; background:#FAFAFA; border-radius:8px; border:1px dashed rgba(211,192,172,0.6);">
              <svg width="100" height="100" viewBox="0 0 120 120" style="flex-shrink:0;">
                <!-- Package Box Shape -->
                <rect x="30" y="30" width="60" height="60" fill="#F2EAE1" stroke="#83513D" stroke-width="2" rx="4"/>
                <line x1="30" y1="60" x2="90" y2="60" stroke="#83513D" stroke-width="1.5" stroke-dasharray="3 3"/>
                <line x1="60" y1="30" x2="60" y2="90" stroke="#83513D" stroke-width="1.5" stroke-dasharray="3 3"/>
              </svg>
              <div style="font-size:12px; color:#554c44; line-height:1.6;">
                <p style="margin:0 0 6px 0;"><strong style="color:#83513D;">■ Standar All Size:</strong> Produk ini didesain universal untuk semua ukuran.</p>
                <p style="margin:0;">Informasi detail ukuran spesifik dapat dilihat langsung pada deskripsi produk.</p>
              </div>
            </div>

            <!-- Sizing Table Default -->
            <table style="width:100%; border-collapse:collapse; text-align:center; font-size:13px; border-radius:6px; overflow:hidden;">
              <thead>
                <tr style="background:#83513D; color:#FFFFFF;">
                  <th style="padding:10px; font-weight:600; border:1px solid #83513D;">Ukuran</th>
                  <th style="padding:10px; font-weight:600; border:1px solid #83513D;">Keterangan</th>
                </tr>
              </thead>
              <tbody style="color:#201916;">
                @foreach ($sizes as $size)
                  <tr style="background: {{ $loop->even ? '#FAFAFA' : '#FFFFFF' }};">
                    <td style="padding:10px; font-weight:700; border:1px solid rgba(211,192,172,0.4); background:#FCF8F3;">{{ $size }}</td>
                    <td style="padding:10px; border:1px solid rgba(211,192,172,0.4);">Cocok untuk semua ukuran standar (One size fits most)</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          @endif

          <!-- Note / Warning -->
          <div style="font-size:11px; color:#8c8279; line-height:1.5; background:#FFFDFB; padding:10px 14px; border-left:3px solid #83513D; border-radius:0 4px 4px 0;">
            <strong>Catatan Penting:</strong>
            <ul style="margin:4px 0 0 0; padding-left:16px;">
              <li>Toleransi ukuran ±1-2 cm dapat terjadi dikarenakan metode pemotongan bahan dan proses produksi jahit massal.</li>
              <li>Warna produk pada foto mungkin sedikit berbeda dengan produk asli karena pencahayaan studio foto.</li>
            </ul>
          </div>

        </div>

        <!-- Footer -->
        <div style="display:flex; justify-content:flex-end; padding:12px 20px; border-top:1px solid rgba(211,192,172,0.2); background:#FAFAFA;">
          <button type="button" onclick="closeSizeChartModal()" style="padding:8px 16px; font-size:12px; font-weight:600; color:#554c44; background:#FFFFFF; border:1px solid rgba(211,192,172,0.6); border-radius:4px; cursor:pointer; transition:all 0.15s;" onmouseover="this.style.background='#F5F0EA'" onmouseout="this.style.background='#FFFFFF'">Tutup</button>
        </div>

      </div>
    </div>

    <style>
      @keyframes modalFadeIn {
        from { opacity: 0; transform: scale(0.96); }
        to { opacity: 1; transform: scale(1); }
      }
    </style>
  </body>
</html>
