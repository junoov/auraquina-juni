@php
  $kategoris = $kategoris ?? collect();
  $backHref = $backHref ?? '/';
  $accountHref = auth()->check() ? route('account.show') : route('login');
@endphp

{{-- Desktop Header (same style as shop.blade.php) --}}
<header class="sticky top-0 text-[var(--ink)] max-lg:hidden" style="z-index:9990;">
  <div class="relative flex h-[86px] items-center px-12">
    <nav class="mr-auto flex items-center gap-1" aria-label="Primary navigation">
      <a class="flex h-9 items-center px-3.5 text-[14px] leading-5 whitespace-nowrap {{ request()->is('/') ? 'font-bold text-[var(--brown)]' : 'text-[var(--ink)]' }}" href="/">Home</a>
      <a class="flex h-9 items-center px-3.5 text-[14px] leading-5 whitespace-nowrap {{ request()->is('shop') ? 'font-bold text-[var(--brown)]' : 'text-[var(--ink)]' }}" href="/shop">All Product</a>
      <div class="group relative flex h-9 items-center px-3.5 text-[14px] leading-5 whitespace-nowrap text-[var(--ink)]">
        Collection
        <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="ml-0.5 h-4 w-4 stroke-[1.7]"><path d="m6 9 6 6 6-6" /></svg>
        <div class="pointer-events-none absolute top-9 left-1/2 z-80 min-w-[214px] -translate-x-1/2 translate-y-2 border border-[var(--border)] bg-[var(--white)] py-2 opacity-0 shadow-[0_8px_24px_rgba(131,81,61,0.12)] transition duration-200 group-hover:pointer-events-auto group-hover:translate-y-0 group-hover:opacity-100">
          <a class="block px-4 py-2 text-[13px] leading-[18px] text-[var(--ink)] hover:bg-[var(--cream)]" href="/shop">New Arrival</a>
          @foreach ($kategoris as $kategori)
            <a class="block px-4 py-2 text-[13px] leading-[18px] text-[var(--ink)] hover:bg-[var(--cream)]" href="/shop?category={{ urlencode($kategori->slug) }}">{{ $kategori->nama }}</a>
          @endforeach
        </div>
      </div>
      <a class="flex h-9 items-center px-3.5 text-[14px] leading-5 whitespace-nowrap {{ request()->is('sale') ? 'font-bold text-[var(--brown)]' : 'text-[var(--ink)]' }}" href="/sale">Sale</a>
      <a class="flex h-9 items-center px-3.5 text-[14px] leading-5 whitespace-nowrap {{ request()->is('about') ? 'font-bold text-[var(--brown)]' : 'text-[var(--ink)]' }}" href="/about">About</a>
    </nav>

    <a class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 text-[28px] lg:text-[32px] leading-none font-medium tracking-[0.03em] text-[var(--ink)]" href="/" aria-label="Auraquina" style="font-family: 'Cormorant Garamond', Georgia, serif;">Auraquina</a>

    <div class="ml-3 flex items-center gap-1">
      @foreach ([
        '<circle cx="12" cy="8" r="4" /><path d="M4 21a8 8 0 0 1 16 0" />' => 'Account',
        '<circle cx="11" cy="11" r="7" /><path d="m20 20-3.5-3.5" />' => 'Search',
        '<circle cx="9" cy="20" r="1.5" /><circle cx="17" cy="20" r="1.5" /><path d="M3.5 4h2.1l2.2 11.2a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 1.9-1.4L21 8H7" />' => 'Cart',
      ] as $icon => $label)
        @if ($label === 'Cart')
          <button type="button" onclick="openCart()" aria-label="Cart" class="relative flex h-9 w-9 items-center justify-center rounded-lg text-[var(--ink)] transition hover:bg-[var(--cream)]">
            <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">{!! $icon !!}</svg>
            <span data-cart-count-badge class="icon-count-badge is-hidden">0</span>
          </button>
        @else
          <a href="{{ $label === 'Account' ? $accountHref : '#' }}" @if ($label === 'Search') data-search-trigger @endif aria-label="{{ $label }}" class="relative flex h-9 w-9 items-center justify-center rounded-lg text-[var(--ink)] transition hover:bg-[var(--cream)]">
            <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">{!! $icon !!}</svg>
          </a>
        @endif
      @endforeach
    </div>
  </div>
</header>

  {{-- Mobile Header: Hamburger + AURAQUINA + Search/Cart --}}
<header class="sticky top-0 hidden max-lg:block" style="z-index:9990;">
  <div class="relative flex h-[72px] items-center justify-between px-4">
    {{-- Left: hamburger --}}
    <div class="flex items-center" style="width:80px;">
      <button type="button" id="menu-open" aria-label="Menu" class="flex h-10 w-10 items-center justify-center rounded-lg text-[var(--ink)] transition hover:bg-[var(--cream)]">
        <svg aria-hidden="true" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="stroke-[1.8]"><path d="M4 6h16M4 12h16M4 18h16" /></svg>
      </button>
    </div>
    {{-- Center: logo (absolute centered) --}}
    <a class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 text-[22px] leading-none font-medium tracking-[0.03em] text-[var(--ink)]" href="/" aria-label="Auraquina" style="font-family: 'Cormorant Garamond', Georgia, serif;">Auraquina</a>
    {{-- Right: search + cart --}}
    <div class="flex items-center gap-1" style="width:80px;justify-content:flex-end;">
      <button type="button" data-search-trigger aria-label="Search" class="flex h-10 w-10 items-center justify-center rounded-lg text-[var(--ink)] transition hover:bg-[var(--cream)]">
        <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="stroke-[1.7]"><circle cx="11" cy="11" r="7" /><path d="m20 20-3.5-3.5" /></svg>
      </button>
      <button type="button" onclick="openCart()" aria-label="Cart" class="relative flex h-10 w-10 items-center justify-center rounded-lg text-[var(--ink)] transition hover:bg-[var(--cream)]">
        <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="stroke-[1.7]"><circle cx="9" cy="20" r="1.5" /><circle cx="17" cy="20" r="1.5" /><path d="M3.5 4h2.1l2.2 11.2a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 1.9-1.4L21 8H7" /></svg>
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
    <button type="button" data-search-trigger aria-label="Search" class="flex h-10 w-10 items-center justify-center rounded-lg text-[var(--ink)] transition hover:bg-[var(--cream)]">
      <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="stroke-[1.7]"><circle cx="11" cy="11" r="7" /><path d="m20 20-3.5-3.5" /></svg>
    </button>
    <button id="menu-close" type="button" aria-label="Close" class="flex h-10 w-10 items-center justify-center rounded-lg text-[var(--ink)] transition hover:bg-[var(--cream)]">
      <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="stroke-[1.7]"><path d="M6 6l12 12M18 6 6 18" /></svg>
    </button>
  </div>
  <nav class="flex flex-col px-5 py-4 text-[15px] leading-[22px]">
    <a class="border-b border-[var(--border)] py-4 {{ request()->is('/') ? 'font-bold text-[var(--brown)]' : '' }}" href="/">Home</a>
    <a class="border-b border-[var(--border)] py-4 {{ request()->is('shop') ? 'font-bold text-[var(--brown)]' : '' }}" href="/shop">All Product</a>
    <a class="border-b border-[var(--border)] py-4" href="/shop">Collection</a>
    <a class="border-b border-[var(--border)] py-4 {{ request()->is('sale') ? 'font-bold text-[var(--brown)]' : '' }}" href="/sale">Sale</a>
    <a class="border-b border-[var(--border)] py-4 {{ request()->is('about') ? 'font-bold text-[var(--brown)]' : '' }}" href="/about">About</a>
    <a class="border-b border-[var(--border)] py-4 font-bold text-[var(--brown)]" href="{{ $accountHref }}">{{ auth()->check() ? 'Akun Saya' : 'Masuk' }}</a>
  </nav>
</div>

<script>
  // Mobile menu open/close (wrapped in IIFE to avoid const conflicts)
  (function() {
    const menu = document.getElementById('mobile-menu');
    const backdrop = document.getElementById('mobile-menu-backdrop');
    const openBtn = document.getElementById('menu-open');
    const closeBtn = document.getElementById('menu-close');
    if (!menu || !openBtn) return;

    let scrollY = 0;

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

    // Close menu when search is triggered inside drawer
    menu.querySelectorAll('[data-search-trigger]').forEach(function(btn) {
      btn.addEventListener('click', function() {
        closeMenu();
      });
    });
  })();
</script>
