@php
  $searchValue = $searchValue ?? request('search', '');
@endphp

<div id="search-overlay" class="pointer-events-none fixed inset-0 z-[160] hidden opacity-0 transition duration-200">
  <button type="button" data-search-close class="absolute inset-0 bg-[rgba(32,25,22,0.38)]" aria-label="Tutup pencarian"></button>
  <div class="relative mx-auto mt-20 w-[min(680px,calc(100vw-24px))] rounded-[8px] border border-[var(--border)] bg-[var(--white)] p-4 shadow-[0_20px_60px_rgba(32,25,22,0.16)] max-sm:mt-16 max-sm:p-3" data-search-panel>
    <div class="mb-3 flex items-center justify-between gap-3">
      <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-[var(--brown)]">Cari Produk</p>
      <button type="button" data-search-close class="flex h-9 w-9 items-center justify-center rounded-full text-[var(--muted)] transition hover:bg-[var(--cream)] hover:text-[var(--ink)]" aria-label="Close">
        <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="stroke-[1.8]"><path d="M6 6l12 12M18 6 6 18" /></svg>
      </button>
    </div>
    <form action="/shop" method="GET" class="flex items-center gap-3 max-sm:flex-col max-sm:items-stretch">
      <div class="flex flex-1 items-center gap-3 rounded-[6px] border border-[var(--border)] bg-[var(--warm)] px-4">
        <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="shrink-0 text-[var(--muted)] stroke-[1.7]"><circle cx="11" cy="11" r="7" /><path d="m20 20-3.5-3.5" /></svg>
        <input id="search-input" name="search" value="{{ $searchValue }}" type="search" autocomplete="off" placeholder="Cari abaya, warna mocha, khimar..." class="h-12 w-full bg-transparent text-[14px] text-[var(--ink)] outline-none placeholder:text-[var(--muted)]" />
      </div>
      <button type="submit" class="flex h-12 items-center justify-center rounded-[6px] bg-[var(--brown)] px-6 text-[12px] font-bold uppercase tracking-[0.1em] text-[var(--white)] transition hover:opacity-90">Cari</button>
    </form>
    <div data-search-results class="mt-3 hidden overflow-hidden rounded-[6px] border border-[var(--border)] bg-[var(--warm)]" role="listbox" aria-label="Hasil pencarian cepat"></div>
  </div>
</div>
