<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Invoice {{ $pesanan->kode_pesanan }} - Auraquina</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,500&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css'])
  </head>
  <body class="min-h-screen bg-[#f8f4ee] px-5 py-8 text-[var(--ink)]">
    <main class="mx-auto max-w-[760px] rounded-[8px] border border-[var(--border)] bg-white p-8 shadow-[0_8px_28px_rgba(131,81,61,0.06)] max-sm:p-5">
      <div class="flex items-start justify-between gap-4 border-b border-[var(--border)] pb-6 max-sm:flex-col">
        <div>
          <p class="mb-2 text-[11px] font-bold uppercase tracking-[0.18em] text-[var(--brown)]">Invoice Digital</p>
          <h1 class="text-[34px] leading-[1.05] text-[var(--ink)] max-sm:text-[28px]" style="font-family:'Plus Jakarta Sans',system-ui,sans-serif;font-weight:500;">#{{ $pesanan->kode_pesanan }}</h1>
          <p class="mt-2 text-[14px] leading-6 text-[var(--muted)]">Dibuat pada {{ $pesanan->created_at->translatedFormat('d M Y, H:i') }}</p>
        </div>
        <div class="text-right max-sm:text-left">
          <p class="text-[13px] text-[var(--muted)]">Auraquina Official</p>
          <p class="text-[13px] text-[var(--muted)]">Malang, East Java, Indonesia</p>
          <p class="mt-2 text-[13px] font-bold text-[var(--ink)]">hello@auraquina.com</p>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-8 py-6 max-sm:grid-cols-1">
        <div>
          <h2 class="mb-2 text-[17px] text-[var(--ink)]" style="font-family:'Plus Jakarta Sans',system-ui,sans-serif;font-weight:600;">Tagihan Untuk</h2>
          <p class="text-[14px] leading-7 text-[var(--muted)]">{{ $pesanan->nama_penerima }}</p>
          @if ($pesanan->email)
            <p class="text-[14px] leading-7 text-[var(--muted)]">{{ $pesanan->email }}</p>
          @endif
          <p class="text-[14px] leading-7 text-[var(--muted)]">{{ $pesanan->telepon }}</p>
          @if ($pesanan->alamat_lengkap)
            <p class="text-[14px] leading-7 text-[var(--muted)]">{{ $pesanan->alamat_lengkap }}</p>
          @endif
          @if ($pesanan->kota)
            <p class="text-[14px] leading-7 text-[var(--muted)]">{{ $pesanan->kota }}</p>
          @endif
        </div>
        <div>
          <h2 class="mb-2 text-[17px] text-[var(--ink)]" style="font-family:'Plus Jakarta Sans',system-ui,sans-serif;font-weight:600;">Ringkasan Pesanan</h2>
          <p class="text-[14px] leading-7 text-[var(--muted)]">Status: <strong class="text-[var(--ink)]">{{ ucfirst(str_replace('_', ' ', $pesanan->status)) }}</strong></p>
          <p class="text-[14px] leading-7 text-[var(--muted)]">Pembayaran: <strong class="text-[var(--ink)]">{{ $pesanan->metode_pembayaran }}</strong></p>
          <p class="text-[14px] leading-7 text-[var(--muted)]">Pengiriman: <strong class="text-[var(--ink)]">{{ $pesanan->kurir_pengiriman ?: $pesanan->metode_pengiriman }}</strong></p>
          @if ($pesanan->nomor_resi)
            <p class="text-[14px] leading-7 text-[var(--muted)]">Nomor Resi: <strong class="text-[var(--ink)]">{{ $pesanan->nomor_resi }}</strong></p>
          @endif
        </div>
      </div>

      <div class="overflow-hidden rounded-[6px] border border-[var(--border)]">
        <table class="min-w-full border-collapse text-left text-[13px]">
          <thead class="bg-[var(--cream)] text-[var(--ink)]">
            <tr>
              <th class="px-4 py-3 font-bold">Produk</th>
              <th class="px-4 py-3 font-bold">Varian</th>
              <th class="px-4 py-3 font-bold text-right">Jumlah</th>
              <th class="px-4 py-3 font-bold text-right">Subtotal</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($pesanan->items as $item)
              <tr class="border-t border-[var(--border)]">
                <td class="px-4 py-3">{{ $item->nama_produk }}</td>
                <td class="px-4 py-3 text-[var(--muted)]">{{ $item->varian_label }}</td>
                <td class="px-4 py-3 text-right">{{ $item->jumlah }}</td>
                <td class="px-4 py-3 text-right">Rp {{ number_format($item->harga * $item->jumlah, 0, ',', '.') }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="ml-auto mt-6 max-w-[280px] space-y-2 text-[14px]">
        <div class="flex items-center justify-between"><span class="text-[var(--muted)]">Subtotal</span><span>Rp {{ number_format($pesanan->subtotal, 0, ',', '.') }}</span></div>
        <div class="flex items-center justify-between"><span class="text-[var(--muted)]">Ongkir</span><span>Rp {{ number_format($pesanan->ongkir, 0, ',', '.') }}</span></div>
        @if ($pesanan->diskon > 0)
          <div class="flex items-center justify-between"><span class="text-[var(--muted)]">Diskon</span><span>-Rp {{ number_format($pesanan->diskon, 0, ',', '.') }}</span></div>
        @endif
        <div class="flex items-center justify-between border-t border-[var(--border)] pt-2 text-[16px] font-bold"><span>Total</span><span>Rp {{ number_format($pesanan->total, 0, ',', '.') }}</span></div>
      </div>
    </main>
  </body>
</html>
