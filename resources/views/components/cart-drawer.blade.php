{{-- Cart Drawer --}}
<style>
  @keyframes cartSlideUp {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0); }
  }
  .cart-slide-item {
    opacity: 0;
    will-change: transform, opacity;
  }
  .cart-slide-item.is-animating {
    animation: cartSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) forwards;
  }
  @media (prefers-reduced-motion: reduce) {
    .cart-slide-item { opacity: 1 !important; }
    .cart-slide-item.is-animating { animation: none !important; }
    #cart-drawer { transition: none !important; }
    #cart-backdrop { transition: none !important; }
  }
  @media (max-width: 480px) {
    #cart-drawer { width: 88% !important; max-width: none !important; }
  }
</style>
{{-- Backdrop --}}
<div id="cart-backdrop" style="position:fixed;inset:0;z-index:9998;pointer-events:none;background:rgba(0,0,0,0);backdrop-filter:blur(0);transition:all 0.6s cubic-bezier(0.22,1,0.36,1);" onclick="closeCart()"></div>

{{-- Panel --}}
<div id="cart-drawer" role="dialog" aria-modal="true" aria-labelledby="cart-title" style="position:fixed;top:0;right:0;bottom:0;z-index:10000;width:100%;max-width:420px;transform:translateX(100%);background:var(--white);box-shadow:-8px 0 40px rgba(32,25,22,0.14);transition:transform 0.6s cubic-bezier(0.22,1,0.36,1);display:none;flex-direction:column;pointer-events:none;">
  {{-- Header --}}
  <div class="cart-slide-item" style="display:flex;align-items:center;justify-content:space-between;padding:24px 24px 20px;border-bottom:1px solid var(--border);">
    <h2 id="cart-title" style="margin:0;font-size:18px;font-weight:500;letter-spacing:0.12em;text-transform:uppercase;color:var(--brown);font-family:'Plus Jakarta Sans',system-ui,sans-serif;">Cart</h2>
    <button type="button" onclick="closeCart()" aria-label="Tutup" style="display:flex;align-items:center;justify-content:center;width:32px;height:32px;border:none;border-radius:50%;background:none;cursor:pointer;color:var(--muted);transition:all 0.15s;" onmouseover="this.style.color='var(--brown)';this.style.background='var(--cream)'" onmouseout="this.style.color='var(--muted)';this.style.background='none'">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 6l12 12M18 6 6 18"/></svg>
    </button>
  </div>

  {{-- Cart Items --}}
  <div id="cart-drawer-items" class="cart-slide-item" style="flex:1;overflow-y:auto;padding:0 24px;">
    {{-- Loading --}}
    <div id="cart-loading" style="display:flex;align-items:center;justify-content:center;padding:64px 0;">
      <p style="font-size:13px;color:var(--muted);">Memuat...</p>
    </div>
    {{-- Empty --}}
    <div id="cart-empty" style="display:none;flex-direction:column;align-items:center;justify-content:center;padding:80px 0;text-align:center;">
      <div style="margin:0 auto 20px;display:flex;align-items:center;justify-content:center;width:56px;height:56px;border-radius:50%;background:var(--cream);color:var(--brown);">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="9" cy="20" r="1.5"/><circle cx="17" cy="20" r="1.5"/><path d="M3.5 4h2.1l2.2 11.2a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 1.9-1.4L21 8H7"/></svg>
      </div>
      <p style="font-size:14px;font-weight:700;color:var(--brown);margin:0 0 4px;font-family:'Plus Jakarta Sans',system-ui,sans-serif;">Keranjang masih kosong</p>
      <p style="font-size:12px;color:var(--muted);margin:0 0 20px;">Yuk mulai belanja!</p>
      <a href="/shop" style="display:inline-flex;align-items:center;height:40px;padding:0 24px;border-radius:3px;background:var(--brown);color:#fff;font-size:11px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;text-decoration:none;">Lihat Produk</a>
    </div>
    {{-- Items --}}
    <div id="cart-list" style="display:none;"></div>
  </div>

  {{-- Order Note --}}
  <div id="cart-order-note-wrap" class="cart-slide-item" style="display:none;padding:16px 24px 8px;">
    <label style="display:block;margin-bottom:8px;font-size:11px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:var(--brown);" for="cart-order-note">Order Note</label>
    <textarea id="cart-order-note" rows="3" style="width:100%;border:1px solid var(--brown);border-radius:3px;padding:12px 16px;font-size:13px;color:var(--brown);background:#fff;resize:none;outline:none;font-family:inherit;box-sizing:border-box;" onfocus="this.style.borderColor='var(--brown)'" onblur="this.style.borderColor='var(--brown)'"></textarea>
  </div>

  {{-- Footer --}}
  <div id="cart-footer" class="cart-slide-item" style="display:none;border-top:1px solid var(--border);padding:20px 24px;background:var(--white);">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
      <span style="font-size:11px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:var(--brown);">Subtotal</span>
      <span id="cart-subtotal" style="font-size:15px;font-weight:700;color:var(--brown);font-family:'Plus Jakarta Sans',system-ui,sans-serif;">Rp 0</span>
    </div>
    <p style="font-size:11px;color:var(--muted);margin:0 0 16px;">Shipping, taxes, and discount codes calculated at checkout.</p>
    <button type="button" id="cart-drawer-checkout" onclick="checkoutFromDrawer()" style="display:flex;align-items:center;justify-content:center;height:46px;width:100%;border-radius:3px;background:var(--brown);color:#fff;font-size:11px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;text-decoration:none;transition:opacity 0.2s;border:none;cursor:pointer;" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">Check Out</button>
  </div>
</div>

<script>
  function openCart() {
    const drawer = document.getElementById('cart-drawer');
    const backdrop = document.getElementById('cart-backdrop');

    // Save focus for restore on close
    drawer._previousFocus = document.activeElement;

    drawer.style.display = 'flex';
    drawer.style.pointerEvents = 'auto';
    backdrop.style.pointerEvents = 'auto';
    document.body.style.overflow = 'hidden';

    // Reset animation state first
    document.querySelectorAll('.cart-slide-item').forEach(el => {
      el.classList.remove('is-animating');
    });

    requestAnimationFrame(() => {
      backdrop.style.background = 'rgba(0,0,0,0.4)';
      backdrop.style.backdropFilter = 'none';
      backdrop.style.webkitBackdropFilter = 'none';
      drawer.style.transform = 'translateX(0)';

      // Focus close button for accessibility
      const closeBtn = drawer.querySelector('button[aria-label="Tutup"]');
      if (closeBtn) closeBtn.focus({ preventScroll: true });

      // Staggered slide-up animation
      const items = document.querySelectorAll('.cart-slide-item');
      items.forEach((el, i) => {
        // Only animate visible items
        const style = getComputedStyle(el);
        if (style.display !== 'none') {
          el.style.animationDelay = (i * 0.06) + 's';
          el.classList.add('is-animating');
        }
      });
    });

    loadCart();
  }

  function closeCart() {
    const drawer = document.getElementById('cart-drawer');
    const backdrop = document.getElementById('cart-backdrop');

    // Cancel any pending auto-open from product page
    if (typeof _cartOpenTimer !== 'undefined') clearTimeout(_cartOpenTimer);

    backdrop.style.background = 'rgba(0,0,0,0)';
    backdrop.style.backdropFilter = 'blur(0)';
    backdrop.style.webkitBackdropFilter = 'blur(0)';
    drawer.style.transform = 'translateX(100%)';

    // Reset animations for next open
    document.querySelectorAll('.cart-slide-item').forEach(el => {
      el.classList.remove('is-animating');
    });

    setTimeout(() => {
      drawer.style.display = 'none';
      drawer.style.pointerEvents = 'none';
      backdrop.style.pointerEvents = 'none';
      document.body.style.overflow = '';

      // Restore focus to element that was focused before drawer opened
      if (drawer._previousFocus && typeof drawer._previousFocus.focus === 'function') {
        drawer._previousFocus.focus();
      }
    }, 300);
  }

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeCart();
  });

  var cartDrawerFormatRupiah = (typeof formatRupiah === 'function')
    ? formatRupiah
    : function(num) { return 'Rp ' + num.toLocaleString('id-ID'); };

  function _cartEscapeHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }
  function _cartSafeHex(s) {
    return /^#[0-9a-fA-F]{3,8}$/.test(s) ? s : '';
  }

  function loadCart() {
    const loading = document.getElementById('cart-loading');
    const empty = document.getElementById('cart-empty');
    const list = document.getElementById('cart-list');
    const footer = document.getElementById('cart-footer');
    const noteWrap = document.getElementById('cart-order-note-wrap');

    loading.style.display = 'flex';
    empty.style.display = 'none';
    list.style.display = 'none';
    footer.style.display = 'none';
    noteWrap.style.display = 'none';

    fetch('/keranjang', { headers: { 'Accept': 'application/json' } })
      .then(r => r.json())
      .then(data => {
        loading.style.display = 'none';

        if (!data.items || data.items.length === 0) {
          // Restore original empty state text (may have been overwritten by error)
          const ps = empty.querySelectorAll('p');
          if (ps[0]) ps[0].textContent = 'Keranjang masih kosong';
          if (ps[1]) ps[1].textContent = 'Yuk mulai belanja!';
          empty.style.display = 'flex';
          return;
        }

        list.style.display = 'block';
        footer.style.display = 'block';
        noteWrap.style.display = 'block';
        document.getElementById('cart-subtotal').textContent = cartDrawerFormatRupiah(data.subtotal);

        // Animate newly visible sections
        [list, noteWrap, footer].forEach((el, i) => {
          el.classList.remove('is-animating');
          void el.offsetWidth; // force reflow
          el.style.animationDelay = (0.12 + i * 0.08) + 's';
          el.classList.add('is-animating');
        });

        list.innerHTML = data.items.map(item => {
          const gambar = _cartEscapeHtml(item.gambar || '');
          const nama = _cartEscapeHtml(item.nama || '');
          const ukuran = _cartEscapeHtml(item.ukuran || '');
          const warna = _cartEscapeHtml(item.warna || '');
          const kodeWarna = _cartSafeHex(item.kode_warna || '');
          const itemId = parseInt(item.id) || 0;
          const itemHarga = parseInt(item.harga) || 0;
          const itemJumlah = parseInt(item.jumlah) || 1;
          const variantLabel = [warna, ukuran].filter(Boolean).join(' / ');

          return `
          <div style="display:flex;gap:16px;padding:20px 0;border-bottom:1px solid var(--border);" data-id="${itemId}" data-harga="${itemHarga}">
            <div style="width:80px;height:100px;flex-shrink:0;border-radius:3px;overflow:hidden;background:var(--cream);">
              <img src="${gambar}" alt="${nama}" style="width:100%;height:100%;object-fit:cover;display:block;" loading="lazy" />
            </div>
            <div style="flex:1;min-width:0;display:flex;flex-direction:column;justify-content:space-between;">
              <div>
                <p style="margin:0;font-size:13px;font-weight:500;color:var(--brown);line-height:1.4;font-family:'Plus Jakarta Sans',system-ui,sans-serif;">${nama}</p>
                ${variantLabel ? `
                <div style="margin-top:6px;display:inline-flex;align-items:center;gap:4px;border-radius:2px;background:var(--cream);padding:2px 6px;">
                  ${kodeWarna ? `<span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:${kodeWarna};"></span>` : ''}
                  <span style="font-size:10px;color:var(--muted);">${_cartEscapeHtml(variantLabel)}</span>
                </div>` : ''}
              </div>
              <div style="display:flex;align-items:center;justify-content:space-between;margin-top:8px;">
                <div style="display:inline-flex;align-items:center;border:1px solid var(--brown);border-radius:2px;">
                  <button type="button" onclick="changeItemQty(${itemId}, -1)" style="width:32px;height:32px;border:none;background:none;cursor:pointer;font-size:14px;color:var(--muted);display:flex;align-items:center;justify-content:center;">−</button>
                  <span style="width:32px;text-align:center;font-size:12px;font-weight:700;color:var(--brown);user-select:none;">${itemJumlah}</span>
                  <button type="button" onclick="changeItemQty(${itemId}, 1)" style="width:32px;height:32px;border:none;background:none;cursor:pointer;font-size:14px;color:var(--muted);display:flex;align-items:center;justify-content:center;">+</button>
                </div>
                <span style="font-size:13px;font-weight:700;color:var(--brown);font-family:'Plus Jakarta Sans',system-ui,sans-serif;">${cartDrawerFormatRupiah(itemHarga)}</span>
              </div>
            </div>
          </div>`;
        }).join('');

        if (typeof syncCartBadges === 'function') {
          syncCartBadges(data.total_item);
        } else {
          document.querySelectorAll('[data-cart-count-badge]').forEach(el => {
            el.textContent = data.total_item > 99 ? '99+' : data.total_item;
            el.classList.toggle('is-hidden', data.total_item === 0);
          });
        }
      })
      .catch(() => {
        loading.style.display = 'none';
        // Show error state with original text preserved for next load
        const origTitle = 'Keranjang masih kosong';
        const origDesc = 'Yuk mulai belanja!';
        empty.style.display = 'flex';
        const ps = empty.querySelectorAll('p');
        if (ps[0]) ps[0].textContent = 'Gagal memuat keranjang';
        if (ps[1]) ps[1].textContent = 'Coba lagi nanti.';
        // Store originals for restore on next successful load
        empty._origTitle = origTitle;
        empty._origDesc = origDesc;
      });
  }

  function changeItemQty(id, delta) {
    const itemEl = document.querySelector(`[data-id="${id}"]`);
    const qtySpan = itemEl?.querySelector('span[style*="font-weight:700"][style*="width:32px"]');
    const current = parseInt(qtySpan?.textContent) || 1;
    updateQty(id, current + delta);
  }

  function updateQty(id, newQty) {
    if (newQty < 1) {
      hapusItem(id);
      return;
    }

    // Optimistic: update DOM instantly
    const itemEl = document.querySelector(`[data-id="${id}"]`);
    if (itemEl) {
      const qtySpan = itemEl.querySelector('span[style*="font-weight:700"][style*="width:32px"]');
      if (qtySpan) qtySpan.textContent = newQty;
      itemEl.dataset.qty = newQty;
      recalcSubtotal();
    }

    // Sync server in background
    fetch('/keranjang/' + id, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
        'Accept': 'application/json',
      },
      body: JSON.stringify({ jumlah: newQty }),
    }).then(r => r.json()).then(data => {
      if (!data.success) loadCart(); // full reload on error
    }).catch(() => loadCart());
  }

  function recalcSubtotal() {
    let total = 0;
    let totalItems = 0;
    document.querySelectorAll('#cart-list [data-id]').forEach(el => {
      const harga = parseInt(el.dataset.harga) || 0;
      const qty = parseInt(el.querySelector('span[style*="font-weight:700"][style*="width:32px"]')?.textContent) || 0;
      total += harga * qty;
      totalItems += qty;
    });
    document.getElementById('cart-subtotal').textContent = cartDrawerFormatRupiah(total);
    if (typeof syncCartBadges === 'function') {
      syncCartBadges(totalItems);
    } else {
      document.querySelectorAll('[data-cart-count-badge]').forEach(el => {
        el.textContent = totalItems > 99 ? '99+' : totalItems;
        el.classList.toggle('is-hidden', totalItems === 0);
      });
    }
  }

  function hapusItem(id) {
    // Optimistic: remove from DOM instantly
    const itemEl = document.querySelector(`#cart-list [data-id="${id}"]`);
    if (itemEl) {
      itemEl.style.transition = 'opacity 0.2s, transform 0.2s';
      itemEl.style.opacity = '0';
      itemEl.style.transform = 'translateX(20px)';
      setTimeout(() => {
        itemEl.remove();
        recalcSubtotal();
        // If no items left, show empty state
        if (!document.querySelector('#cart-list [data-id]')) {
          document.getElementById('cart-list').style.display = 'none';
          document.getElementById('cart-footer').style.display = 'none';
          document.getElementById('cart-order-note-wrap').style.display = 'none';
          document.getElementById('cart-empty').style.display = 'flex';
        }
      }, 200);
    }

    // Sync server in background
    fetch('/keranjang/' + id, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
        'Accept': 'application/json',
      },
    }).then(r => r.json()).then(data => {
      if (typeof syncCartBadges === 'function') {
        syncCartBadges(data.total_item);
      }
    }).catch(() => loadCart());
  }

  function checkoutFromDrawer() {
    const itemNodes = document.querySelectorAll('#cart-list [data-id]');
    const itemIds = Array.from(itemNodes).map(el => parseInt(el.dataset.id));
    
    if (itemIds.length === 0) return;

    const btn = document.getElementById('cart-drawer-checkout');
    const oldText = btn.textContent;
    btn.textContent = 'Memproses...';
    btn.style.opacity = '0.7';
    btn.disabled = true;

    fetch('/checkout/from-cart', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
      },
      body: JSON.stringify({ item_ids: itemIds }),
    })
    .then(r => r.json())
    .then(data => {
      if (data.success && data.redirect) {
        window.location.href = data.redirect;
      } else {
        alert(data.error || 'Checkout gagal diproses');
        btn.textContent = oldText;
        btn.style.opacity = '1';
        btn.disabled = false;
      }
    })
    .catch(err => {
      alert('Terjadi kesalahan koneksi');
      btn.textContent = oldText;
      btn.style.opacity = '1';
      btn.disabled = false;
    });
  }
</script>
