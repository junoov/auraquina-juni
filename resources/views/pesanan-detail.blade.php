<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Auraquina - Pesanan {{ $pesanan->kode_pesanan }}</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;1,300&family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,500&family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>
  <body class="min-h-screen overflow-x-hidden bg-[var(--warm)] text-[var(--text)] antialiased [text-rendering:geometricPrecision]" style="font-family: Lato, system-ui, sans-serif;">

    @include('components.site-header', ['kategoris' => collect(), 'backHref' => route('account.orders')])

    <main class="mx-auto max-w-[560px] bg-white rounded-[10px] border border-[var(--border)] px-5 py-8 my-8 shadow-[0_8px_24px_rgba(131,81,61,0.04)]">
      @php
        $afterSalesTypeMap = [
          'return' => 'Penukaran / Return',
          'refund' => 'Refund',
          'issue' => 'Komplain Pesanan',
        ];
        $statusLabels = [
          'pending_payment' => 'Menunggu Pembayaran',
          'paid' => 'Pembayaran Diterima',
          'processing' => 'Diproses',
          'packed' => 'Dikemas',
          'shipped' => 'Dikirim',
          'delivered' => 'Terkirim',
          'completed' => 'Selesai',
          'cancelled' => 'Dibatalkan',
          'expired' => 'Kedaluwarsa',
          'return_requested' => 'Retur Diajukan',
          'refunded' => 'Dana Dikembalikan',
        ];
        $statusFlow = ['pending_payment', 'paid', 'processing', 'packed', 'shipped', 'delivered', 'completed'];
        $currentStep = array_search($pesanan->status, $statusFlow, true);
        $currentStep = $currentStep === false ? -1 : $currentStep;
        $trackingUrl = null;
        if ($pesanan->nomor_resi) {
          $courier = strtolower((string) ($pesanan->kurir_pengiriman ?: $pesanan->metode_pengiriman));
          $encodedAwb = urlencode($pesanan->nomor_resi);
          $trackingUrl = match (true) {
            str_contains($courier, 'jne') => 'https://www.jne.co.id/id/tracking/trace?awb=' . $encodedAwb,
            str_contains($courier, 'j&t') || str_contains($courier, 'jnt') => 'https://jet.co.id/track',
            str_contains($courier, 'sicepat') => 'https://www.sicepat.com/checkAwb',
            str_contains($courier, 'anteraja') => 'https://anteraja.id/id/tracking',
            default => 'https://cekresi.com/?noresi=' . $encodedAwb,
          };
        }
      @endphp

      {{-- Order Info --}}
      <div class="mb-6 flex">
        <button onclick="if(document.referrer && !document.referrer.includes('/invoice')){window.history.back()}else{window.location.href='{{ route('account.orders') }}'}" class="inline-flex items-center gap-2 text-[12px] font-bold uppercase tracking-[0.08em] text-[var(--brown)] hover:opacity-85 transition cursor-pointer" style="background:none; border:none; padding:0;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
          Kembali
        </button>
      </div>

      @if (session('status') || session('error') || $pesanan->status === 'pending_payment')
        <section class="mb-8">
          @if (session('status'))
            <div class="mb-5 rounded border border-green-200 bg-green-50 px-4 py-3 text-[12px] font-bold text-green-800">{{ session('status') }}</div>
          @endif
          @if (session('error'))
            <div class="mb-5 rounded border border-red-200 bg-red-50 px-4 py-3 text-[12px] font-bold text-red-800">{{ session('error') }}</div>
          @endif

          @if ($pesanan->status === 'pending_payment')
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
                <span class="font-bold text-[#B45309]">Belum Dibayar</span>
              </div>
              <div class="flex justify-between">
                <span class="text-[var(--muted)]">Tanggal Pesanan</span>
                <span class="text-[var(--ink)]">{{ $pesanan->created_at->translatedFormat('d M Y') }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-[var(--muted)]">Total Pembayaran</span>
                <span class="font-bold text-[var(--ink)]">Rp {{ number_format($pesanan->total, 0, ',', '.') }}</span>
              </div>
              @if ($pesanan->batas_bayar)
                <div class="flex justify-between">
                  <span class="text-[var(--muted)]">Sisa Waktu</span>
                  <div class="text-right">
                    <span id="countdown" class="font-bold text-[#DC2626]"></span>
                    <p class="text-[11px] text-[var(--muted)]">Batas: {{ $pesanan->batas_bayar->translatedFormat('d M Y, H:i') }}</p>
                  </div>
                </div>
              @endif
            </div>
          @endif
        </section>
      @endif

      @if ($pesanan->status === 'pending_payment')
        <hr class="border-[var(--border)]" />
      @endif

      @if ($pesanan->status !== 'pending_payment')
        <div class="mb-6 rounded-[10px] border border-[var(--border)] bg-[#FCF8F3] p-4 text-[12px] leading-relaxed text-[var(--ink)] shadow-sm">
          <div class="flex items-center gap-3">
            <span class="flex h-2.5 w-2.5 rounded-full {{ in_array($pesanan->status, ['delivered', 'completed'], true) ? 'bg-green-500' : (in_array($pesanan->status, ['cancelled', 'expired', 'refunded'], true) ? 'bg-red-500' : 'bg-amber-500') }}"></span>
            <div>
              <p class="font-bold">
                Status Pesanan: 
                @if ($pesanan->status === 'paid')
                  Sudah Dibayar
                @elseif ($pesanan->status === 'processing')
                  Sedang Diproses
                @elseif ($pesanan->status === 'packed')
                  Sedang Dikemas
                @elseif ($pesanan->status === 'shipped')
                  Sedang Dikirim (Dalam Perjalanan)
                @elseif ($pesanan->status === 'delivered')
                  Sudah Diterima
                @elseif ($pesanan->status === 'completed')
                  Selesai
                @elseif ($pesanan->status === 'cancelled')
                  Dibatalkan
                @elseif ($pesanan->status === 'expired')
                  Kedaluwarsa
                @elseif ($pesanan->status === 'refunded')
                  Dana Dikembalikan
                @else
                  {{ ucfirst($pesanan->status) }}
                @endif
              </p>
              <p class="text-[11px] text-[var(--muted)] mt-0.5">
                @if (in_array($pesanan->status, ['paid', 'processing', 'packed'], true))
                  Kami sedang memproses pesanan Anda untuk segera diserahkan ke kurir.
                @elseif ($pesanan->status === 'shipped')
                  Paket Anda sedang diantar oleh kurir. Silakan pantau pengiriman di bawah ini.
                @elseif (in_array($pesanan->status, ['delivered', 'completed'], true))
                  Pesanan telah sampai di tujuan. Terima kasih telah berbelanja di Auraquina!
                @elseif ($pesanan->status === 'cancelled')
                  Pesanan ini telah dibatalkan. Hubungi CS jika ada kendala.
                @else
                  Pesanan telah berakhir.
                @endif
              </p>
            </div>
          </div>
        </div>
      @endif

      @if (in_array($pesanan->status, ['paid', 'processing', 'packed', 'shipped'], true))
        <section class="py-6" aria-labelledby="status-timeline-title">
          <div class="mb-5 flex items-end justify-between gap-4">
            <div>
              <p class="mb-1 text-[11px] font-bold uppercase tracking-[0.12em] text-[var(--brown)]">Update Pesanan</p>
              <h2 id="status-timeline-title" class="text-[18px] font-bold text-[var(--ink)]" style="font-family:'Plus Jakarta Sans',system-ui,sans-serif;">Perjalanan Pesanan</h2>
              <p class="mt-1 text-[11px] text-[var(--muted)]">Status Pesanan</p>
            </div>
            <span class="rounded-full bg-[var(--cream)] px-3 py-1 text-[11px] font-bold text-[var(--brown)]">{{ $statusLabels[$pesanan->status] ?? ucfirst(str_replace('_', ' ', $pesanan->status)) }}</span>
          </div>
  
          <div class="space-y-4">
            @foreach ($statusFlow as $index => $status)
              @php
                $isDone = $currentStep >= $index;
                $isCurrent = $currentStep === $index;
              @endphp
              <div class="flex gap-3">
                <div class="flex flex-col items-center">
                  <span class="flex h-7 w-7 items-center justify-center rounded-full border text-[11px] font-bold {{ $isDone ? 'border-[var(--brown)] bg-[var(--brown)] text-white' : 'border-[var(--border)] bg-white text-[var(--muted)]' }}">{{ $index + 1 }}</span>
                  @if (! $loop->last)
                    <span class="h-7 w-px {{ $currentStep > $index ? 'bg-[var(--brown)]' : 'bg-[var(--border)]' }}"></span>
                  @endif
                </div>
                <div class="pb-3">
                  <p class="text-[13px] font-bold {{ $isCurrent ? 'text-[var(--brown)]' : 'text-[var(--ink)]' }}">{{ $statusLabels[$status] }}</p>
                  <p class="mt-0.5 text-[11px] leading-5 text-[var(--muted)]">
                    @if ($status === 'pending_payment') Pesanan berhasil dibuat dan menunggu pembayaran.
                    @elseif ($status === 'paid') Pembayaran sudah diterima oleh sistem.
                    @elseif ($status === 'processing') Tim Auraquina sedang menyiapkan pesanan.
                    @elseif ($status === 'packed') Pesanan sudah dikemas dan siap dikirim.
                    @elseif ($status === 'shipped') Pesanan sudah diserahkan ke kurir.
                    @elseif ($status === 'delivered') Paket sudah sampai ke alamat tujuan.
                    @else Pesanan selesai. Terima kasih sudah berbelanja.
                    @endif
                  </p>
                </div>
              </div>
            @endforeach
          </div>
        </section>
  
        <hr class="border-[var(--border)]" />
      @endif

      @php
        $midtransData = $pesanan->midtrans_raw_response ? json_decode($pesanan->midtrans_raw_response) : null;
        $paymentDetailsDisplayed = false;
        if ($pesanan->status === 'pending_payment') {
            if ($midtransData && (
                (($midtransData->payment_type ?? '') === 'gopay' && isset($midtransData->actions[0]->url)) ||
                isset($midtransData->va_numbers[0]->va_number) ||
                (isset($midtransData->biller_code) && isset($midtransData->bill_key))
            )) {
                $paymentDetailsDisplayed = true;
            }
        }
      @endphp

      {{-- Midtrans Payment (QRIS, Transfer Bank, GoPay, OVO, etc.) --}}
      @if ($pesanan->status === 'pending_payment' && !str_contains(strtolower($pesanan->metode_pembayaran), 'cod'))
        <section class="py-8 text-center">
          <p class="mb-4 text-[12px] font-bold uppercase tracking-wide text-[var(--ink)]">Pembayaran</p>
          <p class="mb-4 text-[12px] text-[var(--muted)]">{{ $pesanan->metode_pembayaran }}</p>

          @if ($pesanan->midtrans_redirect_url)
            {{-- Snap VA payment redirect --}}
            <div class="mx-auto mb-3 flex flex-col items-center justify-center p-6 rounded-xl border border-[var(--border)] bg-white max-w-[320px] shadow-sm">
              <p class="text-[12px] text-[var(--muted)] mb-2">{{ $pesanan->metode_pembayaran }}</p>
              <p class="mb-4 text-[12px] leading-relaxed text-[var(--muted)]">Lanjutkan ke halaman pembayaran Midtrans untuk melihat nomor Virtual Account.</p>
              <a href="{{ $pesanan->midtrans_redirect_url }}" class="inline-flex h-[42px] items-center justify-center rounded bg-[var(--brown)] px-5 text-[11px] font-bold uppercase tracking-[0.08em] text-white transition hover:opacity-85">Bayar via Bank</a>
            </div>
          @elseif ($midtransData && ($midtransData->payment_type ?? '') === 'gopay' && isset($midtransData->actions[0]->url))
            {{-- QR Code Display --}}
            <div class="mx-auto mb-3 flex flex-col items-center justify-center p-4 rounded-xl border border-[var(--border)] bg-white max-w-[280px] shadow-sm">
              <img src="https://gopay.co.id/icon.png" onerror="this.style.display='none'" alt="GoPay" class="h-6 mb-4 object-contain">
              <img src="{{ $midtransData->actions[0]->url }}" alt="QR Code" class="w-[200px] h-[200px] object-cover mb-4">
              <p class="text-[11px] text-[var(--muted)]">Scan QR code menggunakan aplikasi GoPay atau aplikasi QRIS lainnya</p>
            </div>
          @elseif ($midtransData && isset($midtransData->va_numbers[0]->va_number))
            {{-- Virtual Account Display --}}
            <div class="mx-auto mb-3 flex flex-col items-center justify-center p-6 rounded-xl border border-[var(--border)] bg-white w-full max-w-[420px] shadow-sm">
              <p class="text-[12px] text-[var(--muted)] mb-1">Nomor Virtual Account</p>
              <div class="flex flex-wrap items-center justify-center gap-2 mb-2 w-full px-2">
                <p class="text-[16px] sm:text-[20px] font-bold tracking-wider text-[var(--ink)] break-all text-center" id="va-number">{{ $midtransData->va_numbers[0]->va_number }}</p>
                <button onclick="navigator.clipboard.writeText('{{ $midtransData->va_numbers[0]->va_number }}'); alert('Tersalin!')" class="text-[var(--brown)] hover:opacity-80 shrink-0" aria-label="Copy">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                </button>
              </div>
              <p class="text-[12px] font-bold text-[var(--ink)] uppercase">{{ $midtransData->va_numbers[0]->bank }}</p>
            </div>
          @elseif ($midtransData && isset($midtransData->biller_code) && isset($midtransData->bill_key))
            {{-- Mandiri Bill Payment Display --}}
            <div class="mx-auto mb-3 flex flex-col items-center justify-center p-6 rounded-xl border border-[var(--border)] bg-white w-full max-w-[420px] shadow-sm">
              <p class="text-[12px] text-[var(--muted)] mb-1">Kode Perusahaan</p>
              <div class="flex flex-wrap items-center justify-center gap-2 mb-4 w-full px-2">
                <p class="text-[16px] sm:text-[20px] font-bold tracking-wider text-[var(--ink)] break-all text-center">{{ $midtransData->biller_code }}</p>
                <button onclick="navigator.clipboard.writeText('{{ $midtransData->biller_code }}'); alert('Tersalin!')" class="text-[var(--brown)] hover:opacity-80 shrink-0" aria-label="Copy">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                </button>
              </div>
              
              <p class="text-[12px] text-[var(--muted)] mb-1">Nomor Pembayaran</p>
              <div class="flex flex-wrap items-center justify-center gap-2 mb-2 w-full px-2">
                <p class="text-[16px] sm:text-[20px] font-bold tracking-wider text-[var(--ink)] break-all text-center">{{ $midtransData->bill_key }}</p>
                <button onclick="navigator.clipboard.writeText('{{ $midtransData->bill_key }}'); alert('Tersalin!')" class="text-[var(--brown)] hover:opacity-80 shrink-0" aria-label="Copy">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                </button>
              </div>
              <p class="text-[12px] font-bold text-[var(--ink)] uppercase">MANDIRI BILL PAYMENT</p>
            </div>
          @else
            <div class="mx-auto mb-3 flex h-[180px] w-[280px] flex-col items-center justify-center rounded-xl border border-[var(--border)] bg-[#FAFAFA] text-center p-4">
              <svg class="mx-auto mb-2 text-[var(--muted)]" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
              <p class="text-[13px] font-bold text-[var(--ink)] mb-1">Sedang memproses pembayaran...</p>
              <p class="text-[11px] text-[var(--muted)]">Mohon tunggu atau muat ulang halaman</p>
              <button onclick="window.location.reload()" class="mt-3 text-[11px] font-bold text-[var(--brown)] hover:underline">Muat Ulang</button>
            </div>
          @endif

          <p class="mt-4 text-[12px] text-[var(--muted)]">Pembayaran berlaku selama</p>
          <p id="qr-countdown" class="text-[14px] font-bold text-[var(--ink)]"></p>
        </section>

        <hr class="border-[var(--border)]" />

        {{-- How to pay --}}
        <section class="py-6">
          <h3 class="mb-3 text-[13px] font-bold text-[var(--ink)]">Cara Pembayaran</h3>
          
          @if ($pesanan->midtrans_redirect_url)
            <ol class="list-inside list-decimal space-y-1.5 text-[12px] leading-relaxed text-[var(--muted)]">
              <li>Klik tombol <strong>Bayar via Bank</strong> di atas.</li>
              <li>Pilih atau lanjutkan metode <strong>{{ $pesanan->metode_pembayaran }}</strong> di halaman Midtrans.</li>
              <li>Salin nomor Virtual Account yang ditampilkan.</li>
              <li>Buka aplikasi mobile banking atau ATM bank terkait, lalu selesaikan pembayaran sesuai nominal tagihan.</li>
            </ol>
          @elseif ($midtransData && ($midtransData->payment_type ?? '') === 'gopay')
            <ol class="list-inside list-decimal space-y-1.5 text-[12px] leading-relaxed text-[var(--muted)]">
              <li>Buka aplikasi <strong>GoPay</strong>, <strong>OVO</strong>, <strong>Dana</strong>, atau aplikasi mobile banking yang mendukung QRIS.</li>
              <li>Pilih menu <strong>Scan QR / Bayar</strong>.</li>
              <li>Arahkan kamera ke QR Code di atas, atau simpan gambar QR dan upload dari galeri.</li>
              <li>Periksa detail pembayaran (Pastikan nama merchant: <strong>Auraquina</strong>).</li>
              <li>Pilih <strong>Bayar</strong> dan masukkan PIN Anda.</li>
            </ol>
          @elseif ($midtransData && isset($midtransData->va_numbers[0]->va_number))
            <ol class="list-inside list-decimal space-y-1.5 text-[12px] leading-relaxed text-[var(--muted)]">
              <li>Buka aplikasi Mobile Banking atau ATM bank Anda.</li>
              <li>Pilih menu <strong>Transfer</strong> > <strong>Virtual Account</strong>.</li>
              <li>Masukkan Nomor Virtual Account: <strong>{{ $midtransData->va_numbers[0]->va_number }}</strong>.</li>
              <li>Masukkan nominal tagihan: <strong>Rp {{ number_format($pesanan->total, 0, ',', '.') }}</strong>.</li>
              <li>Periksa detail pembayaran dan pastikan nama merchant benar.</li>
              <li>Pilih <strong>Bayar/Konfirmasi</strong> dan masukkan PIN Anda.</li>
            </ol>
          @elseif ($midtransData && isset($midtransData->biller_code))
            <ol class="list-inside list-decimal space-y-1.5 text-[12px] leading-relaxed text-[var(--muted)]">
              <li>Buka aplikasi Livin' by Mandiri atau ATM Mandiri.</li>
              <li>Pilih menu <strong>Bayar</strong> > <strong>Multipayment</strong>.</li>
              <li>Masukkan Kode Perusahaan: <strong>{{ $midtransData->biller_code }}</strong> (Midtrans).</li>
              <li>Masukkan Nomor Pembayaran: <strong>{{ $midtransData->bill_key }}</strong>.</li>
              <li>Periksa detail pembayaran dan konfirmasi.</li>
            </ol>
          @endif

          <div class="mt-4 flex items-start gap-2 rounded bg-[#F5F5F5] px-3 py-2.5">
            <svg class="mt-0.5 flex-shrink-0 text-[var(--muted)]" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
            <p class="text-[11px] leading-relaxed text-[var(--muted)]">Halaman ini akan otomatis diperbarui ketika pembayaran berhasil.</p>
          </div>
        </section>

        <hr class="border-[var(--border)]" />

        {{-- Change payment method --}}
        <div class="py-4 text-center">
          <p class="text-[12px] text-[var(--muted)]">Metode: <strong>{{ $pesanan->metode_pembayaran }}</strong></p>
        </div>

        <hr class="border-[var(--border)]" />
      @endif

      {{-- COD (Bayar di Tempat) --}}
      @if ($pesanan->status === 'pending_payment' && str_contains(strtolower($pesanan->metode_pembayaran), 'cod'))
        <section class="py-8 text-center">
          <p class="mb-3 text-[12px] font-bold uppercase tracking-wide text-[var(--ink)]">COD (Bayar di Tempat)</p>
          <div class="mx-auto max-w-[280px] rounded bg-[#F5F5F5] p-4">
            <svg class="mx-auto mb-2 text-green-600" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/>
              <path d="M8 12l3 3 5-5"/>
            </svg>
            <p class="mb-1 text-[13px] font-bold text-[var(--ink)]">Siapkan uang tunai</p>
            <p class="text-[12px] text-[var(--muted)]">Bayar langsung ke kurir saat barang diterima</p>
          </div>
          <p class="mt-3 text-[12px] text-[var(--muted)]">Total yang harus dibayar: <strong>Rp {{ number_format($pesanan->total, 0, ',', '.') }}</strong></p>
        </section>

        <hr class="border-[var(--border)]" />
      @endif

      @if ($pesanan->status === 'pending_payment')
        <hr class="border-[var(--border)]" />

        {{-- Order Summary --}}
        <section class="py-6">
          <h3 class="mb-4 text-[13px] font-bold text-[var(--ink)]">Order Summary</h3>

          @foreach ($pesanan->items as $item)
            @php
              $produk = $item->produk;
              $existingReview = null;
              if ($produk && auth()->check()) {
                  $existingReview = \App\Models\Review::where('user_id', auth()->id())
                      ->where('produk_id', $item->produk_id)
                      ->first();
              }
            @endphp
            <div class="{{ !$loop->last ? 'mb-4 pb-4 border-b border-[var(--border)]' : 'mb-4' }}">
              <!-- Main Item Row -->
              <div class="flex items-start gap-3">
                <div class="h-[56px] w-[44px] flex-shrink-0 overflow-hidden rounded bg-[#F5F5F5]">
                  @if ($item->gambar_url)
                    <img src="{{ $item->full_gambar_url }}" alt="{{ $item->nama_produk }}" class="h-full w-full object-cover" />
                  @endif
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-[12px] font-bold text-[var(--ink)]">{{ $item->nama_produk }}</p>
                  <p class="text-[11px] text-[var(--muted)]">{{ $item->varian_label }} · x{{ $item->jumlah }}</p>
                </div>
                <p class="flex-shrink-0 text-[12px] font-bold text-[var(--ink)]">Rp {{ number_format($item->harga * $item->jumlah, 0, ',', '.') }}</p>
              </div>

              <!-- Review Button and Form Block -->
              @if ($produk && in_array($pesanan->status, ['delivered', 'completed'], true))
                <div class="mt-3 pl-[56px] text-left">
                  @if ($existingReview)
                    <div class="flex items-center gap-2">
                      <span class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2 py-0.5 text-[10px] font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                          <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd"/>
                        </svg>
                        Sudah Diulas ({{ $existingReview->rating }} ★)
                      </span>
                      <button type="button" onclick="toggleReviewForm({{ $item->id }})" class="text-[11px] font-bold text-[var(--brown)] hover:underline">
                        Ubah Ulasan
                      </button>
                    </div>
                  @else
                    <button type="button" onclick="toggleReviewForm({{ $item->id }})" class="inline-flex h-[28px] items-center justify-center rounded bg-[var(--brown)] px-3.5 text-[10px] font-bold uppercase tracking-[0.08em] text-white transition hover:opacity-85">
                      Beri Ulasan
                    </button>
                  @endif

                  <!-- Review Form -->
                  <div id="review-form-{{ $item->id }}" class="mt-3 hidden rounded border border-[var(--border)] bg-[#FCF8F3]/60 p-4 transition-all duration-300">
                    <form method="POST" action="{{ route('produk.reviews.store', $produk->slug) }}" enctype="multipart/form-data">
                      @csrf
                      
                      <div class="mb-3">
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-[var(--ink)] mb-1">Rating</label>
                        <select name="rating" class="block h-[34px] w-full rounded border border-[var(--border)] bg-white px-2 text-[12px] text-[var(--ink)] outline-none" required>
                          @for ($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" {{ ($existingReview && (int)$existingReview->rating === $i) ? 'selected' : '' }}>{{ $i }} Bintang</option>
                          @endfor
                        </select>
                      </div>

                      <div class="mb-3">
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-[var(--ink)] mb-1 font-bold">Ulasan</label>
                        <textarea name="review" rows="3" class="block w-full rounded border border-[var(--border)] bg-white px-3 py-2 text-[12px] text-[var(--ink)] outline-none" placeholder="Ceritakan pengalaman Anda memakai produk ini (minimal 20 karakter)." required minlength="20">{{ $existingReview ? $existingReview->review : '' }}</textarea>
                      </div>

                      <div class="mb-3">
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-[var(--ink)] mb-1 font-bold">Foto Produk (Opsional)</label>
                        <input type="file" name="photos[]" accept="image/*" multiple class="block w-full text-[11px] text-[var(--muted)]" />
                      </div>

                      <div class="flex justify-end gap-2 pt-2">
                        <button type="button" onclick="toggleReviewForm({{ $item->id }})" class="inline-flex h-[30px] items-center justify-center rounded border border-[var(--border)] bg-white px-4 text-[10px] font-bold uppercase tracking-[0.08em] text-[var(--ink)] transition hover:bg-[var(--cream)]">
                          Batal
                        </button>
                        <button type="submit" class="inline-flex h-[30px] items-center justify-center rounded bg-[var(--brown)] px-4 text-[10px] font-bold uppercase tracking-[0.08em] text-white transition hover:opacity-85">
                          Kirim Ulasan
                        </button>
                      </div>
                    </form>
                  </div>
                </div>
              @endif
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
            @if ($pesanan->catatan)
              <div class="flex justify-between gap-6">
                <span class="flex-shrink-0 text-[var(--muted)]">Catatan</span>
                <span class="text-right text-[var(--ink)] font-medium text-[var(--brown)]">{{ $pesanan->catatan }}</span>
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
      @endif

      @if ($pesanan->status === 'shipped')
        <section class="py-6" aria-labelledby="tracking-title">
          <div class="rounded-[10px] border border-[var(--border)] bg-[var(--warm)] p-5 shadow-[0_10px_30px_rgba(131,81,61,0.06)]">
            <p class="mb-1 text-[11px] font-bold uppercase tracking-[0.12em] text-[var(--brown)]">Pantau Paket</p>
            <h3 id="tracking-title" class="mb-3 text-[18px] font-bold text-[var(--ink)]" style="font-family:'Plus Jakarta Sans',system-ui,sans-serif;">Tracking Pengiriman</h3>
            @if ($pesanan->nomor_resi)
              <p class="mb-4 text-[12px] leading-6 text-[var(--muted)]">Pesanan sudah dikirim. Gunakan nomor resi berikut untuk melacak paket di website kurir.</p>
              <div class="space-y-2 rounded-[8px] bg-white p-4 text-[12px]">
                <div class="flex justify-between gap-4">
                  <span class="text-[var(--muted)]">Kurir</span>
                  <span class="font-bold text-[var(--ink)]">{{ $pesanan->kurir_pengiriman ?: $pesanan->metode_pengiriman }}</span>
                </div>
                <div class="flex justify-between gap-4">
                  <span class="text-[var(--muted)]">Nomor Resi</span>
                  <span class="font-bold text-[var(--ink)]">{{ $pesanan->nomor_resi }}</span>
                </div>
                @if ($pesanan->dikirim_pada)
                  <div class="flex justify-between gap-4">
                    <span class="text-[var(--muted)]">Dikirim Pada</span>
                    <span class="font-bold text-[var(--ink)]">{{ $pesanan->dikirim_pada->translatedFormat('d M Y, H:i') }}</span>
                  </div>
                @endif
              </div>
              <div class="mt-4 flex flex-wrap gap-2">
                <button type="button" onclick="navigator.clipboard.writeText('{{ $pesanan->nomor_resi }}')" class="inline-flex h-[38px] items-center justify-center rounded border border-[var(--border)] bg-white px-4 text-[11px] font-bold uppercase tracking-[0.08em] text-[var(--ink)] transition hover:bg-[var(--cream)]">Salin Resi</button>
                <a href="{{ $trackingUrl }}" target="_blank" rel="noopener" class="inline-flex h-[38px] items-center justify-center rounded bg-[var(--brown)] px-4 text-[11px] font-bold uppercase tracking-[0.08em] text-white transition hover:opacity-85">Lacak di Website Kurir</a>
              </div>
            @else
              <p class="text-[12px] leading-6 text-[var(--muted)]">Nomor resi akan muncul di sini setelah pesanan diserahkan ke kurir.</p>
            @endif
          </div>
        </section>
  
      @endif

      {{-- Ulasan Produk (Rating & Kata-kata) — Hanya tampil jika status Delivered / Completed --}}
      @if (in_array($pesanan->status, ['delivered', 'completed'], true))
        <section class="py-6">
          <p class="mb-1 text-[11px] font-bold uppercase tracking-[0.12em] text-[var(--brown)]">Ulasan Produk</p>
          <h2 class="mb-4 text-[18px] font-bold text-[var(--ink)]" style="font-family:'Plus Jakarta Sans',system-ui,sans-serif;">Berikan Rating & Ulasan</h2>
          
          <div class="space-y-4">
            @foreach ($pesanan->items as $item)
              @php
                $produk = $item->produk;
                $existingReview = null;
                if ($produk && auth()->check()) {
                    $existingReview = \App\Models\Review::where('user_id', auth()->id())
                        ->where('produk_id', $item->produk_id)
                        ->first();
                }
              @endphp
              @if ($produk)
                <div class="{{ !$loop->last ? 'pb-4 border-b border-[var(--border)]' : '' }}">
                  <!-- Main Item Row -->
                  <div class="flex items-start gap-3">
                    <div class="h-[56px] w-[44px] flex-shrink-0 overflow-hidden rounded bg-[#F5F5F5]">
                      @if ($item->gambar_url)
                        <img src="{{ $item->full_gambar_url }}" alt="{{ $item->nama_produk }}" class="h-full w-full object-cover" />
                      @endif
                    </div>
                    <div class="flex-1 min-w-0">
                      <p class="text-[12px] font-bold text-[var(--ink)]">{{ $item->nama_produk }}</p>
                      <p class="text-[11px] text-[var(--muted)]">{{ $item->varian_label }} · x{{ $item->jumlah }}</p>
                    </div>
                  </div>

                  <!-- Review Button and Form Block -->
                  <div class="mt-3 pl-[56px] text-left">
                    @if ($existingReview)
                      <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2 py-0.5 text-[10px] font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                          <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd"/>
                          </svg>
                          Sudah Diulas ({{ $existingReview->rating }} ★)
                        </span>
                        <button type="button" onclick="toggleReviewForm({{ $item->id }})" class="text-[11px] font-bold text-[var(--brown)] hover:underline">
                          Ubah Ulasan
                        </button>
                      </div>
                    @else
                      <button type="button" onclick="toggleReviewForm({{ $item->id }})" class="inline-flex h-[28px] items-center justify-center rounded bg-[var(--brown)] px-3.5 text-[10px] font-bold uppercase tracking-[0.08em] text-white transition hover:opacity-85">
                        Beri Ulasan
                      </button>
                    @endif

                    <!-- Review Form -->
                    <div id="review-form-{{ $item->id }}" class="mt-3 hidden rounded border border-[var(--border)] bg-[#FCF8F3]/60 p-4 transition-all duration-300">
                      <form method="POST" action="{{ route('produk.reviews.store', $produk->slug) }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                          <label class="block text-[10px] font-bold uppercase tracking-wider text-[var(--ink)] mb-1">Rating</label>
                          <input type="hidden" name="rating" id="rating-input-{{ $item->id }}" value="{{ $existingReview ? $existingReview->rating : 5 }}" />
                          @php
                            $initialRating = $existingReview ? (int)$existingReview->rating : 5;
                          @endphp
                          <div class="flex items-center gap-1.5 my-2" id="star-container-{{ $item->id }}" data-rating="{{ $initialRating }}">
                            @for ($star = 1; $star <= 5; $star++)
                              @php
                                $isActive = $star <= $initialRating;
                              @endphp
                              <button type="button" onclick="setRating({{ $item->id }}, {{ $star }})" onmouseover="hoverStars({{ $item->id }}, {{ $star }})" onmouseout="resetStars({{ $item->id }})" class="star-btn-{{ $item->id }} focus:outline-none transition-all duration-150 transform hover:scale-110 {{ $isActive ? 'text-amber-400' : 'text-gray-300' }}" data-star="{{ $star }}">
                                <svg class="h-7 w-7 fill-current" viewBox="0 0 24 24">
                                  <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                </svg>
                              </button>
                            @endfor
                          </div>
                        </div>

                        <div class="mb-3">
                          <label class="block text-[10px] font-bold uppercase tracking-wider text-[var(--ink)] mb-1 font-bold">Ulasan</label>
                          <textarea name="review" rows="3" class="block w-full rounded border border-[var(--border)] bg-white px-3 py-2 text-[12px] text-[var(--ink)] outline-none" placeholder="Ceritakan pengalaman Anda memakai produk ini (minimal 20 karakter)." required minlength="20">{{ $existingReview ? $existingReview->review : '' }}</textarea>
                        </div>

                        <div class="mb-3">
                          <label class="block text-[10px] font-bold uppercase tracking-wider text-[var(--ink)] mb-1 font-bold">Foto Produk (Opsional)</label>
                          <input type="file" name="photos[]" accept="image/*" multiple class="block w-full text-[11px] text-[var(--muted)]" />
                        </div>

                        <div class="flex justify-end gap-2 pt-2">
                          <button type="button" onclick="toggleReviewForm({{ $item->id }})" class="inline-flex h-[30px] items-center justify-center rounded border border-[var(--border)] bg-white px-4 text-[10px] font-bold uppercase tracking-[0.08em] text-[var(--ink)] transition hover:bg-[var(--cream)]">
                            Batal
                          </button>
                          <button type="submit" class="inline-flex h-[30px] items-center justify-center rounded bg-[var(--brown)] px-4 text-[10px] font-bold uppercase tracking-[0.08em] text-white transition hover:opacity-85">
                            Kirim Ulasan
                          </button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              @endif
            @endforeach
          </div>
        </section>
        <hr class="border-[var(--border)]" />
      @endif

      <section id="after-sales" class="py-6">
        @if ($pesanan->after_sales_status)
          <h3 class="mb-3 text-[13px] font-bold text-[var(--ink)]">After-Sales Care</h3>
          <div class="rounded border border-[#FEDF89] bg-[#FFFAEB] px-4 py-4 text-[12px] leading-5 text-[#B54708]">
            <p class="font-bold">Request sudah dikirim</p>
            <p class="mt-1">Jenis: {{ $afterSalesTypeMap[$pesanan->after_sales_type] ?? ucfirst((string) $pesanan->after_sales_type) }}</p>
            @if ($pesanan->after_sales_solution)
              <p class="mt-1">Solusi: {{ ucfirst(str_replace('_', ' ', $pesanan->after_sales_solution)) }}</p>
            @endif
            <p class="mt-1">Status: {{ ucfirst(str_replace('_', ' ', (string) $pesanan->after_sales_status)) }}</p>
            <p class="mt-1">Alasan: {{ $pesanan->after_sales_reason }}</p>
            @if (! empty($pesanan->after_sales_items))
              <p class="mt-1">Item: {{ implode(', ', $pesanan->after_sales_items) }}</p>
            @endif
            @if (! empty($pesanan->after_sales_evidence))
              <div class="mt-2 flex flex-wrap gap-2">
                @foreach ($pesanan->after_sales_evidence as $evidence)
                  <a href="{{ $evidence }}" target="_blank" rel="noopener" class="rounded bg-white px-2 py-1 text-[11px] font-bold text-[#B54708] underline">Bukti {{ $loop->iteration }}</a>
                @endforeach
              </div>
            @endif
          </div>
        @elseif ($pesanan->canRequestAfterSales())
          <button type="button" onclick="toggleAfterSales()" class="w-full flex items-center justify-between py-2 text-left outline-none border-none bg-transparent cursor-pointer">
            <span class="text-[13px] font-bold text-[var(--ink)]">Ajukan After-Sales Care</span>
            <svg id="after-sales-arrow" class="transition-transform duration-200 text-[var(--muted)]" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
          </button>
          
          <div id="after-sales-content" class="hidden mt-3 transition-all duration-300">
            <p class="mb-4 text-[12px] leading-relaxed text-[var(--muted)]">Jika ada kendala dengan pesanan yang sudah diterima, Anda dapat mengajukan request after-sales melalui form berikut.</p>
            <form method="POST" action="{{ URL::temporarySignedRoute('pesanan.after-sales', now()->addDays(30), ['kode' => $pesanan->kode_pesanan]) }}" class="space-y-3">
              @csrf
              <div class="space-y-3">
                <label class="block">
                  <span class="mb-1.5 block text-[11px] font-bold uppercase tracking-[0.08em] text-[var(--ink)]">Jenis Request</span>
                  <select name="type" class="block h-[42px] w-full rounded border border-[var(--border)] bg-white px-3 text-[12px] text-[var(--ink)] outline-none">
                    <option value="return">Penukaran / Return</option>
                    <option value="refund">Refund</option>
                    <option value="issue">Komplain Pesanan</option>
                  </select>
                </label>
                <label class="block">
                  <span class="mb-1.5 block text-[11px] font-bold uppercase tracking-[0.08em] text-[var(--ink)]">Solusi yang Diinginkan</span>
                  <select name="solution" class="block h-[42px] w-full rounded border border-[var(--border)] bg-white px-3 text-[12px] text-[var(--ink)] outline-none">
                    <option value="return_refund">Return & Refund</option>
                    <option value="refund_only">Refund Only</option>
                    <option value="exchange">Tukar Produk</option>
                    <option value="item_check">Cek Kendala Produk</option>
                  </select>
                </label>
                <label class="block">
                  <span class="mb-1.5 block text-[11px] font-bold uppercase tracking-[0.08em] text-[var(--ink)]">Item Terkait</span>
                  <input name="items[]" class="block h-[42px] w-full rounded border border-[var(--border)] bg-white px-3 text-[12px] text-[var(--ink)] outline-none" placeholder="Contoh: Khimar Mocha - warna tidak sesuai" />
                </label>
                <label class="block">
                  <span class="mb-1.5 block text-[11px] font-bold uppercase tracking-[0.08em] text-[var(--ink)]">Link Bukti Foto/Video</span>
                  <input name="evidence_urls[]" type="url" class="block h-[42px] w-full rounded border border-[var(--border)] bg-white px-3 text-[12px] text-[var(--ink)] outline-none" placeholder="https://..." />
                </label>
                <label class="block">
                  <span class="mb-1.5 block text-[11px] font-bold uppercase tracking-[0.08em] text-[var(--ink)]">Keterangan</span>
                  <textarea name="reason" rows="4" class="block w-full rounded border border-[var(--border)] bg-white px-3 py-2 text-[12px] text-[var(--ink)] outline-none" placeholder="Jelaskan kendala Anda, misalnya ukuran tidak sesuai atau ada masalah pada produk." required>{{ old('reason') }}</textarea>
                </label>
                <button type="submit" class="inline-flex h-[42px] items-center justify-center rounded bg-[var(--brown)] px-6 text-[11px] font-bold uppercase tracking-[0.1em] text-white transition hover:opacity-85">Kirim Request</button>
              </div>
            </form>
          </div>
        @else
          <h3 class="mb-3 text-[13px] font-bold text-[var(--ink)]">After-Sales Care</h3>
          <p class="text-[12px] leading-relaxed text-[var(--muted)]">Request after-sales tersedia setelah pesanan berstatus terkirim atau selesai.</p>
        @endif
      </section>

      {{-- Back --}}
      <div class="space-y-3 pb-10 pt-4 text-center">
        @if ($pesanan->status === 'pending_payment' && !str_contains(strtolower($pesanan->metode_pembayaran), 'cod') && !$paymentDetailsDisplayed)
          <button
            type="button"
            id="btn-pay-now"
            onclick="payWithMidtrans()"
            class="inline-flex h-[42px] items-center justify-center rounded bg-[var(--brown)] px-8 text-[11px] font-bold uppercase tracking-[0.1em] text-white transition hover:opacity-85">
            Bayar Sekarang
          </button>
        @endif
        @if ($pesanan->status !== 'pending_payment')
          <a href="{{ route('pesanan.invoice', $pesanan->kode_pesanan) }}" class="inline-flex h-[42px] items-center justify-center rounded px-8 text-[11px] font-bold uppercase tracking-[0.1em] transition {{ $pesanan->status === 'cancelled' ? 'bg-[var(--brown)] text-white hover:opacity-85' : 'border border-[var(--border)] bg-white text-[var(--ink)] hover:bg-[var(--cream)]' }}">Buka Invoice</a>
        @endif
        @if ($pesanan->status === 'pending_payment')
          <form method="POST" action="{{ URL::temporarySignedRoute('pesanan.cancel', now()->addDays(30), ['kode' => $pesanan->kode_pesanan]) }}">
            @csrf
            <button type="submit" class="inline-flex h-[42px] items-center justify-center rounded border border-[#DC2626] bg-white px-8 text-[11px] font-bold uppercase tracking-[0.1em] text-[#DC2626] transition hover:bg-[#DC2626] hover:text-white">Batalkan Pesanan</button>
          </form>
        @elseif ($pesanan->status === 'shipped' || $pesanan->status === 'delivered')
          <form method="POST" action="{{ URL::temporarySignedRoute('pesanan.confirm-received', now()->addDays(30), ['kode' => $pesanan->kode_pesanan]) }}">
            @csrf
            <button type="submit" class="inline-flex h-[42px] items-center justify-center rounded bg-[var(--brown)] px-8 text-[11px] font-bold uppercase tracking-[0.1em] text-white transition hover:opacity-85">Konfirmasi Diterima</button>
          </form>
        @endif
        <a href="/shop" class="inline-flex h-[42px] items-center justify-center rounded bg-[var(--brown)] px-8 text-[11px] font-bold uppercase tracking-[0.1em] text-white transition hover:opacity-85">Lanjut Belanja</a>
      </div>

    </main>

    @if ($pesanan->status === 'pending_payment' && !str_contains(strtolower($pesanan->metode_pembayaran), 'cod'))
      @php
        $isProduction = config('services.midtrans.is_production', false);
        $snapJsUrl = $isProduction ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js';
        $clientKey = config('services.midtrans.client_key');
      @endphp
      @if ($pesanan->midtrans_snap_token)
        <script type="text/javascript" src="{{ $snapJsUrl }}" data-client-key="{{ $clientKey }}"></script>
      @endif
    @endif

    <script>
      function payWithMidtrans() {
        @if ($pesanan->status === 'pending_payment')
          @if ($pesanan->midtrans_snap_token)
            if (typeof snap !== 'undefined') {
              snap.pay('{{ $pesanan->midtrans_snap_token }}', {
                onSuccess: function(result) {
                  window.location.reload();
                },
                onPending: function(result) {
                  window.location.reload();
                },
                onError: function(result) {
                  window.location.reload();
                },
                onClose: function() {
                  console.log('customer closed the popup without finishing the payment');
                }
              });
            } else {
              @if ($pesanan->midtrans_redirect_url)
                window.location.href = "{{ $pesanan->midtrans_redirect_url }}";
              @else
                alert('Gagal memuat pembayaran Midtrans. Coba muat ulang halaman.');
              @endif
            }
          @elseif ($pesanan->midtrans_redirect_url)
            window.location.href = "{{ $pesanan->midtrans_redirect_url }}";
          @else
            window.location.href = "{{ route('pesanan.pay', $pesanan->kode_pesanan) }}";
          @endif
        @endif
      }

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

      @if ($pesanan->status === 'pending_payment' && !str_contains(strtolower($pesanan->metode_pembayaran), 'cod'))
        let isPolling = true;
        
        async function checkPaymentStatus() {
          if (!isPolling) return;
          
          try {
            const response = await fetch(window.location.href, {
              headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
              }
            });
            
            if (response.redirected || response.status === 200) {
              // Read status from a small API or just reload if we see it changed 
              // Better: just fetch a specific status endpoint, but for now we can 
              // just reload the page and let the server decide.
              // Actually, fetching the page and checking the HTML is easiest without a new route
              const html = await response.text();
              if (!html.includes('pending_payment') || html.includes('Sudah Dibayar')) {
                isPolling = false;
                window.location.reload();
              }
            }
          } catch (e) {
            console.error('Polling error', e);
          }
          
          if (isPolling) {
            setTimeout(checkPaymentStatus, 5000);
          }
        }
        
        // Start polling
        setTimeout(checkPaymentStatus, 5000);
      @endif

      function toggleReviewForm(itemId) {
        const el = document.getElementById(`review-form-${itemId}`);
        if (el) {
          el.classList.toggle('hidden');
        }
      }

      function setRating(itemId, val) {
        const input = document.getElementById(`rating-input-${itemId}`);
        const container = document.getElementById(`star-container-${itemId}`);
        if (input && container) {
          input.value = val;
          container.setAttribute('data-rating', val);
          updateStars(itemId, val);
        }
      }

      function hoverStars(itemId, val) {
        updateStars(itemId, val);
      }

      function resetStars(itemId) {
        const container = document.getElementById(`star-container-${itemId}`);
        if (container) {
          const val = parseInt(container.getAttribute('data-rating') || '5', 10);
          updateStars(itemId, val);
        }
      }

      function updateStars(itemId, val) {
        const stars = document.querySelectorAll(`.star-btn-${itemId}`);
        stars.forEach(star => {
          const starIdx = parseInt(star.getAttribute('data-star'), 10);
          if (starIdx <= val) {
            star.classList.remove('text-gray-300');
            star.classList.add('text-amber-400');
          } else {
            star.classList.remove('text-amber-400');
            star.classList.add('text-gray-300');
          }
        });
      }

      function toggleAfterSales() {
        const content = document.getElementById('after-sales-content');
        const arrow = document.getElementById('after-sales-arrow');
        if (content) {
          content.classList.toggle('hidden');
        }
        if (arrow) {
          arrow.classList.toggle('rotate-180');
        }
      }
    </script>
  </body>
</html>
