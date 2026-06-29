<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Auraquina - Checkout</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="icon" href="https://d2kchovjbwl1tk.cloudfront.net/vendors/292/assets/image/1769740142660-Untitled-1_resized128-png.webp" />
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,500&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>
  <body class="min-h-screen overflow-x-hidden bg-[var(--warm)] text-[var(--text)] antialiased [text-rendering:geometricPrecision]">
    @php
      $cartItems = collect($checkoutItems ?? [])->values()->all();
      $addressStorageKey = auth()->check()
          ? 'auraquina_address_user_' . auth()->id()
          : 'auraquina_address_guest_' . session()->getId();
      $defaultAddress = [
          'name' => auth()->user()?->recipient_name ?: auth()->user()?->name,
          'email' => auth()->user()?->email,
          'phone' => auth()->user()?->phone,
          'city' => auth()->user()?->city,
          'address' => auth()->user()?->address,
      ];
    @endphp

    @include('components.site-header', ['kategoris' => collect(), 'backHref' => '/shop'])

    <main class="checkout-main" style="max-width:1100px;margin:0 auto;padding:40px 40px 100px;">
      <div class="checkout-grid" style="display:grid;grid-template-columns:1fr 380px;gap:48px;align-items:start;">

        {{-- LEFT --}}
        <div>
          <h1 style="font-size:26px;font-weight:500;color:#201916;margin-bottom:28px;font-family:'Plus Jakarta Sans',system-ui,sans-serif;letter-spacing:-0.01em;">Alamat Pengiriman</h1>

          <div id="address-form" style="margin-bottom:32px;">
            <div style="margin-bottom:14px;">
               <input type="text" id="inp-name" value="{{ $defaultAddress['name'] }}" placeholder="Nama Lengkap Penerima" style="width:100%;height:48px;border:1.5px solid rgba(211,192,172,0.58);border-radius:8px;padding:0 16px;font-size:14px;color:#201916;background:#FFFFFF;outline:none;" />
            </div>
            <div style="margin-bottom:14px;">
               <input type="email" id="inp-email" value="{{ $defaultAddress['email'] }}" placeholder="Email (opsional, untuk invoice & update pesanan)" style="width:100%;height:48px;border:1.5px solid rgba(211,192,172,0.58);border-radius:8px;padding:0 16px;font-size:14px;color:#201916;background:#FFFFFF;outline:none;" />
            </div>
            <div style="margin-bottom:14px;">
               <input type="tel" id="inp-phone" value="{{ $defaultAddress['phone'] }}" placeholder="Nomor Telepon" style="width:100%;height:48px;border:1.5px solid rgba(211,192,172,0.58);border-radius:8px;padding:0 16px;font-size:14px;color:#201916;background:#FFFFFF;outline:none;" />
            </div>
            <div style="margin-bottom:14px;">
               <input type="text" id="inp-city" value="{{ $defaultAddress['city'] }}" placeholder="Kecamatan, Kota, Provinsi" style="width:100%;height:48px;border:1.5px solid rgba(211,192,172,0.58);border-radius:8px;padding:0 16px;font-size:14px;color:#201916;background:#FFFFFF;outline:none;" />
            </div>
            <div style="margin-bottom:14px;">
               <textarea id="inp-address" rows="3" placeholder="Alamat Lengkap (Nama jalan, No. rumah, RT/RW)" style="width:100%;border:1.5px solid rgba(211,192,172,0.58);border-radius:8px;padding:12px 16px;font-size:14px;color:#201916;background:#FFFFFF;outline:none;resize:none;">{{ $defaultAddress['address'] }}</textarea>
            </div>
          </div>

          {{-- Saved address card (hidden initially) --}}
          <div id="address-saved" style="display:none;margin-bottom:32px;padding:16px 20px;border:1.5px solid rgba(211,192,172,0.58);border-radius:10px;background:#FFFFFF;">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;">
              <div>
                <p id="saved-name" style="font-size:14px;font-weight:700;color:#201916;margin-bottom:2px;"></p>
                <p id="saved-phone" style="font-size:13px;color:#71665d;margin-bottom:4px;"></p>
                <p id="saved-addr" style="font-size:13px;color:#71665d;line-height:1.5;"></p>
              </div>
              <button type="button" onclick="editAddress()" style="font-size:12px;color:#83513D;font-weight:700;background:none;border:none;cursor:pointer;">Ubah</button>
            </div>
          </div>

          {{-- Shipment Method --}}
          <h2 style="font-size:20px;font-weight:500;color:#201916;margin-bottom:16px;font-family:'Plus Jakarta Sans',system-ui,sans-serif;letter-spacing:-0.01em;">Metode Pengiriman</h2>
          <div onclick="openSheet('ship')" style="border:1.5px solid rgba(211,192,172,0.58);border-radius:10px;padding:16px 20px;background:#FFFFFF;display:flex;align-items:center;justify-content:space-between;cursor:pointer;margin-bottom:32px;">
            <div style="display:flex;align-items:center;gap:12px;">
              <span style="font-size:11px;font-weight:700;color:#83513D;background:#F5F0EA;padding:4px 8px;border-radius:4px;">JNE</span>
              <span id="ship-label" style="font-size:13px;color:#201916;">Reguler (3-5 hari)</span>
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
              <span id="ship-price" style="font-size:13px;font-weight:700;color:#201916;">Rp 11.500</span>
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#71665d" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
            </div>
          </div>

          {{-- Payment Method --}}
          <h2 style="font-size:20px;font-weight:500;color:#201916;margin-bottom:16px;font-family:'Plus Jakarta Sans',system-ui,sans-serif;letter-spacing:-0.01em;">Metode Pembayaran</h2>
          <div onclick="openSheet('pay')" style="border:1.5px solid rgba(211,192,172,0.58);border-radius:10px;padding:16px 20px;background:#FFFFFF;display:flex;align-items:center;justify-content:space-between;cursor:pointer;margin-bottom:32px;">
            <div style="display:flex;align-items:center;gap:12px;">
              <span style="font-size:11px;font-weight:700;color:#FFFFFF;background:#83513D;padding:4px 8px;border-radius:4px;">QRIS</span>
              <span id="pay-label" style="font-size:13px;color:#201916;">QRIS</span>
            </div>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#71665d" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
          </div>
        </div>

        {{-- RIGHT: Order Summary --}}
        <div style="position:sticky;top:90px;">
          <div style="border-left:1px solid rgba(211,192,172,0.38);padding-left:32px;">
            <h3 style="font-size:11px;font-weight:700;letter-spacing:0.14em;text-transform:uppercase;color:#83513D;margin-bottom:20px;">Ringkasan Pesanan</h3>

            @foreach ($cartItems as $item)
              <div style="display:flex;gap:12px;margin-bottom:20px;padding-bottom:20px;border-bottom:1px solid rgba(211,192,172,0.38);">
                <div style="width:56px;height:72px;border-radius:6px;overflow:hidden;background:#F5F0EA;flex-shrink:0;">
                  <img src="{{ $item['img'] }}" alt="{{ $item['name'] }}" style="width:100%;height:100%;object-fit:cover;display:block;" />
                </div>
                <div style="flex:1;">
                  <p style="font-size:13px;font-weight:700;color:#201916;margin-bottom:2px;">{{ $item['name'] }}</p>
                  <p style="font-size:11px;color:#71665d;">{{ $item['variant'] }} · Qty: {{ $item['qty'] }}</p>
                </div>
                <p style="font-size:13px;font-weight:700;color:#201916;">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
              </div>
            @endforeach

            {{-- Voucher --}}
            <div onclick="openSheet('voucher')" style="border:1.5px solid rgba(211,192,172,0.58);border-radius:8px;padding:12px 16px;background:#FFFFFF;display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;cursor:pointer;">
              <div style="display:flex;align-items:center;gap:8px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#83513D" stroke-width="1.7"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                <span id="voucher-label" style="font-size:12px;color:#201916;font-weight:700;">{{ $voucher ? 'Voucher: ' . $voucher->code : 'Vouchers' }}</span>
              </div>
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#71665d" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
            </div>

            {{-- Totals --}}
            <div style="margin-bottom:6px;display:flex;justify-content:space-between;">
              <span style="font-size:13px;color:#71665d;">Subtotal · {{ count($cartItems) }} items</span>
              <span style="font-size:13px;color:#201916;">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
            </div>
            <div style="margin-bottom:16px;display:flex;justify-content:space-between;">
              <span style="font-size:13px;color:#71665d;">Shipping</span>
              <span style="font-size:13px;color:#201916;">Rp {{ number_format($shipping, 0, ',', '.') }}</span>
            </div>
            <div id="discount-row" style="margin-bottom:16px;display:{{ ($discount ?? 0) > 0 ? 'flex' : 'none' }};justify-content:space-between;">
              <span style="font-size:13px;color:#71665d;">Diskon</span>
              <span id="discount-amount" style="font-size:13px;color:#15803D;font-weight:700;">-Rp {{ number_format($discount ?? 0, 0, ',', '.') }}</span>
            </div>

            <div style="border-top:1.5px solid rgba(211,192,172,0.58);padding-top:14px;display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
              <span style="font-size:14px;font-weight:700;color:#201916;">Total Pembayaran</span>
              <span id="total-amount" style="font-size:20px;font-weight:500;color:#83513D;font-family:'Plus Jakarta Sans',system-ui,sans-serif;letter-spacing:-0.01em;">Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>

            <p style="text-align:center;font-size:11px;color:#71665d;margin-bottom:16px;">🔒 Pembayaran aman & terenkripsi</p>

            <button type="button" onclick="placeOrder()" style="width:100%;height:50px;background:#83513D;color:#FFFFFF;border:none;font-size:12px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;border-radius:6px;cursor:pointer;transition:opacity 0.2s;" onmouseover="this.style.opacity='0.88'" onmouseout="this.style.opacity='1'">Pesan Sekarang</button>

            <p style="text-align:center;margin-top:12px;font-size:10px;color:#71665d;">Dengan memesan, kamu setuju dengan <a href="{{ route('pages.show', 'terms-conditions') }}" style="color:#83513D;text-decoration:underline;font-weight:700;">Syarat & Ketentuan</a></p>
          </div>
        </div>
      </div>
    </main>

    {{-- POPUP / BOTTOM SHEET --}}
    <div id="sheet-overlay" onclick="closeSheet()" style="display:none;position:fixed;inset:0;background:rgba(122,80,62,0.10);z-index:200;"></div>
    <div id="sheet-panel" style="display:none;position:fixed;bottom:0;left:0;right:0;z-index:201;background:#FFFFFF;border-radius:8px 8px 0 0;max-height:70vh;overflow-y:auto;padding:24px 20px 32px;box-shadow:0 -4px 24px rgba(122,80,62,0.09);">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid rgba(211,192,172,0.58);">
        <h3 id="sheet-title" style="font-size:16px;font-weight:700;color:#201916;"></h3>
        <button type="button" onclick="closeSheet()" style="background:none;border:none;cursor:pointer;padding:4px;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#201916" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>
      </div>
      <div id="sheet-content"></div>
    </div>

    <style>
      @media (max-width: 1023px) {
        .checkout-grid { display: block !important; }
        .checkout-grid > div:last-child { position: static !important; margin-top: 32px; }
        .checkout-grid > div:last-child > div { border-left: none !important; padding-left: 0 !important; }
        .checkout-main { padding: 86px 16px 80px !important; }
      }
      @media (min-width: 1024px) {
        #sheet-panel { left: 50% !important; right: auto !important; bottom: auto !important; top: 50% !important; transform: translate(-50%,-50%) !important; border-radius: 8px !important; max-width: 440px; width: 100%; max-height: 80vh; }
      }
    </style>

    <script>
      const addressStorageKey = @json($addressStorageKey);
      const defaultAddress = @json($defaultAddress);
      const isAuthenticated = @json(auth()->check());

      function normalizeAddress(data) {
        return {
          name: data?.name?.trim?.() || '',
          email: data?.email?.trim?.() || '',
          phone: data?.phone?.trim?.() || '',
          city: data?.city?.trim?.() || '',
          address: data?.address?.trim?.() || '',
        };
      }

      function loadSavedAddress() {
        if (isAuthenticated) {
          return normalizeAddress(defaultAddress);
        }

        try {
          const parsed = JSON.parse(localStorage.getItem(addressStorageKey) || 'null');
          const normalized = normalizeAddress(parsed || defaultAddress);
          return normalized;
        } catch {
          return normalizeAddress(defaultAddress);
        }
      }

      function persistAddress(data) {
        if (isAuthenticated) {
          return;
        }

        localStorage.setItem(addressStorageKey, JSON.stringify(normalizeAddress(data)));
      }

      function hasCompleteAddress(data) {
        return Boolean(data.name && data.phone && (data.city || data.address));
      }

      const saved = loadSavedAddress();
      if (hasCompleteAddress(saved)) {
        showSaved(saved);
      }

      function showSaved(d) {
        document.getElementById('address-form').style.display = 'none';
        document.getElementById('address-saved').style.display = 'block';
        document.getElementById('saved-name').textContent = d.name;
        document.getElementById('saved-phone').textContent = d.phone;
        document.getElementById('saved-addr').textContent = [d.email, d.city, d.address].filter(Boolean).join('\n');
      }

      function editAddress() {
        document.getElementById('address-form').style.display = 'block';
        document.getElementById('address-saved').style.display = 'none';
        const d = loadSavedAddress();
        if (d.name) document.getElementById('inp-name').value = d.name;
        if (d.email) document.getElementById('inp-email').value = d.email;
        if (d.phone) document.getElementById('inp-phone').value = d.phone;
        if (d.city) document.getElementById('inp-city').value = d.city;
        if (d.address) document.getElementById('inp-address').value = d.address;
      }

      // Sheet / Popup
      function openSheet(type) {
        const overlay = document.getElementById('sheet-overlay');
        const panel = document.getElementById('sheet-panel');
        const title = document.getElementById('sheet-title');
        const content = document.getElementById('sheet-content');

        let html = '';
        if (type === 'ship') {
          title.textContent = 'Pilih Metode Pengiriman';
          html = `
            <label style="display:flex;align-items:center;gap:14px;padding:14px 0;border-bottom:1px solid rgba(211,192,172,0.38);cursor:pointer;" onclick="selectShip('JNE Reguler (3-5 hari)','Rp 11.500')">
              <input type="radio" name="s" checked style="accent-color:#83513D;width:16px;height:16px;">
              <div style="flex:1;"><p style="font-size:13px;font-weight:700;color:#201916;">JNE Reguler</p><p style="font-size:11px;color:#71665d;">3-5 hari kerja</p></div>
              <span style="font-size:13px;font-weight:700;color:#201916;">Rp 11.500</span>
            </label>
            <label style="display:flex;align-items:center;gap:14px;padding:14px 0;border-bottom:1px solid rgba(211,192,172,0.38);cursor:pointer;" onclick="selectShip('J&T Express (2-4 hari)','Rp 12.000')">
              <input type="radio" name="s" style="accent-color:#83513D;width:16px;height:16px;">
              <div style="flex:1;"><p style="font-size:13px;font-weight:700;color:#201916;">J&T Express</p><p style="font-size:11px;color:#71665d;">2-4 hari kerja</p></div>
              <span style="font-size:13px;font-weight:700;color:#201916;">Rp 12.000</span>
            </label>
            <label style="display:flex;align-items:center;gap:14px;padding:14px 0;border-bottom:1px solid rgba(211,192,172,0.38);cursor:pointer;" onclick="selectShip('SiCepat BEST (1-3 hari)','Rp 14.000')">
              <input type="radio" name="s" style="accent-color:#83513D;width:16px;height:16px;">
              <div style="flex:1;"><p style="font-size:13px;font-weight:700;color:#201916;">SiCepat BEST</p><p style="font-size:11px;color:#71665d;">1-3 hari kerja</p></div>
              <span style="font-size:13px;font-weight:700;color:#201916;">Rp 14.000</span>
            </label>
            <label style="display:flex;align-items:center;gap:14px;padding:14px 0;cursor:pointer;" onclick="selectShip('GoSend Same Day','Rp 25.000')">
              <input type="radio" name="s" style="accent-color:#83513D;width:16px;height:16px;">
              <div style="flex:1;"><p style="font-size:13px;font-weight:700;color:#201916;">GoSend Same Day</p><p style="font-size:11px;color:#71665d;">Hari ini (Jabodetabek)</p></div>
              <span style="font-size:13px;font-weight:700;color:#201916;">Rp 25.000</span>
            </label>
          `;
        } else if (type === 'pay') {
          title.textContent = 'Pilih Metode Pembayaran';
          html = `
            <label style="display:flex;align-items:center;gap:14px;padding:14px 0;border-bottom:1px solid rgba(211,192,172,0.38);cursor:pointer;" onclick="selectPay('QRIS')">
              <input type="radio" name="p" checked style="accent-color:#83513D;width:16px;height:16px;">
              <span style="font-size:13px;font-weight:700;color:#201916;">QRIS (GoPay, OVO, Dana, dll)</span>
            </label>
            <label style="display:flex;align-items:center;gap:14px;padding:14px 0;border-bottom:1px solid rgba(211,192,172,0.38);cursor:pointer;" onclick="selectPay('Transfer BCA')">
              <input type="radio" name="p" style="accent-color:#83513D;width:16px;height:16px;">
              <span style="font-size:13px;font-weight:700;color:#201916;">Transfer Bank BCA</span>
            </label>
            <label style="display:flex;align-items:center;gap:14px;padding:14px 0;border-bottom:1px solid rgba(211,192,172,0.38);cursor:pointer;" onclick="selectPay('Transfer Mandiri')">
              <input type="radio" name="p" style="accent-color:#83513D;width:16px;height:16px;">
              <span style="font-size:13px;font-weight:700;color:#201916;">Transfer Bank Mandiri</span>
            </label>
            <label style="display:flex;align-items:center;gap:14px;padding:14px 0;border-bottom:1px solid rgba(211,192,172,0.38);cursor:pointer;" onclick="selectPay('Transfer BRI')">
              <input type="radio" name="p" style="accent-color:#83513D;width:16px;height:16px;">
              <span style="font-size:13px;font-weight:700;color:#201916;">Transfer Bank BRI</span>
            </label>
            <label style="display:flex;align-items:center;gap:14px;padding:14px 0;border-bottom:1px solid rgba(211,192,172,0.38);cursor:pointer;" onclick="selectPay('Transfer BSI')">
              <input type="radio" name="p" style="accent-color:#83513D;width:16px;height:16px;">
              <span style="font-size:13px;font-weight:700;color:#201916;">Transfer Bank BSI</span>
            </label>
            <label style="display:flex;align-items:center;gap:14px;padding:14px 0;cursor:pointer;" onclick="selectPay('COD')">
              <input type="radio" name="p" style="accent-color:#83513D;width:16px;height:16px;">
              <span style="font-size:13px;font-weight:700;color:#201916;">COD (Bayar di Tempat)</span>
            </label>
          `;
        } else if (type === 'voucher') {
          title.textContent = 'Masukkan Kode Voucher';
          html = `
            <div style="display:flex;gap:10px;margin-bottom:16px;">
              <input id="voucher-code" type="text" value="{{ $voucher?->code }}" placeholder="Kode voucher" style="flex:1;height:46px;border:1.5px solid rgba(211,192,172,0.58);border-radius:6px;padding:0 14px;font-size:14px;color:#201916;background:#FFFFFF;outline:none;text-transform:uppercase;" />
            </div>
            <button type="button" onclick="applyVoucher()" style="width:100%;height:46px;background:#83513D;color:#FFFFFF;border:none;font-size:13px;font-weight:700;border-radius:6px;cursor:pointer;">Apply</button>
            <p id="voucher-message" style="font-size:12px;color:#71665d;margin-top:12px;text-align:center;">Coba AURA10 atau FREESHIP500.</p>
          `;
        }

        content.innerHTML = html;
        overlay.style.display = 'block';
        panel.style.display = 'block';
      }

      function closeSheet() {
        document.getElementById('sheet-overlay').style.display = 'none';
        document.getElementById('sheet-panel').style.display = 'none';
      }

      function selectShip(label, price) {
        document.getElementById('ship-label').textContent = label;
        document.getElementById('ship-price').textContent = price;
        closeSheet();
      }

      function selectPay(label) {
        document.getElementById('pay-label').textContent = label;
        closeSheet();
      }

      function formatRupiah(value) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value).replace(/\s/g, ' ');
      }

      function applyVoucher() {
        const code = document.getElementById('voucher-code')?.value?.trim();
        const message = document.getElementById('voucher-message');
        if (!code) {
          if (message) message.textContent = 'Masukkan kode voucher terlebih dahulu.';
          return;
        }

        fetch('/checkout/voucher', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '', 'Accept': 'application/json' },
          body: JSON.stringify({ code }),
        })
        .then(r => r.json().then(data => ({ ok: r.ok, data })))
        .then(({ ok, data }) => {
          if (!ok || !data.success) {
            if (message) message.textContent = data.error || 'Voucher tidak bisa dipakai.';
            return;
          }
          document.getElementById('voucher-label').textContent = `Voucher: ${data.voucher}`;
          document.getElementById('discount-row').style.display = data.discount > 0 ? 'flex' : 'none';
          document.getElementById('discount-amount').textContent = `-${formatRupiah(data.discount)}`;
          document.getElementById('total-amount').textContent = formatRupiah(data.total);
          if (message) message.textContent = data.message;
          setTimeout(closeSheet, 500);
        })
        .catch(() => {
          if (message) message.textContent = 'Voucher gagal diproses. Coba lagi.';
        });
      }

      function placeOrder() {
        const btn = document.querySelector('button[onclick="placeOrder()"]');
        if (btn && btn.disabled) return;

        const name = document.getElementById('inp-name')?.value?.trim();
        const phone = document.getElementById('inp-phone')?.value?.trim();
        if (document.getElementById('address-form').style.display !== 'none') {
          if (!name || !phone) { alert('Mohon isi nama dan nomor telepon.'); return; }
          persistAddress({
            name, phone,
            email: document.getElementById('inp-email').value.trim(),
            city: document.getElementById('inp-city').value.trim(),
            address: document.getElementById('inp-address').value.trim(),
          });
        }

        // Gather data
        const savedAddr = loadSavedAddress();
        const namaPenerima = savedAddr.name || name;
        const email = savedAddr.email || document.getElementById('inp-email')?.value?.trim() || '';
        const telepon = savedAddr.phone || phone;
        const kota = savedAddr.city || document.getElementById('inp-city')?.value?.trim() || '';
        const alamat = savedAddr.address || document.getElementById('inp-address')?.value?.trim() || '';
        const metodePengiriman = document.getElementById('ship-label')?.textContent || 'JNE Reguler';
        const metodePembayaran = document.getElementById('pay-label')?.textContent || 'QRIS';

        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

        // Disable button to prevent double-click
        if (btn) {
          btn.textContent = 'Memproses...';
          btn.disabled = true;
          btn.style.opacity = '0.7';
        }

        fetch('/checkout/place-order', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
          body: JSON.stringify({
            nama_penerima: namaPenerima,
            telepon: telepon,
            email: email,
            kota: kota,
            alamat_lengkap: alamat,
            metode_pengiriman: metodePengiriman,
            metode_pembayaran: metodePembayaran,
          }),
        })
        .then(r => r.json())
        .then(data => {
          if (data.success && data.redirect) {
            window.location.href = data.redirect;
          } else {
            alert(data.error || 'Terjadi kesalahan. Silakan coba lagi.');
            if (btn) {
              btn.textContent = 'Pesan Sekarang';
              btn.disabled = false;
              btn.style.opacity = '1';
            }
          }
        })
        .catch(() => {
          alert('Terjadi kesalahan jaringan. Silakan coba lagi.');
          if (btn) {
            btn.textContent = 'Pesan Sekarang';
            btn.disabled = false;
            btn.style.opacity = '1';
          }
        });
      }
    </script>
  </body>
</html>
