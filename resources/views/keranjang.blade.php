<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Auraquina - Keranjang</title>
    <meta name="description" content="Keranjang belanja Auraquina." />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="icon" href="https://d2kchovjbwl1tk.cloudfront.net/vendors/292/assets/image/1769740142660-Untitled-1_resized128-png.webp" />
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,500&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>
  <body class="min-h-screen overflow-x-hidden bg-[var(--warm)] text-[var(--text)] antialiased [text-rendering:geometricPrecision]">
    @php
      $containerClass = 'mx-auto w-[min(1120px,calc(100vw-32px))] max-lg:w-[calc(100vw-28px)]';
      $shippingEstimate = $subtotal > 0 ? 11500 : 0;
      $grandTotal = $subtotal + $shippingEstimate;
    @endphp

    @include('components.site-header', ['kategoris' => collect(), 'backHref' => '/shop'])

    <main class="{{ $containerClass }} pt-[100px] pb-5 max-lg:pt-[86px] max-lg:pb-4">
      @if ($items->isEmpty())
        <section class="grid min-h-[420px] place-items-center rounded-[8px] border border-[var(--border)] bg-[var(--white)] px-6 py-16 text-center shadow-[0_2px_12px_rgba(131,81,61,0.04)]">
          <div class="max-w-[420px]">
            <div class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-full bg-[var(--cream)] text-[var(--brown)]">
              <svg aria-hidden="true" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="stroke-[1.5]"><circle cx="9" cy="20" r="1.5" /><circle cx="17" cy="20" r="1.5" /><path d="M3.5 4h2.1l2.2 11.2a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 1.9-1.4L21 8H7" /></svg>
            </div>
            <h2 class="mb-3 text-[32px] leading-tight text-[var(--ink)]" style="font-family:'Plus Jakarta Sans',system-ui,sans-serif;font-weight:500;">Keranjang masih kosong</h2>
          <p class="mb-7 text-[14px] leading-6 text-[var(--muted)]">Temukan abaya dan khimar pilihanmu, lalu tambahkan ke keranjang dari halaman produk.</p>
            <a href="/shop" class="inline-flex h-11 items-center justify-center rounded-[4px] bg-[var(--brown)] px-7 text-[12px] font-bold tracking-[0.1em] text-[var(--white)] uppercase transition hover:opacity-85">Mulai Belanja</a>
          </div>
        </section>
      @else
        <section class="rounded-[8px] border border-[var(--border)] bg-[var(--white)] shadow-[0_2px_12px_rgba(131,81,61,0.04)]">
          <div class="flex items-center justify-between border-b border-[var(--border)] px-6 py-4 text-[12px] text-[var(--muted)] max-sm:px-4">
            <label class="flex cursor-pointer items-center gap-3">
              <input id="select-all-cart" type="checkbox" class="h-4 w-4 accent-[var(--brown)]" checked />
              <span class="font-medium tracking-wide">Pilih semua</span>
            </label>
            <button id="delete-selected" type="button" class="text-[11px] font-bold uppercase tracking-[0.06em] text-[var(--muted)] transition hover:text-[var(--brown)]">Hapus terpilih</button>
          </div>

          <div id="cart-items" class="divide-y divide-[var(--border)]">
            @foreach ($items as $item)
              @php
                $price = $item->produk->harga + ($item->varian?->penyesuaian_harga ?? 0);
                $itemSubtotal = $price * $item->jumlah;
              @endphp
              <article class="cart-item grid grid-cols-[20px_104px_minmax(0,1fr)_auto] gap-5 px-6 py-6 max-sm:grid-cols-[20px_84px_minmax(0,1fr)] max-sm:gap-3 max-sm:px-4 max-sm:py-4" data-cart-id="{{ $item->id }}">
                <input type="checkbox" class="cart-check mt-12 h-4 w-4 accent-[var(--brown)] max-sm:mt-9" checked aria-label="Pilih {{ $item->produk->nama }}" />
                <a href="/shop/{{ $item->produk->slug }}" class="block aspect-[3/4] overflow-hidden rounded-[4px] bg-[var(--cream)]">
                  <img src="{{ $item->produk->gambarUtama?->url ?? '' }}" alt="{{ $item->produk->nama }}" class="h-full w-full object-cover transition-transform duration-300 hover:scale-[1.03]" />
                </a>
                <div class="min-w-0 py-1">
                  <p class="mb-1.5 text-[10px] font-bold uppercase tracking-[0.16em] text-[var(--brown)]">{{ $item->produk->kategori->nama }}</p>
                  <a href="/shop/{{ $item->produk->slug }}" class="mb-2.5 block truncate text-[18px] leading-tight text-[var(--ink)] transition hover:text-[var(--brown)] max-sm:text-[15px]" style="font-family:'Plus Jakarta Sans',system-ui,sans-serif;font-weight:500;letter-spacing:-0.01em;">{{ $item->produk->nama }}</a>
                  <div class="mb-3 inline-flex items-center gap-1.5 rounded-[3px] border border-[var(--border)] bg-[var(--warm)] px-2.5 py-1 text-[11px] text-[var(--ink)]">
                    @if ($item->varian?->kode_warna)
                      <span class="inline-block h-3 w-3 rounded-full border border-[var(--border)]" style="background:{{ $item->varian->kode_warna }};"></span>
                    @endif
                    <span class="text-[var(--muted)]">{{ $item->varian?->warna ?? 'Default' }}</span>
                    <span class="text-[var(--border)]">•</span>
                    <span class="font-bold">{{ $item->varian?->ukuran ?? '-' }}</span>
                  </div>
                  <div class="mb-4 flex items-baseline gap-2">
                    @if ($item->produk->harga_coret)
                      <span class="text-[12px] text-[var(--muted)] line-through">Rp {{ number_format($item->produk->harga_coret, 0, ',', '.') }}</span>
                    @endif
                    <span class="text-[15px] font-bold text-[var(--ink)]" style="font-family:'Plus Jakarta Sans',system-ui,sans-serif;letter-spacing:-0.01em;">Rp {{ number_format($price, 0, ',', '.') }}</span>
                  </div>
                  <div class="flex items-center justify-between gap-3">
                    <div class="flex h-9 items-center rounded-[4px] border border-[var(--border)] bg-[var(--white)]">
                      <button type="button" class="cart-qty flex h-full w-9 items-center justify-center text-[16px] text-[var(--muted)] transition hover:bg-[var(--cream)] hover:text-[var(--brown)]" data-action="decrease" aria-label="Kurangi jumlah">−</button>
                      <span class="cart-qty-value w-9 text-center text-[13px] font-bold text-[var(--ink)]">{{ $item->jumlah }}</span>
                      <button type="button" class="cart-qty flex h-full w-9 items-center justify-center text-[16px] text-[var(--muted)] transition hover:bg-[var(--cream)] hover:text-[var(--brown)]" data-action="increase" aria-label="Tambah jumlah">+</button>
                    </div>
                    <button type="button" class="cart-remove hidden text-[11px] font-bold uppercase tracking-[0.06em] text-[var(--muted)] hover:text-[var(--brown)] max-sm:inline">Hapus</button>
                  </div>
                </div>
                <div class="flex flex-col items-end justify-between py-1 max-sm:hidden">
                  <button type="button" class="cart-remove text-[11px] font-bold uppercase tracking-[0.06em] text-[var(--muted)] transition hover:text-[var(--brown)]" aria-label="Hapus item">Hapus</button>
                  <p class="cart-line-total text-[16px] font-bold text-[var(--ink)]" data-price="{{ $price }}" style="font-family:'Plus Jakarta Sans',system-ui,sans-serif;letter-spacing:-0.01em;">Rp {{ number_format($itemSubtotal, 0, ',', '.') }}</p>
                </div>
              </article>
            @endforeach
          </div>

          <div class="sticky bottom-0 flex items-center justify-between gap-4 rounded-b-[8px] border-t border-[var(--border)] bg-[var(--white)] px-6 py-4 shadow-[0_-4px_16px_rgba(32,25,22,0.04)] max-sm:px-4">
            <label class="flex cursor-pointer items-center gap-3 text-[12px] text-[var(--muted)] max-sm:hidden">
              <input id="select-all-cart-bottom" type="checkbox" class="h-4 w-4 accent-[var(--brown)]" checked />
              <span class="font-medium">Semua</span>
            </label>
            <div class="ml-auto text-right">
              <p class="text-[11px] uppercase tracking-[0.08em] text-[var(--muted)]">Total · <span id="cart-total-item">{{ $totalItem }}</span> item</p>
              <p id="cart-grand-total" class="text-[22px] font-bold text-[var(--brown)]" style="font-family:'Plus Jakarta Sans',system-ui,sans-serif;letter-spacing:-0.015em;">Rp {{ number_format($subtotal, 0, ',', '.') }}</p>
            </div>
            <button id="checkout-selected" type="button" class="flex h-11 min-w-[140px] items-center justify-center rounded-[4px] bg-[var(--brown)] px-6 text-[11px] font-bold tracking-[0.12em] text-[var(--white)] uppercase transition hover:opacity-90">Checkout</button>
          </div>
        </section>
      @endif
    </main>

    <script>
      const rupiah = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 });
      const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

      function formatRupiah(value) {
        return rupiah.format(value).replace(/\s/g, ' ');
      }

      function recalculateCart() {
        const rows = [...document.querySelectorAll('.cart-item')];
        let subtotal = 0;
        let totalItem = 0;
        let cartBadgeTotal = 0;

        rows.forEach((row) => {
          const checked = row.querySelector('.cart-check')?.checked;
          const qty = Number(row.querySelector('.cart-qty-value').textContent);
          const price = Number(row.querySelector('.cart-line-total').dataset.price);
          const lineTotal = price * qty;
          cartBadgeTotal += qty;
          if (checked) {
            totalItem += qty;
            subtotal += lineTotal;
          }
          row.querySelector('.cart-line-total').textContent = formatRupiah(lineTotal);
        });

        document.getElementById('cart-total-item').textContent = totalItem;
        document.getElementById('cart-grand-total').textContent = formatRupiah(subtotal);

        const allChecked = rows.length > 0 && rows.every((row) => row.querySelector('.cart-check').checked);
        document.querySelectorAll('#select-all-cart, #select-all-cart-bottom').forEach((checkbox) => {
          checkbox.checked = allChecked;
        });

        const checkout = document.getElementById('checkout-selected');
        checkout.classList.toggle('pointer-events-none', totalItem === 0);
        checkout.classList.toggle('opacity-45', totalItem === 0);
        window.dispatchEvent(new CustomEvent('cart:changed', { detail: { totalItem: cartBadgeTotal } }));
      }

      async function updateQuantity(row, quantity) {
        const id = row.dataset.cartId;
        await fetch(`/keranjang/${id}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrf,
          },
          body: JSON.stringify({ jumlah: quantity }),
        });
      }

      async function removeItem(row) {
        const id = row.dataset.cartId;
        await fetch(`/keranjang/${id}`, {
          method: 'DELETE',
          headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrf,
          },
        });
        row.remove();
        if (!document.querySelector('.cart-item')) {
          window.location.reload();
          return;
        }
        recalculateCart();
      }

      document.querySelectorAll('.cart-qty').forEach((button) => {
        button.addEventListener('click', async () => {
          const row = button.closest('.cart-item');
          const value = row.querySelector('.cart-qty-value');
          const current = Number(value.textContent);
          const next = button.dataset.action === 'increase' ? current + 1 : Math.max(1, current - 1);
          if (next === current) return;
          value.textContent = next;
          recalculateCart();
          await updateQuantity(row, next);
        });
      });

      document.querySelectorAll('.cart-remove').forEach((button) => {
        button.addEventListener('click', () => removeItem(button.closest('.cart-item')));
      });

      document.querySelectorAll('.cart-check').forEach((checkbox) => {
        checkbox.addEventListener('change', recalculateCart);
      });

      document.querySelectorAll('#select-all-cart, #select-all-cart-bottom').forEach((checkbox) => {
        checkbox.addEventListener('change', () => {
          document.querySelectorAll('.cart-check').forEach((itemCheckbox) => {
            itemCheckbox.checked = checkbox.checked;
          });
          recalculateCart();
        });
      });

      document.getElementById('delete-selected')?.addEventListener('click', () => {
        document.querySelectorAll('.cart-item').forEach((row) => {
          if (row.querySelector('.cart-check').checked) removeItem(row);
        });
      });

      document.getElementById('checkout-selected')?.addEventListener('click', async () => {
        const itemIds = [...document.querySelectorAll('.cart-item')]
          .filter((row) => row.querySelector('.cart-check').checked)
          .map((row) => Number(row.dataset.cartId));

        if (itemIds.length === 0) return;

        const response = await fetch('/checkout/from-cart', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrf,
          },
          body: JSON.stringify({ item_ids: itemIds }),
        });

        const data = await response.json();

        if (data.success && data.redirect) {
          window.location.href = data.redirect;
          return;
        }

        alert(data.error || 'Checkout gagal diproses');
      });

      recalculateCart();
    </script>
  </body>
</html>
