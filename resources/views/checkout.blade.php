<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Auraquina - Checkout</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="icon" href="{{ asset('images/logo.png') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,500&family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&display=swap" rel="stylesheet" />
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
      $savedAddresses = ($addresses ?? collect())->values();
    @endphp

    @include('components.site-header', ['kategoris' => collect(), 'backHref' => '/shop'])

    <main class="checkout-main" style="max-width:1100px;margin:0 auto;padding:40px 40px 100px;">
      <div class="checkout-grid" style="display:grid;grid-template-columns:1fr 380px;gap:48px;align-items:start;">

        {{-- LEFT --}}
	        <div>
	          <h1 style="font-size:26px;font-weight:500;color:#201916;margin-bottom:28px;font-family:'Plus Jakarta Sans',system-ui,sans-serif;letter-spacing:-0.01em;">Alamat Pengiriman</h1>

          @if ($savedAddresses->isNotEmpty())
            <div style="margin-bottom:32px;">
              <div style="display:flex;justify-content:space-between;gap:12px;align-items:center;margin-bottom:14px;">
                <p style="font-size:12px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:#83513D;">Pilih Alamat Tersimpan</p>
                <a href="{{ route('account.delivery') }}" style="font-size:12px;font-weight:700;color:#83513D;text-decoration:none;">Kelola</a>
              </div>
              <div id="saved-address-cards" style="display:grid;gap:10px;">
                @foreach ($savedAddresses as $address)
                  <button type="button" class="saved-address-btn {{ $address->is_default ? 'is-default' : '' }}" data-name="{{ $address->recipient_name }}" data-email="{{ auth()->user()?->email }}" data-phone="{{ $address->phone }}" data-city="{{ $address->city }}" data-address="{{ $address->address }}" onclick="selectSavedAddress(this)" style="display:block;width:100%;text-align:left;border:1.5px solid {{ $address->is_default ? '#83513D' : 'rgba(211,192,172,0.58)' }};border-radius:10px;background:#FFFFFF;padding:14px 16px;cursor:pointer;transition:border-color 0.15s;">
                    <span style="display:flex;justify-content:space-between;gap:10px;margin-bottom:4px;">
                      <strong style="font-size:13px;color:#201916;">{{ $address->label }} · {{ $address->recipient_name }}</strong>
                      @if ($address->is_default)
                        <em style="font-style:normal;font-size:10px;font-weight:700;text-transform:uppercase;color:#83513D;">Utama</em>
                      @endif
                    </span>
                    <span style="display:block;font-size:12px;line-height:1.55;color:#71665d;">{{ $address->phone }} · {{ $address->city }}<br>{{ $address->address }}</span>
                  </button>
                @endforeach
              </div>
              <p style="margin-top:10px;font-size:11px;color:#71665d;">atau <button type="button" onclick="showManualForm()" style="font-size:11px;color:#83513D;font-weight:700;background:none;border:none;cursor:pointer;text-decoration:underline;">isi alamat manual</button></p>
            </div>
          @endif

          <div id="address-form" style="display:{{ $savedAddresses->isNotEmpty() ? 'none' : 'block' }};margin-bottom:32px;">
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

          {{-- Catatan Pesanan --}}
          <h2 style="font-size:20px;font-weight:500;color:#201916;margin-bottom:16px;font-family:'Plus Jakarta Sans',system-ui,sans-serif;letter-spacing:-0.01em;">Catatan Pesanan (Opsional)</h2>
          <div style="margin-bottom:32px;">
             <textarea id="inp-note" rows="3" placeholder="Tulis catatan untuk toko (misal: warna cadangan, patokan alamat, dll)" style="width:100%;border:1.5px solid rgba(211,192,172,0.58);border-radius:8px;padding:12px 16px;font-size:14px;color:#201916;background:#FFFFFF;outline:none;resize:none;box-sizing:border-box;"></textarea>
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
              <span style="font-size:11px;font-weight:700;color:#FFFFFF;background:#83513D;padding:4px 8px;border-radius:4px;">VA</span>
              <span id="pay-label" style="font-size:13px;color:#201916;">Transfer BCA</span>
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
                <span id="voucher-label" style="font-size:12px;color:#201916;font-weight:700;">{{ count($appliedVouchers ?? []) > 0 ? count($appliedVouchers) . ' voucher dipakai' : 'Gunakan Voucher' }}</span>
              </div>
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#71665d" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
            </div>

            @if (count($appliedVouchers ?? []) > 0)
              <div id="applied-vouchers" style="margin-bottom:16px;display:flex;flex-wrap:wrap;gap:6px;">
                @foreach ($appliedVouchers as $applied)
                  <span style="display:inline-flex;align-items:center;gap:4px;padding:4px 8px;border-radius:4px;background:#F5F0EA;font-size:10px;font-weight:700;color:#83513D;">
                    {{ $applied['code'] }} (-Rp {{ number_format($applied['discount'], 0, ',', '.') }})
                    <button type="button" onclick="event.stopPropagation(); removeVoucher('{{ $applied['code'] }}')" style="background:none;border:none;cursor:pointer;color:#83513D;font-size:12px;line-height:1;padding:0 2px;">×</button>
                  </span>
                @endforeach
              </div>
            @endif

            {{-- Totals --}}
            <div style="margin-bottom:6px;display:flex;justify-content:space-between;">
              <span style="font-size:13px;color:#71665d;">Subtotal · {{ count($cartItems) }} produk</span>
              <span style="font-size:13px;color:#201916;">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
            </div>
            <div style="margin-bottom:16px;display:flex;justify-content:space-between;">
              <span style="font-size:13px;color:#71665d;">Pengiriman</span>
              <span id="summary-shipping" style="font-size:13px;color:#201916;">Rp {{ number_format($shipping, 0, ',', '.') }}</span>
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

    {{-- TOAST --}}
    <div id="toast" style="display:none;position:fixed;top:28px;left:50%;transform:translateX(-50%);z-index:99999;padding:12px 20px;border-radius:8px;font-size:13px;font-weight:500;color:#FFFFFF;box-shadow:0 4px 20px rgba(0,0,0,0.12);pointer-events:none;opacity:0;transition:opacity 0.3s,transform 0.3s;"></div>

    {{-- POPUP / BOTTOM SHEET --}}
    <div id="sheet-overlay" onclick="closeSheet()" style="display:none;position:fixed;inset:0;background:rgba(122,80,62,0.10);z-index:200;"></div>
    <div id="sheet-panel" style="display:none;position:fixed;bottom:0;left:0;right:0;z-index:201;background:#FFFFFF;border-radius:8px 8px 0 0;max-height:80vh;overflow-y:auto;padding:24px 20px 40px;box-shadow:0 -4px 24px rgba(122,80,62,0.09);">
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
      const hasSavedAddresses = @json($savedAddresses->isNotEmpty());
      let selectedCheckoutAddress = null;

      // Shipping & payment selection tracking
      let selectedShipIndex = 0;
      let selectedPayIndex = 0;
      const shippingOptions = [
        { label: 'JNE Reguler (3-5 hari)', price: 11500, priceLabel: 'Rp 11.500' },
        { label: 'J&T Express (2-4 hari)', price: 12000, priceLabel: 'Rp 12.000' },
        { label: 'SiCepat BEST (1-3 hari)', price: 14000, priceLabel: 'Rp 14.000' },
        { label: 'GoSend Same Day', price: 25000, priceLabel: 'Rp 25.000' },
      ];
      const paymentOptions = [
        'Transfer BCA', 'Transfer Mandiri', 'Transfer BNI', 'Transfer BRI',
        'Transfer Permata', 'Transfer CIMB Niaga', 'Transfer SeaBank',
        'Transfer Danamon', 'Transfer BSI', 'Transfer Bank Saqu', 'Other Bank',
      ];
      let currentSubtotal = @json($subtotal);
      let currentDiscount = @json($discount ?? 0);

      function normalizeAddress(data) {
        return {
          name: data?.name?.trim?.() || '',
          email: data?.email?.trim?.() || '',
          phone: data?.phone?.trim?.() || '',
          city: data?.city?.trim?.() || '',
          address: data?.address?.trim?.() || '',
        };
      }

      function persistAddress(data) {
        if (isAuthenticated) return;
        localStorage.setItem(addressStorageKey, JSON.stringify(normalizeAddress(data)));
      }

      function showToast(msg, type) {
        const toast = document.getElementById('toast');
        const bg = type === 'error' ? '#83513D' : '#201916';
        toast.textContent = msg;
        toast.style.background = bg;
        toast.style.display = 'block';
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(-50%) translateY(-8px)';
        requestAnimationFrame(() => {
          toast.style.opacity = '1';
          toast.style.transform = 'translateX(-50%) translateY(0)';
        });
        setTimeout(() => {
          toast.style.opacity = '0';
          toast.style.transform = 'translateX(-50%) translateY(-8px)';
          setTimeout(() => { toast.style.display = 'none'; }, 300);
        }, 3000);
      }

      function highlightAddressButton(btn) {
        const cards = document.getElementById('saved-address-cards');
        if (!cards) return;
        cards.querySelectorAll('button').forEach(b => {
          b.style.borderColor = 'rgba(211,192,172,0.58)';
        });
        btn.style.borderColor = '#83513D';
      }

      function selectSavedAddress(btn) {
        const d = normalizeAddress(btn.dataset);
        selectedCheckoutAddress = d;
        highlightAddressButton(btn);
        document.getElementById('address-form').style.display = 'none';
      }

      function showManualForm() {
        selectedCheckoutAddress = null;
        const cards = document.getElementById('saved-address-cards');
        if (cards) {
          cards.parentElement.style.display = 'none';
        }
        document.getElementById('address-form').style.display = 'block';
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
          const shipNames = ['JNE Reguler', 'J&T Express', 'SiCepat BEST', 'GoSend Same Day'];
          const shipTimes = ['3-5 hari kerja', '2-4 hari kerja', '1-3 hari kerja', 'Hari ini (Jabodetabek)'];
          html = shippingOptions.map((opt, i) => `
            <label style="display:flex;align-items:center;gap:14px;padding:14px 0;${i < shippingOptions.length - 1 ? 'border-bottom:1px solid rgba(211,192,172,0.38);' : ''}cursor:pointer;" onclick="selectShip(${i})">
              <input type="radio" name="s" ${i === selectedShipIndex ? 'checked' : ''} style="accent-color:#83513D;width:16px;height:16px;">
              <div style="flex:1;"><p style="font-size:13px;font-weight:700;color:#201916;">${shipNames[i]}</p><p style="font-size:11px;color:#71665d;">${shipTimes[i]}</p></div>
              <span style="font-size:13px;font-weight:700;color:#201916;">${opt.priceLabel}</span>
            </label>
          `).join('');
        } else if (type === 'pay') {
          title.textContent = 'Pilih Metode Pembayaran';
          html = paymentOptions.map((opt, i) => `
            <label style="display:flex;align-items:center;gap:14px;padding:14px 0;${i < paymentOptions.length - 1 ? 'border-bottom:1px solid rgba(211,192,172,0.38);' : ''}cursor:pointer;" onclick="selectPay(${i})">
              <input type="radio" name="p" ${i === selectedPayIndex ? 'checked' : ''} style="accent-color:#83513D;width:16px;height:16px;">
              <span style="font-size:13px;font-weight:700;color:#201916;">${opt}</span>
            </label>
          `).join('');
        } else if (type === 'voucher') {
          title.textContent = 'Masukkan Kode Voucher';
          const appliedCodes = @json(array_column($appliedVouchers ?? [], 'code'));
          html = `
            <div style="display:flex;gap:10px;margin-bottom:16px;">
              <input id="voucher-code" type="text" placeholder="Kode voucher" style="flex:1;height:46px;border:1.5px solid rgba(211,192,172,0.58);border-radius:6px;padding:0 14px;font-size:14px;color:#201916;background:#FFFFFF;outline:none;text-transform:uppercase;" />
              <button type="button" onclick="applyVoucher()" style="height:46px;padding:0 16px;background:#83513D;color:#FFFFFF;border:none;font-size:12px;font-weight:700;border-radius:6px;cursor:pointer;">Tambah</button>
            </div>
            <div id="applied-voucher-chips" style="margin-bottom:16px;display:flex;flex-wrap:wrap;gap:6px;"></div>
            @if (($loyaltyVouchers ?? collect())->isNotEmpty())
              <div style="margin-bottom:16px;padding:12px;border-radius:8px;background:#F5F0EA;">
                <p style="margin:0 0 8px;font-size:11px;font-weight:700;color:#83513D;">Voucher loyalty tersedia (bisa dipakai bersamaan)</p>
                @foreach ($loyaltyVouchers as $loyaltyVoucher)
                  <button type="button" onclick="applyVoucherDirect('{{ $loyaltyVoucher->code }}')" style="display:block;width:100%;padding:8px 10px;margin-top:6px;border:1px solid rgba(131,81,61,0.25);border-radius:6px;background:#FFFFFF;text-align:left;cursor:pointer;font-size:11px;color:#201916;">
                    {{ $loyaltyVoucher->code }} · Potongan Rp {{ number_format($loyaltyVoucher->value, 0, ',', '.') }}
                  </button>
                @endforeach
              </div>
            @endif
            <button type="button" onclick="applyVoucher()" style="width:100%;height:46px;background:#83513D;color:#FFFFFF;border:none;font-size:13px;font-weight:700;border-radius:6px;cursor:pointer;">Apply</button>
            <p style="font-size:11px;color:#71665d;margin-top:12px;text-align:center;">Coba AURA10 atau FREESHIP500.</p>
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

      function selectShip(index) {
        selectedShipIndex = index;
        const opt = shippingOptions[index];
        document.getElementById('ship-label').textContent = opt.label;
        document.getElementById('ship-price').textContent = opt.priceLabel;
        // Update order summary
        document.getElementById('summary-shipping').textContent = opt.priceLabel;
        const total = currentSubtotal + opt.price - currentDiscount;
        document.getElementById('total-amount').textContent = formatRupiah(total);
        closeSheet();
      }

      function selectPay(index) {
        selectedPayIndex = index;
        document.getElementById('pay-label').textContent = paymentOptions[index];
        closeSheet();
      }

      function formatRupiah(value) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value).replace(/\s/g, ' ');
      }

      function applyVoucher() {
        const code = document.getElementById('voucher-code')?.value?.trim();
        if (!code) {
          showToast('Masukkan kode voucher terlebih dahulu.', 'error');
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
            showToast(data.error || 'Voucher tidak bisa dipakai.', 'error');
            return;
          }
          updateVoucherUI(data);
          document.getElementById('voucher-code').value = '';
          showToast(data.message, 'success');
        })
        .catch(() => {
          showToast('Voucher gagal diproses. Coba lagi.', 'error');
        });
      }

      function removeVoucher(code) {
        fetch('/checkout/voucher/remove', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '', 'Accept': 'application/json' },
          body: JSON.stringify({ code }),
        })
        .then(r => r.json().then(data => ({ ok: r.ok, data })))
        .then(({ ok, data }) => {
          if (!ok || !data.success) {
            showToast(data.error || 'Gagal menghapus voucher.', 'error');
            return;
          }
          updateVoucherUI(data);
          showToast(data.message, 'success');
        })
        .catch(() => showToast('Gagal menghapus voucher.', 'error'));
      }

      function applyVoucherDirect(code) {
        fetch('/checkout/voucher', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '', 'Accept': 'application/json' },
          body: JSON.stringify({ code }),
        })
        .then(r => r.json().then(data => ({ ok: r.ok, data })))
        .then(({ ok, data }) => {
          if (!ok || !data.success) {
            showToast(data.error || 'Voucher tidak bisa dipakai.', 'error');
            return;
          }
          updateVoucherUI(data);
          showToast(data.message, 'success');
        })
        .catch(() => showToast('Voucher gagal diproses.', 'error'));
      }

      function updateVoucherUI(data) {
        const count = data.appliedVouchers?.length || 0;
        document.getElementById('voucher-label').textContent = count > 0 ? `${count} voucher dipakai` : 'Gunakan Voucher';
        document.getElementById('discount-row').style.display = data.discount > 0 ? 'flex' : 'none';
        document.getElementById('discount-amount').textContent = `-${formatRupiah(data.discount)}`;
        document.getElementById('total-amount').textContent = formatRupiah(data.total);
        currentDiscount = data.discount;

        const container = document.getElementById('applied-vouchers') || document.querySelector('[id="applied-vouchers"]');
        if (container) {
          if (count > 0) {
            container.innerHTML = data.appliedVouchers.map(v =>
              `<span style="display:inline-flex;align-items:center;gap:4px;padding:4px 8px;border-radius:4px;background:#F5F0EA;font-size:10px;font-weight:700;color:#83513D;">${v.code} (-${formatRupiah(v.discount)})<button type="button" onclick="event.stopPropagation(); removeVoucher('${v.code}')" style="background:none;border:none;cursor:pointer;color:#83513D;font-size:12px;line-height:1;padding:0 2px;">×</button></span>`
            ).join('');
            container.style.display = 'flex';
          } else {
            container.style.display = 'none';
          }
        }
      }

      function placeOrder() {
        const btn = document.querySelector('button[onclick="placeOrder()"]');
        if (btn && btn.disabled) return;

        const formVisible = document.getElementById('address-form').style.display !== 'none';
        let addr;

        if (formVisible) {
          const name = document.getElementById('inp-name')?.value?.trim();
          const phone = document.getElementById('inp-phone')?.value?.trim();
          if (!name || !phone) {
            showToast('Mohon isi nama dan nomor telepon.', 'error');
            return;
          }
          addr = normalizeAddress({
            name, phone,
            email: document.getElementById('inp-email')?.value?.trim() || '',
            city: document.getElementById('inp-city')?.value?.trim() || '',
            address: document.getElementById('inp-address')?.value?.trim() || '',
          });
          persistAddress(addr);
        } else if (selectedCheckoutAddress) {
          addr = selectedCheckoutAddress;
        } else {
          showToast('Pilih alamat pengiriman terlebih dahulu.', 'error');
          return;
        }

        const metodePengiriman = document.getElementById('ship-label')?.textContent || 'JNE Reguler';
        const metodePembayaran = document.getElementById('pay-label')?.textContent || 'Transfer BCA';
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

        if (btn) {
          btn.textContent = 'Memproses...';
          btn.disabled = true;
          btn.style.opacity = '0.7';
        }

         fetch('/checkout/place-order', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
          body: JSON.stringify({
            nama_penerima: addr.name,
            telepon: addr.phone,
            email: addr.email,
            kota: addr.city,
            alamat_lengkap: addr.address,
            catatan: document.getElementById('inp-note')?.value?.trim() || '',
            metode_pengiriman: metodePengiriman,
            metode_pembayaran: metodePembayaran,
          }),
        })
        .then(r => r.json())
        .then(data => {
          if (data.success && data.redirect) {
            window.location.href = data.redirect;
          } else {
            showToast(data.error || 'Terjadi kesalahan. Silakan coba lagi.', 'error');
            if (btn) {
              btn.textContent = 'Pesan Sekarang';
              btn.disabled = false;
              btn.style.opacity = '1';
            }
          }
        })
        .catch(() => {
          showToast('Terjadi kesalahan jaringan. Silakan coba lagi.', 'error');
          if (btn) {
            btn.textContent = 'Pesan Sekarang';
            btn.disabled = false;
            btn.style.opacity = '1';
          }
        });
      }

      document.addEventListener('DOMContentLoaded', () => {
        const defaultBtn = document.querySelector('.saved-address-btn.is-default') || document.querySelector('.saved-address-btn');
        if (defaultBtn) {
          selectSavedAddress(defaultBtn);
        }
      });
    </script>
  </body>
</html>
