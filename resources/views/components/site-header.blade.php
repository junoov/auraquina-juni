@php
  $kategoris = $kategoris ?? collect();
  $transparent = $transparent ?? false;
  $accountHref = auth()->check() ? route('account.show') : route('login');
@endphp

{{-- Unified Header — supports transparent (homepage) and solid (other pages) modes --}}
<header
  class="site-header sticky top-0 {{ $transparent ? 'site-header--transparent h-0' : '' }}"
  style="z-index:9990; color: var(--ink);"
  @if($transparent) data-transparent @endif
>
  {{-- Desktop Nav --}}
  <div class="site-header__inner relative flex items-center px-12 max-lg:hidden {{ $transparent ? 'absolute inset-x-0 top-0' : '' }}" style="height:86px;">
    <nav class="mr-auto flex items-center gap-1" aria-label="Primary navigation">
      <a class="flex h-9 items-center px-3.5 text-[14px] leading-5 whitespace-nowrap {{ request()->is('/') ? 'font-bold' : '' }}" href="/">Home</a>
      <a class="flex h-9 items-center px-3.5 text-[14px] leading-5 whitespace-nowrap {{ request()->is('shop') ? 'font-bold' : '' }}" href="/shop">All Product</a>
      <div class="group relative flex h-9 items-center px-3.5 text-[14px] leading-5 whitespace-nowrap">
        Collection
        <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="ml-0.5 h-4 w-4 stroke-[1.7]"><path d="m6 9 6 6 6-6" /></svg>
        <div class="pointer-events-none absolute top-9 left-1/2 z-80 min-w-[214px] -translate-x-1/2 translate-y-2 border border-[var(--border)] bg-[var(--white)] py-2 opacity-0 shadow-[0_8px_24px_rgba(131,81,61,0.12)] transition duration-200 group-hover:pointer-events-auto group-hover:translate-y-0 group-hover:opacity-100">
          <a class="block px-4 py-2 text-[13px] leading-[18px] text-[var(--ink)] hover:bg-[var(--cream)]" href="/shop">New Arrival</a>
          @foreach ($kategoris as $kategori)
            <a class="block px-4 py-2 text-[13px] leading-[18px] text-[var(--ink)] hover:bg-[var(--cream)]" href="/shop?category={{ urlencode($kategori->slug) }}">{{ $kategori->nama }}</a>
          @endforeach
        </div>
      </div>
    </nav>

    <a class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 flex items-center gap-2.5 text-[28px] lg:text-[32px] leading-none font-medium tracking-[0.03em]" href="/" aria-label="Auraquina" style="font-family: 'Cormorant Garamond', Georgia, serif;">
      <img src="{{ asset('images/logo.png') }}" alt="" class="h-8 lg:h-9 w-auto object-contain" />
      <span>Auraquina</span>
    </a>

    <div class="ml-3 flex items-center gap-2">
      <button type="button" data-search-trigger aria-label="Search" class="flex h-9 w-9 items-center justify-center rounded-lg transition">
        <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
      </button>

      <a href="{{ $accountHref }}" class="flex items-center gap-2 px-3 h-9 rounded-lg transition" aria-label="Account">
        <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>
        <span class="text-[13px] font-semibold tracking-wide uppercase truncate max-w-[120px]">{{ auth()->check() ? 'Hi, ' . Str::limit(auth()->user()->name, 12) : 'Hi, Guest' }}</span>
      </a>

      <button type="button" onclick="openCart()" aria-label="Cart" class="relative flex h-9 w-9 items-center justify-center rounded-lg transition">
        <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
          <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
          <line x1="3" y1="6" x2="21" y2="6"/>
          <path d="M16 10a4 4 0 0 1-8 0"/>
        </svg>
        <span data-cart-count-badge class="icon-count-badge is-hidden">0</span>
      </button>
    </div>
  </div>

  {{-- Mobile Nav --}}
  <div class="site-header__inner relative flex items-center justify-between px-4 lg:hidden {{ $transparent ? 'absolute inset-x-0 top-0' : '' }}" style="height:72px;">
    {{-- Left: hamburger --}}
    <div class="flex items-center" style="width:80px;">
      <button type="button" id="menu-open" aria-label="Menu" class="flex h-10 w-10 items-center justify-center rounded-lg text-[var(--ink)] transition">
        <svg aria-hidden="true" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="stroke-[1.8]"><path d="M4 6h16M4 12h16M4 18h16" /></svg>
      </button>
    </div>
    {{-- Center: logo --}}
    <a class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 flex items-center gap-2 text-[22px] leading-none font-medium tracking-[0.03em] text-[var(--ink)]" href="/" aria-label="Auraquina" style="font-family: 'Cormorant Garamond', Georgia, serif;">
      <img src="{{ asset('images/logo.png') }}" alt="" class="h-6 w-auto object-contain" />
      <span>Auraquina</span>
    </a>
    {{-- Right: search + cart --}}
    <div class="flex items-center gap-1" style="width:80px;justify-content:flex-end;">
      <button type="button" data-search-trigger aria-label="Search" class="flex h-10 w-10 items-center justify-center rounded-lg text-[var(--ink)] transition">
        <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
      </button>
      <button type="button" onclick="openCart()" aria-label="Cart" class="relative flex h-10 w-10 items-center justify-center rounded-lg text-[var(--ink)] transition">
        <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
          <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
          <line x1="3" y1="6" x2="21" y2="6"/>
          <path d="M16 10a4 4 0 0 1-8 0"/>
        </svg>
        <span data-cart-count-badge class="icon-count-badge is-hidden">0</span>
      </button>
    </div>
  </div>
</header>

@include('components.search-overlay', ['searchValue' => ''])
@include('components.cart-drawer')

{{-- Mobile Menu Drawer --}}
<div id="mobile-menu-backdrop" class="fixed inset-0 bg-[rgba(32,25,22,0.38)] opacity-0 pointer-events-none transition-opacity duration-300" style="z-index: 11000;"></div>
<div id="mobile-menu" class="fixed top-0 left-0 bottom-0 w-[70%] max-w-[300px] -translate-x-full bg-[var(--white)] text-[var(--ink)] shadow-[4px_0_24px_rgba(0,0,0,0.12)] transition-transform duration-300" style="z-index: 12000;">
  <div class="flex h-16 items-center justify-between border-b border-[var(--border)] px-4">
    <button type="button" data-search-trigger aria-label="Search" class="flex h-10 w-10 items-center justify-center rounded-lg text-[var(--ink)] transition">
      <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="stroke-[1.7]"><circle cx="11" cy="11" r="7" /><path d="m20 20-3.5-3.5" /></svg>
    </button>
    <button id="menu-close" type="button" aria-label="Close" class="flex h-10 w-10 items-center justify-center rounded-lg text-[var(--ink)] transition">
      <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="stroke-[1.7]"><path d="M6 6l12 12M18 6 6 18" /></svg>
    </button>
  </div>
  <nav class="flex flex-col px-5 py-4 text-[15px] leading-[22px]">
    <a class="border-b border-[var(--border)] py-4 {{ request()->is('/') ? 'font-bold text-[var(--brown)]' : '' }}" href="/">Home</a>
    <a class="border-b border-[var(--border)] py-4 {{ request()->is('shop') ? 'font-bold text-[var(--brown)]' : '' }}" href="/shop">All Product</a>
    <a class="border-b border-[var(--border)] py-4" href="/shop">Collection</a>
    <a class="border-b border-[var(--border)] py-4 font-bold text-[var(--brown)]" href="{{ $accountHref }}">{{ auth()->check() ? 'Akun Saya' : 'Masuk' }}</a>
  </nav>
</div>

<script>
  (function() {
    /* ---- Transparent header scroll handler ---- */
    var header = document.querySelector('.site-header[data-transparent]');
    if (header) {
      var inners = header.querySelectorAll('.site-header__inner');
      function onScroll() {
        var scrolled = window.scrollY > 60;
        if (scrolled) {
          header.classList.add('site-header--scrolled');
          inners.forEach(function(el) {
            el.style.backgroundColor = 'rgba(255,254,252,0.94)';
            el.style.backdropFilter = 'blur(10px)';
            el.style.webkitBackdropFilter = 'blur(10px)';
            el.style.boxShadow = '0 1px 0 var(--border)';
            el.style.color = '';
          });
        } else {
          header.classList.remove('site-header--scrolled');
          inners.forEach(function(el) {
            el.style.backgroundColor = '';
            el.style.backdropFilter = '';
            el.style.webkitBackdropFilter = '';
            el.style.boxShadow = '';
            el.style.color = '#fff';
          });
        }
      }
      window.addEventListener('scroll', onScroll, { passive: true });
      onScroll();
    }

    /* ---- Mobile menu ---- */
    var menu = document.getElementById('mobile-menu');
    var backdrop = document.getElementById('mobile-menu-backdrop');
    var openBtn = document.getElementById('menu-open');
    var closeBtn = document.getElementById('menu-close');
    if (!menu || !openBtn) return;

    var scrollY = 0;

    function openMenu() {
      scrollY = window.scrollY;
      menu.classList.remove('-translate-x-full');
      menu.classList.add('translate-x-0');
      if (backdrop) {
        backdrop.classList.remove('opacity-0', 'pointer-events-none');
        backdrop.classList.add('opacity-50', 'pointer-events-auto');
      }
      document.documentElement.style.overflow = 'hidden';
      document.body.style.overflow = 'hidden';
      document.body.style.touchAction = 'none';
    }

    function closeMenu() {
      menu.classList.add('-translate-x-full');
      menu.classList.remove('translate-x-0');
      if (backdrop) {
        backdrop.classList.add('opacity-0', 'pointer-events-none');
        backdrop.classList.remove('opacity-50', 'pointer-events-auto');
      }
      document.documentElement.style.overflow = '';
      document.body.style.overflow = '';
      document.body.style.touchAction = '';
      window.scrollTo(0, scrollY);
    }

    if (backdrop) {
      backdrop.addEventListener('touchmove', function(e) { e.preventDefault(); }, { passive: false });
    }

    openBtn.addEventListener('click', openMenu);
    if (closeBtn) closeBtn.addEventListener('click', closeMenu);
    if (backdrop) backdrop.addEventListener('click', closeMenu);

    menu.querySelectorAll('[data-search-trigger]').forEach(function(btn) {
      btn.addEventListener('click', function() {
        closeMenu();
      });
    });
  })();
</script>
