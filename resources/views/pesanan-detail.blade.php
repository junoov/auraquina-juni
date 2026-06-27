<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Auraquina - Pesanan {{ $pesanan->kode_pesanan }}</title>
    <link rel="icon" href="https://d2kchovjbwl1tk.cloudfront.net/vendors/292/assets/image/1769740142660-Untitled-1_resized128-png.webp" />
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,500&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>
  <body class="min-h-screen overflow-x-hidden bg-white text-[var(--ink)] antialiased" style="font-family:'Plus Jakarta Sans',sans-serif;">

    @include('components.site-header', ['kategoris' => collect(), 'backHref' => '/account/orders'])

    <main class="mx-auto max-w-[560px] px-5 py-8">
      @php
        $afterSalesTypeMap = [
          'return' => 'Penukaran / Return',
          'refund' => 'Refund',
          'issue' => 'Komplain Pesanan',
        ];
      @endphp

      {{-- Order Info --}}
      <section class="mb-8">
        @if (session('status'))
          <div class="mb-5 rounded border border-green-200 bg-green-50 px-4 py-3 text-[12px] font-bold text-green-800">{{ session('status') }}</div>
        @endif
        @if (session('error'))
          <div class="mb-5 rounded border border-red-200 bg-red-50 px-4 py-3 text-[12px] font-bold text-red-800">{{ session('error') }}</div>
        @endif
        <p class="mb-1 text-[11px] uppercase tracking-[0.1em] text-[var(--muted)]">Pesanan Kamu</p>
        <div class="mb-5 flex items-center gap-2">
          <span class="text-[15px] font-bold text-[var(--ink)]">ID #{{ $pesanan->kode_pesanan }}</span>
          <button onclick="navigator.clipboard.writeText('{{ $pesanan->kode_pesanan }}')" class="text-[var(--muted)] hover:text-[var(--ink)]" aria-label="Copy">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
          </button>
        </div>

        {{-- Status rows --}}
        <div class="space-y-2 text-[13px]">
          <div class="flex justify-between">
            <span class="text-[var(--muted)]">Status</span>
            @if ($pesanan->status === 'pending_payment')
              <span class="font-bold text-[#B45309]">Belum Dibayar</span>
            @elseif ($pesanan->status === 'paid')
              <span class="font-bold text-green-700">Sudah Dibayar</span>
            @else
              <span class="font-bold capitalize text-[var(--ink)]">{{ str_replace('_', ' ', $pesanan->status) }}</span>
            @endif
          </div>
          <div class="flex justify-between">
            <span class="text-[var(--muted)]">Tanggal Pesanan</span>
            <span class="text-[var(--ink)]">{{ $pesanan->created_at->translatedFormat('d M Y') }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-[var(--muted)]">Total Pembayaran</span>
            <span class="font-bold text-[var(--ink)]">Rp {{ number_format($pesanan->total, 0, ',', '.') }}</span>
          </div>
          @if ($pesanan->status === 'pending_payment' && $pesanan->batas_bayar)
            <div class="flex justify-between">
              <span class="text-[var(--muted)]">Sisa Waktu</span>
              <div class="text-right">
                <span id="countdown" class="font-bold text-[#DC2626]"></span>
                <p class="text-[11px] text-[var(--muted)]">Batas: {{ $pesanan->batas_bayar->translatedFormat('d M Y, H:i') }}</p>
              </div>
            </div>
          @endif
        </div>
      </section>

      <hr class="border-[var(--border)]" />

      {{-- QRIS Section --}}
      @if ($pesanan->status === 'pending_payment' && str_contains(strtolower($pesanan->metode_pembayaran), 'qris'))
        <section class="py-8 text-center">
          <p class="mb-4 text-[12px] font-bold uppercase tracking-wide text-[var(--ink)]">QRIS</p>

          {{-- QR placeholder --}}
          <div class="mx-auto mb-3 flex h-[180px] w-[180px] items-center justify-center border border-[var(--border)] bg-[#FAFAFA]">
            <div class="text-center">
              <svg class="mx-auto mb-1 text-[var(--muted)]" width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="0.7">
                <rect x="2" y="2" width="8" height="8" rx="1"/>
                <rect x="14" y="2" width="8" height="8" rx="1"/>
                <rect x="2" y="14" width="8" height="8" rx="1"/>
                <rect x="14" y="14" width="4" height="4"/>
                <rect x="18" y="18" width="4" height="4"/>
                <rect x="4" y="4" width="4" height="4" fill="currentColor" opacity="0.2"/>
                <rect x="16" y="4" width="4" height="4" fill="currentColor" opacity="0.2"/>
                <rect x="4" y="16" width="4" height="4" fill="currentColor" opacity="0.2"/>
              </svg>
              <p class="text-[10px] text-[var(--muted)]">Placeholder</p>
            </div>
          </div>

          <p class="text-[12px] text-[var(--muted)]">QR Code berlaku selama</p>
          <p id="qr-countdown" class="text-[14px] font-bold text-[var(--ink)]"></p>
        </section>

        <hr class="border-[var(--border)]" />

        {{-- How to pay --}}
        <section class="py-6">
          <h3 class="mb-3 text-[13px] font-bold text-[var(--ink)]">How to pay</h3>
          <ol class="list-inside list-decimal space-y-1.5 text-[12px] leading-relaxed text-[var(--muted)]">
            <li>Open your chosen app to make the payment, such as GoPay, OVO, BCA Mobile, etc.</li>
            <li>Choose pay with QR method.</li>
            <li>Scan QR Code from the order detail.</li>
            <li>Follow the instruction and confirm your payment from the app.</li>
          </ol>

          <div class="mt-4 flex items-start gap-2 rounded bg-[#F5F5F5] px-3 py-2.5">
            <svg class="mt-0.5 flex-shrink-0 text-[var(--muted)]" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
            <p class="text-[11px] leading-relaxed text-[var(--muted)]">Follow payment instructions on our website and avoid sharing personal info elsewhere.</p>
          </div>
        </section>

        <hr class="border-[var(--border)]" />

        {{-- Change payment method --}}
        <div class="py-4 text-center">
          <p class="text-[12px] text-[var(--muted)]">Metode: <strong>{{ $pesanan->metode_pembayaran }}</strong></p>
        </div>

        <hr class="border-[var(--border)]" />
      @endif

      {{-- Transfer Bank --}}
      @if ($pesanan->status === 'pending_payment' && str_contains(strtolower($pesanan->metode_pembayaran), 'transfer'))
        <section class="py-8 text-center">
          <p class="mb-3 text-[12px] font-bold uppercase tracking-wide text-[var(--ink)]">{{ $pesanan->metode_pembayaran }}</p>
          <div class="mx-auto max-w-[280px] rounded bg-[#F5F5F5] p-4">
            <p class="mb-1 text-[11px] text-[var(--muted)]">Nomor Rekening</p>
            <p class="text-[18px] font-bold tracking-[0.04em] text-[var(--ink)]">XXXX-XXXX-XXXX</p>
            <p class="mt-1 text-[11px] text-[var(--muted)]">a.n. Auraquina Official</p>
          </div>
          <p class="mt-3 text-[12px] text-[var(--muted)]">Transfer tepat <strong>Rp {{ number_format($pesanan->total, 0, ',', '.') }}</strong></p>
        </section>

        <hr class="border-[var(--border)]" />

        <section class="py-6">
          <h3 class="mb-3 text-[13px] font-bold text-[var(--ink)]">How to pay</h3>
          <ol class="list-inside list-decimal space-y-1.5 text-[12px] leading-relaxed text-[var(--muted)]">
            <li>Open your mobile banking or go to ATM.</li>
            <li>Select Transfer menu.</li>
            <li>Enter the account number above.</li>
            <li>Enter the exact amount as shown.</li>
            <li>Confirm and save the receipt.</li>
          </ol>
        </section>

        <hr class="border-[var(--border)]" />
      @endif

      {{-- Order Summary --}}
      <section class="py-6">
        <h3 class="mb-4 text-[13px] font-bold text-[var(--ink)]">Order Summary</h3>

        @foreach ($pesanan->items as $item)
          <div class="flex items-start gap-3 {{ !$loop->last ? 'mb-4 pb-4 border-b border-[var(--border)]' : 'mb-4' }}">
            <div class="h-[56px] w-[44px] flex-shrink-0 overflow-hidden rounded bg-[#F5F5F5]">
              @if ($item->gambar_url)
                <img src="{{ $item->gambar_url }}" alt="{{ $item->nama_produk }}" class="h-full w-full object-cover" />
              @endif
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-[12px] font-bold text-[var(--ink)]">{{ $item->nama_produk }}</p>
              <p class="text-[11px] text-[var(--muted)]">{{ $item->varian_label }} · x{{ $item->jumlah }}</p>
            </div>
            <p class="flex-shrink-0 text-[12px] font-bold text-[var(--ink)]">Rp {{ number_format($item->harga * $item->jumlah, 0, ',', '.') }}</p>
          </div>
        @endforeach

        {{-- Totals --}}
        <div class="space-y-1.5 border-t border-[var(--border)] pt-4 text-[12px]">
          <div class="flex justify-between">
            <span class="text-[var(--muted)]">Subtotal · {{ $pesanan->items->sum('jumlah') }} Items</span>
            <span class="text-[var(--ink)]">Rp {{ number_format($pesanan->subtotal, 0, ',', '.') }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-[var(--muted)]">Shipment Price</span>
            <span class="text-[var(--ink)]">Rp {{ number_format($pesanan->ongkir, 0, ',', '.') }}</span>
          </div>
          @if ($pesanan->diskon > 0)
            <div class="flex justify-between">
              <span class="text-[var(--muted)]">Diskon</span>
              <span class="text-green-600">-Rp {{ number_format($pesanan->diskon, 0, ',', '.') }}</span>
            </div>
          @endif
          <div class="flex justify-between border-t border-[var(--border)] pt-2 text-[13px] font-bold">
            <span class="text-[var(--ink)]">Total</span>
            <span class="text-[var(--ink)]">Rp {{ number_format($pesanan->total, 0, ',', '.') }}</span>
          </div>
        </div>
      </section>

      <hr class="border-[var(--border)]" />

      {{-- Shipping --}}
      <section class="py-6">
        <h3 class="mb-3 text-[13px] font-bold text-[var(--ink)]">Pengiriman</h3>
        <div class="space-y-1.5 text-[12px]">
          <div class="flex justify-between">
            <span class="text-[var(--muted)]">Penerima</span>
            <span class="font-medium text-[var(--ink)]">{{ $pesanan->nama_penerima }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-[var(--muted)]">Telepon</span>
            <span class="text-[var(--ink)]">{{ $pesanan->telepon }}</span>
          </div>
          @if ($pesanan->kota)
            <div class="flex justify-between">
              <span class="text-[var(--muted)]">Kota</span>
              <span class="text-[var(--ink)]">{{ $pesanan->kota }}</span>
            </div>
          @endif
          @if ($pesanan->alamat_lengkap)
            <div class="flex justify-between gap-6">
              <span class="flex-shrink-0 text-[var(--muted)]">Alamat</span>
              <span class="text-right text-[var(--ink)]">{{ $pesanan->alamat_lengkap }}</span>
            </div>
          @endif
          <div class="flex justify-between">
            <span class="text-[var(--muted)]">Kurir</span>
            <span class="text-[var(--ink)]">{{ $pesanan->kurir_pengiriman ?: $pesanan->metode_pengiriman }}</span>
          </div>
          @if ($pesanan->nomor_resi)
            <div class="flex justify-between">
              <span class="text-[var(--muted)]">Nomor Resi</span>
              <span class="text-[var(--ink)]">{{ $pesanan->nomor_resi }}</span>
            </div>
          @endif
          @if ($pesanan->dikirim_pada)
            <div class="flex justify-between">
              <span class="text-[var(--muted)]">Dikirim Pada</span>
              <span class="text-[var(--ink)]">{{ $pesanan->dikirim_pada->translatedFormat('d M Y, H:i') }}</span>
            </div>
          @endif
        </div>
      </section>

      <hr class="border-[var(--border)]" />

      <section id="after-sales" class="py-6">
        <h3 class="mb-3 text-[13px] font-bold text-[var(--ink)]">After-Sales</h3>

        @if ($pesanan->after_sales_status)
          <div class="rounded border border-[#FEDF89] bg-[#FFFAEB] px-4 py-4 text-[12px] leading-5 text-[#B54708]">
            <p class="font-bold">Request sudah dikirim</p>
            <p class="mt-1">Jenis: {{ $afterSalesTypeMap[$pesanan->after_sales_type] ?? ucfirst((string) $pesanan->after_sales_type) }}</p>
            <p class="mt-1">Status: {{ ucfirst(str_replace('_', ' ', (string) $pesanan->after_sales_status)) }}</p>
            <p class="mt-1">Alasan: {{ $pesanan->after_sales_reason }}</p>
          </div>
        @elseif ($pesanan->canRequestAfterSales())
          <p class="mb-4 text-[12px] leading-relaxed text-[var(--muted)]">Jika ada kendala dengan pesanan yang sudah diterima, Anda dapat mengajukan request after-sales melalui form berikut.</p>
          <form method="POST" action="{{ URL::temporarySignedRoute('pesanan.after-sales', now()->addDays(30), ['kode' => $pesanan->kode_pesanan]) }}" class="space-y-3">
            @csrf
            <label class="block">
              <span class="mb-1.5 block text-[11px] font-bold uppercase tracking-[0.08em] text-[var(--ink)]">Jenis Request</span>
              <select name="type" class="block h-[42px] w-full rounded border border-[var(--border)] bg-white px-3 text-[12px] text-[var(--ink)] outline-none">
                <option value="return">Penukaran / Return</option>
                <option value="refund">Refund</option>
                <option value="issue">Komplain Pesanan</option>
              </select>
            </label>
            <label class="block">
              <span class="mb-1.5 block text-[11px] font-bold uppercase tracking-[0.08em] text-[var(--ink)]">Keterangan</span>
              <textarea name="reason" rows="4" class="block w-full rounded border border-[var(--border)] bg-white px-3 py-2 text-[12px] text-[var(--ink)] outline-none" placeholder="Jelaskan kendala Anda, misalnya ukuran tidak sesuai atau ada masalah pada produk." required>{{ old('reason') }}</textarea>
            </label>
            <button type="submit" class="inline-flex h-[42px] items-center justify-center rounded bg-[var(--brown)] px-6 text-[11px] font-bold uppercase tracking-[0.1em] text-white transition hover:opacity-85">Kirim Request</button>
          </form>
        @else
          <p class="text-[12px] leading-relaxed text-[var(--muted)]">Request after-sales tersedia setelah pesanan berstatus terkirim atau selesai.</p>
        @endif
      </section>

      {{-- Back --}}
      <div class="space-y-3 pb-10 pt-4 text-center">
        <a href="{{ route('pesanan.invoice', $pesanan->kode_pesanan) }}" class="inline-flex h-[42px] items-center justify-center rounded border border-[var(--border)] bg-white px-8 text-[11px] font-bold uppercase tracking-[0.1em] text-[var(--ink)] transition hover:bg-[var(--cream)]">Buka Invoice</a>
        @if ($pesanan->status === 'pending_payment')
          <form method="POST" action="{{ URL::temporarySignedRoute('pesanan.cancel', now()->addDays(30), ['kode' => $pesanan->kode_pesanan]) }}">
            @csrf
            <button type="submit" class="inline-flex h-[42px] items-center justify-center rounded border border-[#DC2626] bg-white px-8 text-[11px] font-bold uppercase tracking-[0.1em] text-[#DC2626] transition hover:bg-[#DC2626] hover:text-white">Batalkan Pesanan</button>
          </form>
        @elseif ($pesanan->status === 'delivered')
          <form method="POST" action="{{ URL::temporarySignedRoute('pesanan.confirm-received', now()->addDays(30), ['kode' => $pesanan->kode_pesanan]) }}">
            @csrf
            <button type="submit" class="inline-flex h-[42px] items-center justify-center rounded bg-[var(--brown)] px-8 text-[11px] font-bold uppercase tracking-[0.1em] text-white transition hover:opacity-85">Konfirmasi Diterima</button>
          </form>
        @endif
        <a href="/shop" class="inline-flex h-[42px] items-center justify-center rounded bg-[var(--ink)] px-8 text-[11px] font-bold uppercase tracking-[0.1em] text-white transition hover:opacity-85">Lanjut Belanja</a>
      </div>

    </main>

    <script>
      @if ($pesanan->status === 'pending_payment' && $pesanan->batas_bayar)
        (function() {
          const deadline = new Date('{{ $pesanan->batas_bayar->toISOString() }}').getTime();
          function pad(n) { return String(n).padStart(2, '0'); }
          function tick() {
            const diff = deadline - Date.now();
            if (diff <= 0) {
              document.getElementById('countdown').textContent = 'Waktu habis';
              const qr = document.getElementById('qr-countdown');
              if (qr) qr.textContent = 'Waktu habis';
              return;
            }
            const h = Math.floor(diff / 3600000);
            const m = Math.floor((diff % 3600000) / 60000);
            const s = Math.floor((diff % 60000) / 1000);
            const text = `${h}h ${pad(m)}m ${pad(s)}s`;
            document.getElementById('countdown').textContent = text;
            const qr = document.getElementById('qr-countdown');
            if (qr) qr.textContent = text;
            setTimeout(tick, 1000);
          }
          tick();
        })();
      @endif
    </script>
  </body>
</html>
