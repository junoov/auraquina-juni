<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Auraquina - Shop</title>
    <meta name="description" content="Jelajahi koleksi produk modest Auraquina." />
    <link rel="icon" href="{{ asset('images/logo.png') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,500&family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>
  <body class="min-h-screen overflow-x-hidden bg-[var(--warm)] text-[var(--text)] antialiased [text-rendering:geometricPrecision]">
    @php
      $selectedSizes = collect($filterState['sizes'] ?? []);
      $selectedPrices = collect($filterState['prices'] ?? []);
      $selectedColors = collect($filterState['colors'] ?? []);
      $selectedSort = $filterState['sort'] ?? 'featured';
      $filters = [
          'Kategori' => collect([['label' => 'Semua Produk', 'value' => '']])->merge($kategoris->map(fn ($kategori) => ['label' => $kategori->nama, 'value' => $kategori->slug]))->values(),
          'Ukuran' => collect([
              ['label' => 'XXL', 'value' => 'XXL'],
              ['label' => 'All Size', 'value' => 'All Size'],
          ]),
          'Harga' => collect([
            ['label' => '< Rp 300.000', 'value' => 'under_300'],
            ['label' => 'Rp 300.000 - 500.000', 'value' => '300_500'],
            ['label' => '> Rp 500.000', 'value' => 'over_500'],
          ]),
      ];

      $sortOptions = [
        'featured' => 'Featured',
        'newest' => 'Terbaru',
        'price_asc' => 'Harga: Rendah ke Tinggi',
        'price_desc' => 'Harga: Tinggi ke Rendah',
        'name_asc' => 'Nama A-Z',
      ];

      $containerClass = 'mx-auto w-full max-w-[1184px] px-4 max-sm:px-3';
      $imageVariants = app(\App\Services\ProductImageVariantService::class);
      $productImageUrl = fn (?string $path, string $variant = 'card') => $imageVariants->url($path, $variant);
      $productImageSrcset = fn (?string $path) => $imageVariants->srcset($path, ['card' => 600, 'detail' => 1200]);
    @endphp

    @include('components.site-header', ['kategoris' => $kategoris, 'backHref' => '/'])

    <main class="">
      <section class="{{ $containerClass }} pt-4 pb-8 max-sm:pt-3 max-sm:pb-5">

        @if (! empty($searchTerm))
          <div class="mb-5 flex flex-wrap items-center justify-between gap-3 rounded-[8px] border border-[var(--border)] bg-[var(--white)] px-4 py-3 text-[13px] text-[var(--muted)]">
            <span>{{ $produks->count() }} produk ditemukan untuk <span class="font-bold text-[var(--ink)]">"{{ $searchTerm }}"</span>.</span>
            <a href="{{ ! empty($category) ? '/shop?category=' . urlencode($category) : '/shop' }}" class="inline-flex h-8 items-center rounded-full bg-[var(--cream)] px-3 text-[11px] font-bold uppercase tracking-[0.08em] text-[var(--brown)] transition hover:bg-[var(--sand)]">Hapus</a>
          </div>
        @endif

        {{-- Page title + sort (desktop) --}}
        <div class="mb-7 flex items-center justify-between gap-4 max-sm:hidden">
          <h1 class="text-[20px] leading-[1.2] font-bold text-[var(--ink)]">{{ empty($searchTerm) ? 'All Products' : 'Search Results' }}</h1>
          <select name="sort" form="shop-filter-form" class="h-9 rounded-lg border border-[var(--border)] bg-[var(--white)] px-3 text-[12px] font-bold text-[var(--ink)] outline-none transition focus:border-[var(--brown)]">
            @foreach ($sortOptions as $value => $label)
              <option value="{{ $value }}" {{ $selectedSort === $value ? 'selected' : '' }}>sort : {{ $label }}</option>
            @endforeach
          </select>
        </div>

        {{-- Mobile: Filter + Sort buttons --}}
        <div class="mb-4 hidden max-sm:flex gap-2">
          <button id="mobile-sort-btn" type="button" class="flex h-9 flex-1 items-center justify-center gap-2 rounded-full border border-[var(--border)] bg-[var(--white)] text-[12px] font-bold tracking-[0.02em] text-[var(--ink)] transition hover:border-[var(--brown)]">
            <svg aria-hidden="true" viewBox="0 0 24 24" class="h-3.5 w-3.5 fill-none stroke-current stroke-[1.8]"><path d="M3 6h18M6 12h12M9 18h6" /></svg>
            Sort
          </button>
          <button id="mobile-filter-btn" type="button" class="flex h-9 flex-1 items-center justify-center gap-2 rounded-full border border-[var(--border)] bg-[var(--white)] text-[12px] font-bold tracking-[0.02em] text-[var(--ink)] transition hover:border-[var(--brown)]">
            <svg aria-hidden="true" viewBox="0 0 24 24" class="h-3.5 w-3.5 fill-none stroke-current stroke-[1.8]"><path d="M4 4h16M4 12h10M4 20h6" /></svg>
            Filter
          </button>
        </div>

        {{-- Mobile Filter Bottom Sheet --}}
        <div id="filter-sheet" class="fixed inset-0 z-[200] hidden max-sm:block pointer-events-none">
          <div id="filter-backdrop" class="absolute inset-0 bg-[var(--ink)]/0 transition-all duration-300"></div>
          <div id="filter-panel" class="absolute right-0 bottom-0 left-0 max-h-[85vh] translate-y-full overflow-y-auto rounded-t-[8px] bg-[var(--white)] shadow-[0_-4px_24px_rgba(122,80,62,0.09)] transition-transform duration-300">
            <div class="sticky top-0 z-10 flex items-center justify-between border-b border-[var(--border)] bg-[var(--white)] px-5 py-4">
              <h2 class="text-[18px] font-bold text-[var(--ink)]">Filter</h2>
              <button id="filter-close" type="button" aria-label="Close" class="flex h-9 w-9 items-center justify-center rounded-lg text-[var(--ink)] transition hover:bg-[var(--cream)]">
                <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="stroke-[1.8]"><path d="M6 6l12 12M18 6 6 18" /></svg>
              </button>
            </div>

            <div class="px-5 py-4 space-y-5">
              @foreach ($filters as $title => $items)
                <details class="border-b border-[var(--border)] pb-4" open>
                  <summary class="flex cursor-pointer list-none items-center justify-between py-1 text-[15px] font-bold text-[var(--ink)]">
                    {{ $title }}
                    <svg viewBox="0 0 24 24" class="h-4 w-4 fill-none stroke-[var(--muted)] stroke-[1.8] transition-transform [[open]>&]:rotate-180"><path d="m6 9 6 6 6-6" /></svg>
                  </summary>
                  <div class="mt-3 space-y-3">
                    @foreach ($items as $item)
                      @php
                        $inputName = $title === 'Kategori' ? 'category' : ($title === 'Ukuran' ? 'size[]' : ($title === 'Warna' ? 'color[]' : 'price[]'));
                        $isChecked = match ($title) {
                          'Kategori' => ($category ?? '') === $item['value'] || (($category ?? '') === '' && $item['value'] === ''),
                          'Ukuran' => $selectedSizes->contains($item['value']),
                          'Warna' => $selectedColors->contains($item['value']),
                          default => $selectedPrices->contains($item['value']),
                        };
                      @endphp
                      <label class="flex cursor-pointer items-center gap-3 text-[14px] text-[var(--ink)]">
                        <input type="radio" name="{{ $inputName }}" value="{{ $item['value'] }}" data-filter-group="{{ Str::slug($title) }}" class="h-[18px] w-[18px] border-[var(--border)] accent-[var(--brown)]" {{ $isChecked ? 'checked' : '' }} />
                        {{ $item['label'] }}
                      </label>
                    @endforeach
                  </div>
                </details>
              @endforeach
            </div>

            <div class="sticky bottom-0 border-t border-[var(--border)] bg-[var(--white)] px-5 py-4">
              <button id="filter-apply" type="button" class="flex h-[46px] w-full items-center justify-center rounded-[6px] bg-[var(--brown)] text-[13px] font-bold tracking-[0.02em] text-[var(--white)]">Apply</button>
            </div>
          </div>
        </div>

        {{-- Mobile Sort Bottom Sheet --}}
        <div id="sort-sheet" class="fixed inset-0 z-[200] hidden max-sm:block pointer-events-none">
          <div id="sort-backdrop" class="absolute inset-0 bg-[var(--ink)]/0 transition-all duration-300"></div>
          <div id="sort-panel" class="absolute right-0 bottom-0 left-0 max-h-[60vh] translate-y-full overflow-y-auto rounded-t-[8px] bg-[var(--white)] shadow-[0_-4px_24px_rgba(122,80,62,0.09)] transition-transform duration-300">
            <div class="sticky top-0 z-10 flex items-center justify-between border-b border-[var(--border)] bg-[var(--white)] px-5 py-4">
              <h2 class="text-[18px] font-bold text-[var(--ink)]">Sort</h2>
              <button id="sort-close" type="button" aria-label="Close" class="flex h-9 w-9 items-center justify-center rounded-lg text-[var(--ink)] transition hover:bg-[var(--cream)]">
                <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="stroke-[1.8]"><path d="M6 6l12 12M18 6 6 18" /></svg>
              </button>
            </div>

            <div class="px-5 py-4 space-y-3">
              @foreach ($sortOptions as $value => $sortOption)
                <label class="flex cursor-pointer items-center gap-3 py-1 text-[14px] text-[var(--ink)]">
                  <input type="radio" name="sort" value="{{ $value }}" class="h-[18px] w-[18px] border-[var(--border)] accent-[var(--brown)]" {{ $selectedSort === $value ? 'checked' : '' }} />
                  {{ $sortOption }}
                </label>
              @endforeach
            </div>

            <div class="sticky bottom-0 border-t border-[var(--border)] bg-[var(--white)] px-5 py-4">
              <button id="sort-apply" type="button" class="flex h-[46px] w-full items-center justify-center rounded-[6px] bg-[var(--brown)] text-[13px] font-bold tracking-[0.02em] text-[var(--white)]">Apply</button>
            </div>
          </div>
        </div>

        {{-- Main grid --}}
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-[220px_minmax(0,1fr)]">
          {{-- Sidebar filters (desktop) --}}
          <aside class="hidden lg:block">
            <form id="shop-filter-form" method="GET" action="/shop">
              @if (! empty($searchTerm))
                <input type="hidden" name="search" value="{{ $searchTerm }}" />
              @endif
            <div class="space-y-6">
              @foreach ($filters as $title => $items)
                @php
                  $isOpen = $loop->first || match ($title) {
                      'Kategori' => ! empty($category),
                      'Ukuran' => $selectedSizes->isNotEmpty(),
                      'Warna' => $selectedColors->isNotEmpty(),
                      'Harga' => $selectedPrices->isNotEmpty(),
                      default => false,
                  };
                @endphp
                <details id="filter-details-{{ Str::slug($title) }}" class="border-b border-[var(--border)] pb-5" {{ $isOpen ? 'open' : '' }}>
                  <summary class="flex cursor-pointer list-none items-center justify-between text-[14px] font-bold text-[var(--ink)]">
                    {{ $title }}
                    <svg viewBox="0 0 24 24" class="h-4 w-4 fill-none stroke-[var(--muted)] stroke-[1.8]"><path d="m6 9 6 6 6-6" /></svg>
                  </summary>
                  <div class="mt-4 space-y-3">
                    @foreach ($items as $item)
                      @php
                        $inputName = $title === 'Kategori' ? 'category' : ($title === 'Ukuran' ? 'size[]' : ($title === 'Warna' ? 'color[]' : 'price[]'));
                        $isChecked = match ($title) {
                          'Kategori' => ($category ?? '') === $item['value'] || (($category ?? '') === '' && $item['value'] === ''),
                          'Ukuran' => $selectedSizes->contains($item['value']),
                          'Warna' => $selectedColors->contains($item['value']),
                          default => $selectedPrices->contains($item['value']),
                        };
                      @endphp
                      <label class="flex cursor-pointer items-center gap-3 text-[13px] text-[var(--muted)] transition hover:text-[var(--ink)]">
                        <input type="{{ $title === 'Kategori' ? 'radio' : 'checkbox' }}" name="{{ $inputName }}" value="{{ $item['value'] }}" data-filter-group="{{ Str::slug($title) }}" class="h-4 w-4 rounded border-[var(--border)] accent-[var(--brown)]" {{ $isChecked ? 'checked' : '' }} />
                        {{ $item['label'] }}
                      </label>
                    @endforeach
                  </div>
                </details>
              @endforeach
            </div>
            </form>
          </aside>

          {{-- Product grid --}}
          <div class="min-w-0">
            <div id="product-empty-filter" class="hidden rounded-[8px] border border-[var(--border)] bg-[var(--white)] px-6 py-16 text-center">
              <h2 class="mb-3 text-[28px] text-[var(--ink)]" style="font-family:'Plus Jakarta Sans',system-ui,sans-serif;font-weight:500;">Produk tidak ditemukan</h2>
            <p class="text-[14px] leading-6 text-[var(--muted)]">Coba ubah filter untuk menemukan koleksi pilihanmu.</p>
            </div>
            @if ($produks->isEmpty())
              <div class="rounded-[8px] border border-[var(--border)] bg-[var(--white)] px-6 py-16 text-center">
                <h2 class="mb-3 text-[28px] text-[var(--ink)]" style="font-family:'Plus Jakarta Sans',system-ui,sans-serif;font-weight:500;">Produk tidak ditemukan</h2>
                <p class="mx-auto mb-6 max-w-[420px] text-[14px] leading-6 text-[var(--muted)]">Coba kata kunci lain, atau lihat beberapa koleksi pilihan Auraquina di bawah ini.</p>
                <div class="mb-8 flex flex-wrap justify-center gap-3">
                  <button type="button" data-search-trigger class="inline-flex h-11 items-center justify-center rounded-[4px] bg-[var(--brown)] px-7 text-[12px] font-bold uppercase tracking-[0.1em] text-[var(--white)] transition hover:opacity-90">Cari Lagi</button>
                  <a href="{{ ! empty($category) ? '/shop?category=' . urlencode($category) : '/shop' }}" class="inline-flex h-11 items-center justify-center rounded-[4px] border border-[var(--border)] bg-[var(--white)] px-7 text-[12px] font-bold uppercase tracking-[0.1em] text-[var(--ink)] transition hover:bg-[var(--cream)]">Reset</a>
                </div>
                @if (($produkSaran ?? collect())->isNotEmpty())
                  <div class="mx-auto max-w-[640px] text-left">
                    <p class="mb-4 text-center text-[11px] font-bold uppercase tracking-[0.16em] text-[var(--brown)]">Lihat juga</p>
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                      @foreach ($produkSaran as $saran)
                        @php
                          $suggestionImage = $productImageUrl($saran->gambarUtama?->url, 'card') ?? '';
                          $suggestionSrcset = $productImageSrcset($saran->gambarUtama?->url);
                        @endphp
                        <a href="/shop/{{ $saran->slug }}" class="group block text-center text-[var(--ink)]">
                          <span class="block rounded-[4px] bg-[var(--sand)]">
                            @if ($suggestionImage !== '')
                              <img src="{{ $suggestionImage }}" @if ($suggestionSrcset) srcset="{{ $suggestionSrcset }}" sizes="(max-width: 640px) 50vw, 160px" @endif alt="{{ $saran->nama }}" loading="lazy" decoding="async" class="h-auto w-full rounded-[4px] object-contain transition-transform duration-300 group-hover:scale-[1.01]" />
                            @else
                              <span class="flex aspect-[3/4] items-center justify-center rounded-[4px] px-3 text-center text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--brown)]">Foto segera hadir</span>
                            @endif
                          </span>
                          <span class="mt-2 block truncate text-[12px]">{{ $saran->nama }}</span>
                        </a>
                      @endforeach
                    </div>
                  </div>
                @endif
              </div>
            @else
              <div id="product-grid" class="grid grid-cols-3 gap-5 max-lg:grid-cols-2 max-lg:gap-[14px] max-sm:grid-cols-2 max-sm:gap-x-3 max-sm:gap-y-5">
                @foreach ($produks as $index => $produk)
                  @php
                    $cardImage = $productImageUrl($produk->gambarUtama?->url, 'card') ?? '';
                    $cardSrcset = $productImageSrcset($produk->gambarUtama?->url);
                  @endphp
                  <a class="group block text-[var(--ink)]" href="/shop/{{ $produk->slug }}" data-product-card data-category="{{ $produk->kategori->nama }}" data-category-slug="{{ $produk->kategori->slug }}" data-price="{{ $produk->harga }}" data-sizes="{{ $produk->varians->pluck('ukuran')->unique()->implode(',') }}" data-colors="{{ $produk->varians->pluck('warna')->unique()->implode(',') }}">
                    <span class="block overflow-hidden rounded-[4px] bg-[var(--sand)]" style="aspect-ratio:3/4;">
                      @if ($cardImage !== '')
                        <img src="{{ $cardImage }}" @if ($cardSrcset) srcset="{{ $cardSrcset }}" sizes="(max-width: 640px) 50vw, (max-width: 1024px) 50vw, 310px" @endif alt="{{ $produk->nama }}" loading="{{ $index < 3 ? 'eager' : 'lazy' }}" fetchpriority="{{ $index < 3 ? 'high' : 'auto' }}" decoding="async" width="480" height="640" class="h-full w-full rounded-[4px] object-cover object-top transition-transform duration-300 ease-out group-hover:scale-[1.01]" />
                      @else
                        <span class="flex h-full w-full items-center justify-center px-4 text-center text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--brown)]">Foto segera hadir</span>
                      @endif
                    </span>
                    <span class="block pt-3 max-sm:pt-2">
                      <p class="mb-1 text-[11px] font-bold uppercase tracking-[0.06em] text-[var(--brown)] max-sm:text-[9px]">{{ $produk->kategori->nama }}</p>
                      <p class="mb-1.5 text-[14px] leading-5 text-[var(--ink)] max-sm:text-[11px] max-sm:leading-[15px]">{{ $produk->nama }}</p>
                      <p class="text-[14px] leading-5 font-bold text-[var(--ink)] max-sm:text-[11px] max-sm:leading-[15px]">{{ $produk->hargaFormatted() }}</p>
                    </span>
                  </a>
                @endforeach
              </div>
            @endif

            {{-- Pagination --}}
            <div id="product-pagination" class="mt-10 flex items-center justify-center gap-2"></div>
          </div>
        </div>
      </section>
    </main>

    {{-- Footer --}}
    @include('components.site-footer')

    {{-- WhatsApp FAB --}}
    <a class="fixed right-[22px] bottom-[18px] z-[90] flex h-11 w-11 items-center justify-center rounded-xl bg-[var(--brown)] text-[var(--white)] max-lg:right-3 max-lg:bottom-3" href="https://wa.me/6287711516373" aria-label="WhatsApp">
      <svg aria-hidden="true" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
        <path d="M20.52 3.48A11.93 11.93 0 0 0 12 0C5.37 0 0 5.37 0 12a11.93 11.93 0 0 0 1.64 6.06L0 24l6.16-1.61A11.93 11.93 0 0 0 12 24c6.63 0 12-5.37 12-12 0-3.19-1.25-6.2-3.48-8.52zM12 21.8a9.78 9.78 0 0 1-5-1.37l-.36-.21-3.66.96.98-3.57-.23-.37A9.8 9.8 0 1 1 21.8 12 9.8 9.8 0 0 1 12 21.8zm5.36-7.34c-.29-.15-1.74-.86-2-.96s-.46-.15-.66.15-.76.96-.93 1.16-.34.22-.63.07a8.06 8.06 0 0 1-2.36-1.46 8.86 8.86 0 0 1-1.63-2.04c-.17-.29 0-.45.13-.6s.29-.34.43-.5a2 2 0 0 0 .29-.5.55.55 0 0 0 0-.5c-.07-.15-.66-1.6-.91-2.18s-.48-.5-.66-.5h-.57a1.1 1.1 0 0 0-.8.37 3.36 3.36 0 0 0-1.05 2.5 5.83 5.83 0 0 0 1.22 3.1 13.34 13.34 0 0 0 5.13 4.53c.71.31 1.27.5 1.7.64a4.13 4.13 0 0 0 1.88.12 3.07 3.07 0 0 0 2-1.42 2.5 2.5 0 0 0 .17-1.42c-.07-.12-.27-.2-.56-.34z" />
      </svg>
    </a>

    <script>
      // Restore & save filter details open/close state
      document.querySelectorAll('details[id^="filter-details-"]').forEach(details => {
        const id = details.id;
        const savedState = sessionStorage.getItem(id);
        if (savedState !== null) {
          if (savedState === 'true') {
            details.setAttribute('open', '');
          } else {
            details.removeAttribute('open');
          }
        }
        details.addEventListener('toggle', () => {
          sessionStorage.setItem(id, details.open ? 'true' : 'false');
        });
      });

      // Bottom sheet logic
      function initSheet(btnId, sheetId, backdropId, panelId, closeId, applyId) {
        const btn = document.getElementById(btnId);
        const sheet = document.getElementById(sheetId);
        const backdrop = document.getElementById(backdropId);
        const panel = document.getElementById(panelId);
        const closeBtn = document.getElementById(closeId);
        const applyBtn = document.getElementById(applyId);

        if (!btn || !sheet || !panel) return;

        function open() {
          sheet.classList.remove('pointer-events-none');
          backdrop.classList.replace('bg-[var(--ink)]/0', 'bg-[var(--ink)]/40');
          panel.classList.remove('translate-y-full');
          panel.classList.add('translate-y-0');
          document.body.style.overflow = 'hidden';
        }

        function close() {
          backdrop.classList.replace('bg-[var(--ink)]/40', 'bg-[var(--ink)]/0');
          panel.classList.remove('translate-y-0');
          panel.classList.add('translate-y-full');
          setTimeout(() => {
            sheet.classList.add('pointer-events-none');
            document.body.style.overflow = '';
          }, 300);
        }

        btn.addEventListener('click', open);
        backdrop.addEventListener('click', close);
        closeBtn?.addEventListener('click', close);
        applyBtn?.addEventListener('click', close);
      }

      initSheet('mobile-filter-btn', 'filter-sheet', 'filter-backdrop', 'filter-panel', 'filter-close', 'filter-apply');
      initSheet('mobile-sort-btn', 'sort-sheet', 'sort-backdrop', 'sort-panel', 'sort-close', 'sort-apply');

      let productCards = Array.from(document.querySelectorAll('[data-product-card]'));
      const emptyFilter = document.getElementById('product-empty-filter');
      const productPagination = document.getElementById('product-pagination');
      const itemsPerPage = 24;
      let currentPage = 0;
      let filteredCards = productCards;

      function perPage() {
        return itemsPerPage;
      }

      function selectedFilters(group) {
        return Array.from(document.querySelectorAll(`[data-filter-group="${group}"]:checked`)).map((input) => input.value);
      }

      function submitMobileFilters() {
        const params = new URLSearchParams();
        const search = @json($searchTerm ?? '');
        if (search) params.set('search', search);

        const category = selectedFilters('kategori').find((value) => value !== '');
        if (category) params.set('category', category);

        selectedFilters('ukuran').forEach((value) => params.append('size[]', value));
        selectedFilters('warna').forEach((value) => params.append('color[]', value));
        selectedFilters('harga').forEach((value) => params.append('price[]', value));

        const sort = document.querySelector('#sort-sheet input[name="sort"]:checked')?.value;
        if (sort && sort !== 'featured') params.set('sort', sort);

        const newUrl = `/shop${params.toString() ? `?${params.toString()}` : ''}`;
        loadProductsAjax(newUrl);
      }

      function buttonClass(active = false) {
        return active
          ? 'flex h-9 w-9 items-center justify-center rounded-full bg-[var(--brown)] text-[13px] font-bold text-[var(--white)]'
          : 'flex h-9 w-9 items-center justify-center rounded-full text-[13px] font-bold text-[var(--ink)] transition hover:bg-[var(--cream)]';
      }

      function renderPagination() {
        if (!productPagination) return;

        const totalPages = Math.ceil(filteredCards.length / perPage());
        productPagination.innerHTML = '';
        productPagination.classList.toggle('hidden', totalPages <= 1);

        if (totalPages <= 1) return;

        const prev = document.createElement('button');
        prev.type = 'button';
        prev.className = 'flex h-9 w-9 items-center justify-center rounded-full border border-[var(--border)] bg-[var(--white)] text-[var(--ink)] transition hover:bg-[var(--cream)] disabled:opacity-30';
        prev.disabled = currentPage === 0;
        prev.innerHTML = '<svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4 stroke-[2]"><path d="M19 12H5M12 5l-7 7 7 7" /></svg>';
        prev.addEventListener('click', () => goToPage(currentPage - 1));
        productPagination.appendChild(prev);

        for (let page = 0; page < totalPages; page += 1) {
          const button = document.createElement('button');
          button.type = 'button';
          button.textContent = page + 1;
          button.className = buttonClass(page === currentPage);
          button.addEventListener('click', () => goToPage(page));
          productPagination.appendChild(button);
        }

        const next = document.createElement('button');
        next.type = 'button';
        next.className = 'flex h-9 w-9 items-center justify-center rounded-full border border-[var(--border)] bg-[var(--white)] text-[var(--ink)] transition hover:bg-[var(--cream)] disabled:opacity-30';
        next.disabled = currentPage >= totalPages - 1;
        next.innerHTML = '<svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4 stroke-[2]"><path d="M5 12h14M12 5l7 7-7 7" /></svg>';
        next.addEventListener('click', () => goToPage(currentPage + 1));
        productPagination.appendChild(next);
      }

      function goToPage(page) {
        const totalPages = Math.ceil(filteredCards.length / perPage());
        currentPage = Math.max(0, Math.min(page, totalPages - 1));
        const pageSize = perPage();
        const start = currentPage * pageSize;
        const end = start + pageSize;

        productCards.forEach((card) => card.classList.add('hidden'));
        filteredCards.slice(start, end).forEach((card) => card.classList.remove('hidden'));
        renderPagination();
      }

      function loadProductsAjax(url) {
        const gridContainer = document.getElementById('product-grid');
        const paginationContainer = document.getElementById('product-pagination');
        const emptyFilterContainer = document.getElementById('product-empty-filter');

        if (gridContainer) {
          gridContainer.style.opacity = '0.3';
          gridContainer.style.transition = 'opacity 0.15s ease';
        }

        fetch(url, {
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        })
        .then(response => response.text())
        .then(html => {
          const parser = new DOMParser();
          const doc = parser.parseFromString(html, 'text/html');

          const newGrid = doc.getElementById('product-grid');
          if (newGrid && gridContainer) {
            gridContainer.innerHTML = newGrid.innerHTML;
          }

          const newPagination = doc.getElementById('product-pagination');
          if (newPagination && paginationContainer) {
            paginationContainer.innerHTML = newPagination.innerHTML;
            paginationContainer.className = newPagination.className;
          }

          const newEmpty = doc.getElementById('product-empty-filter');
          if (newEmpty && emptyFilterContainer) {
            emptyFilterContainer.innerHTML = newEmpty.innerHTML;
            emptyFilterContainer.className = newEmpty.className;
          }

          window.history.pushState(null, '', url);

          productCards = Array.from(document.querySelectorAll('[data-product-card]'));
          filteredCards = productCards;
          currentPage = 0;
          renderPagination();
          goToPage(0);

          if (gridContainer) {
            gridContainer.style.opacity = '1';
          }
        })
        .catch(err => {
          console.error(err);
          window.location.href = url;
        });
      }

      function submitFiltersAjax() {
        const form = document.getElementById('shop-filter-form');
        if (!form) return;
        const formData = new FormData(form);
        const params = new URLSearchParams();

        for (const [key, value] of formData.entries()) {
          if (value !== '') {
            params.append(key, value);
          }
        }

        const search = @json($searchTerm ?? '');
        if (search) params.set('search', search);

        const sortSelect = document.querySelector('select[name="sort"]');
        if (sortSelect && sortSelect.value && sortSelect.value !== 'featured') {
          params.set('sort', sortSelect.value);
        }

        const newUrl = `/shop${params.toString() ? `?${params.toString()}` : ''}`;
        loadProductsAjax(newUrl);
      }

      // Initialize page
      goToPage(0);

      // Listen for filter changes
      document.querySelectorAll('#shop-filter-form input, select[name="sort"]').forEach((el) => {
        el.addEventListener('change', submitFiltersAjax);
      });

      document.getElementById('filter-apply')?.addEventListener('click', submitMobileFilters);
      document.getElementById('sort-apply')?.addEventListener('click', submitMobileFilters);
    </script>
  </body>
</html>
