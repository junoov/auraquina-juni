<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Lacak Pesanan - Auraquina</title>
    <meta name="description" content="Lacak status pesanan Auraquina dengan nomor pesanan dan email atau nomor HP." />
    <link rel="icon" href="https://d2kchovjbwl1tk.cloudfront.net/vendors/292/assets/image/1769740142660-Untitled-1_resized128-png.webp" />
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,500&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>
  <body class="min-h-dvh bg-[var(--warm)] text-[var(--text)] antialiased [text-rendering:geometricPrecision]" style="font-family:'Plus Jakarta Sans',system-ui,sans-serif;">
    @php
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
      $currentStep = $pesanan ? array_search($pesanan->status, $statusFlow, true) : -1;
      $currentStep = $currentStep === false ? -1 : $currentStep;
      $trackingUrl = null;
      if ($pesanan?->nomor_resi) {
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

    @include('components.site-header', ['kategoris' => collect(), 'backHref' => '/'])

    <main class="mx-auto w-[min(1040px,calc(100vw-32px))] py-12 max-sm:w-[calc(100vw-24px)] max-sm:py-8">
      <section class="grid grid-cols-[0.9fr_1.1fr] gap-8 max-lg:grid-cols-1">
        <div class="rounded-[14px] border border-[var(--border)] bg-[var(--white)] p-7 shadow-[0_18px_50px_rgba(131,81,61,0.08)] max-sm:p-5">
          <p class="mb-2 text-[11px] font-bold uppercase tracking-[0.18em] text-[var(--brown)]">Pantau Pesanan</p>
          <h1 class="text-[34px] leading-[1.08] text-[var(--ink)] max-sm:text-[28px]" style="font-family:'Cormorant Garamond',Georgia,serif;">Lacak Pesanan</h1>
          <p class="mt-3 text-[14px] leading-7 text-[var(--muted)]">Masukkan nomor pesanan dan email atau nomor HP yang digunakan saat checkout.</p>

          @if ($errors->any())
            <div class="mt-5 rounded-[8px] border border-[#FECACA] bg-[#FEF2F2] px-4 py-3 text-[12px] font-bold text-[#B42318]">{{ $errors->first() }}</div>
          @endif

          <form method="POST" action="{{ route('orders.track.lookup') }}" class="mt-6 space-y-4">
            @csrf
            <label class="block">
              <span class="mb-1.5 block text-[12px] font-bold text-[var(--ink)]">Nomor Pesanan</span>
              <input type="text" name="kode_pesanan" value="{{ old('kode_pesanan') }}" placeholder="Contoh: AQ2607050001" class="block h-12 w-full rounded-[8px] border border-[var(--border)] bg-[var(--warm)] px-4 text-[14px] text-[var(--ink)] outline-none transition focus:border-[var(--brown)]" required />
            </label>
            <label class="block">
              <span class="mb-1.5 block text-[12px] font-bold text-[var(--ink)]">Email atau Nomor HP</span>
              <input type="text" name="contact" value="{{ old('contact') }}" placeholder="Email atau nomor HP penerima" class="block h-12 w-full rounded-[8px] border border-[var(--border)] bg-[var(--warm)] px-4 text-[14px] text-[var(--ink)] outline-none transition focus:border-[var(--brown)]" required />
            </label>
            <button type="submit" class="inline-flex h-12 w-full items-center justify-center rounded-[8px] bg-[var(--brown)] px-6 text-[12px] font-bold uppercase tracking-[0.12em] text-white transition hover:opacity-90">Lacak Pesanan</button>
          </form>
        </div>

        <div class="rounded-[14px] border border-[var(--border)] bg-[var(--white)] p-7 shadow-[0_18px_50px_rgba(131,81,61,0.08)] max-sm:p-5">
          @if ($pesanan)
            <div class="mb-6 flex items-start justify-between gap-4 border-b border-[var(--border)] pb-5">
              <div>
                <p class="text-[11px] uppercase tracking-[0.12em] text-[var(--muted)]">Hasil Pelacakan</p>
                <h2 class="mt-1 text-[20px] font-bold text-[var(--ink)]">#{{ $pesanan->kode_pesanan }}</h2>
              </div>
              <span class="rounded-full bg-[var(--cream)] px-3 py-1 text-[11px] font-bold text-[var(--brown)]">{{ $statusLabels[$pesanan->status] ?? ucfirst(str_replace('_', ' ', $pesanan->status)) }}</span>
            </div>

            <section aria-labelledby="public-timeline-title">
              <h3 id="public-timeline-title" class="mb-4 text-[16px] font-bold text-[var(--ink)]">Status Pesanan</h3>
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
                    </div>
                  </div>
                @endforeach
              </div>
            </section>

            <section class="mt-7 rounded-[10px] border border-[var(--border)] bg-[var(--warm)] p-5" aria-labelledby="public-tracking-title">
              <p class="mb-1 text-[11px] font-bold uppercase tracking-[0.12em] text-[var(--brown)]">Pantau Paket</p>
              <h3 id="public-tracking-title" class="mb-3 text-[16px] font-bold text-[var(--ink)]">Tracking Pengiriman</h3>
              @if ($pesanan->nomor_resi)
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
                <p class="text-[12px] leading-6 text-[var(--muted)]">Nomor resi akan muncul setelah pesanan diserahkan ke kurir.</p>
              @endif
            </section>

            <a href="{{ URL::temporarySignedRoute('pesanan.show', now()->addDays(30), ['kode' => $pesanan->kode_pesanan]) }}" class="mt-5 inline-flex h-11 items-center justify-center rounded-[8px] border border-[var(--border)] bg-white px-5 text-[12px] font-bold uppercase tracking-[0.1em] text-[var(--ink)] transition hover:bg-[var(--cream)]">Buka Detail Pesanan</a>
          @else
            <div class="flex min-h-[360px] flex-col items-center justify-center rounded-[10px] border border-dashed border-[var(--border)] bg-[var(--warm)] px-6 text-center">
              <p class="mb-2 text-[13px] font-bold text-[var(--ink)]">Belum ada pesanan yang ditampilkan</p>
              <p class="max-w-[360px] text-[12px] leading-6 text-[var(--muted)]">Hasil tracking akan muncul di sini setelah nomor pesanan dan kontak cocok dengan data pesanan.</p>
            </div>
          @endif
        </div>
      </section>
    </main>
  </body>
</html>
