<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Account - Auraquina</title>
    <meta name="description" content="Kelola akun, alamat, pesanan, dan after-sales Auraquina." />
    <link rel="icon" href="{{ asset('images/logo.png') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,500&family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>
  <body class="min-h-dvh bg-[var(--warm)] text-[var(--text)] antialiased [text-rendering:geometricPrecision]">
    @php
      $containerClass = 'mx-auto w-[min(1184px,calc(100vw-32px))] max-lg:w-[calc(100vw-28px)]';
      $payments = ['QRIS', 'mandiri', 'BRI', 'BNI', 'PermataBank', 'Danamon', 'BSI'];
      $shipments = ['gosend', 'DHL', 'Lion Parcel', 'J&T'];
      $section = $section ?? 'profile';
      $settingLinks = [
        ['label' => 'Info Profil Saya', 'href' => route('account.show'), 'key' => 'profile'],
        ['label' => 'Info Pengiriman', 'href' => route('account.delivery'), 'key' => 'delivery'],
        ['label' => 'Pesanan Saya', 'href' => route('account.orders'), 'key' => 'orders'],
        ['label' => 'Informasi Akun', 'href' => route('account.information'), 'key' => 'information'],
      ];
      $orderStatusMap = [
        'pending_payment' => ['label' => 'Menunggu Pembayaran', 'class' => 'bg-[#FFF4E5] text-[#B45309]'],
        'paid' => ['label' => 'Sudah Dibayar', 'class' => 'bg-[#ECFDF3] text-[#027A48]'],
        'processing' => ['label' => 'Diproses', 'class' => 'bg-[#EEF4FF] text-[#175CD3]'],
        'packed' => ['label' => 'Siap Kirim', 'class' => 'bg-[#EEF4FF] text-[#175CD3]'],
        'shipped' => ['label' => 'Dikirim', 'class' => 'bg-[#EEF4FF] text-[#175CD3]'],
        'delivered' => ['label' => 'Terkirim', 'class' => 'bg-[#ECFDF3] text-[#027A48]'],
        'completed' => ['label' => 'Selesai', 'class' => 'bg-[#ECFDF3] text-[#027A48]'],
        'cancelled' => ['label' => 'Dibatalkan', 'class' => 'bg-[#FEF3F2] text-[#B42318]'],
        'expired' => ['label' => 'Kedaluwarsa', 'class' => 'bg-[#FEF3F2] text-[#B42318]'],
      ];
      $afterSalesTypeMap = [
        'return' => 'Penukaran / Return',
        'refund' => 'Refund',
        'issue' => 'Komplain Pesanan',
      ];
    @endphp

    @include('components.site-header', ['kategoris' => $kategoris ?? collect(), 'backHref' => '/'])

    <main class="min-h-[calc(100dvh-438px)] bg-[#f8f8f8] py-12">
      <section class="mx-auto w-[min(1000px,calc(100vw-32px))]">
        @if (session('status'))
          <div class="mb-5 rounded-[4px] border border-[#d4edda] bg-[#d4edda] px-4 py-3 text-[14px] text-[#155724]">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
          <div class="mb-5 rounded-[4px] border border-[#f5c6cb] bg-[#f8d7da] px-4 py-3 text-[14px] text-[#721c24]">{{ $errors->first() }}</div>
        @endif
      </section>

      <section class="mx-auto flex w-[min(1000px,calc(100vw-32px))] gap-8 max-md:flex-col" aria-labelledby="account-title">
        <aside class="w-[300px] shrink-0 max-md:w-full" aria-label="My Settings">
          <div class="rounded-[8px] bg-white p-6 shadow-sm">
            <h1 id="account-title" class="mb-4 text-[18px] font-medium text-[#333]" style="font-family:sans-serif;">Pengaturan Saya</h1>
            <nav class="flex flex-col gap-3">
              @foreach ($settingLinks as $link)
                <a href="{{ $link['href'] }}" class="text-[14px] text-[#555] transition-colors hover:text-[var(--brown)] {{ $section === $link['key'] ? 'font-medium !text-[var(--brown)]' : '' }}">
                  {{ $link['label'] }}
                </a>
              @endforeach
              <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <button type="submit" class="text-left text-[14px] text-[#555] transition-colors hover:text-[var(--brown)]">Keluar</button>
              </form>
            </nav>
          </div>
        </aside>

        <section class="flex-1" aria-label="Account detail">
          @if ($section === 'delivery')
            <div class="rounded-[8px] bg-white p-8 shadow-sm">
              <h2 class="mb-6 text-[18px] font-medium text-[#333]" style="font-family:sans-serif;">Informasi pengiriman</h2>
              <div class="mb-8 rounded-[8px] border border-[#eee] bg-[#fbfaf8] p-5">
                <div class="mb-4 flex items-center justify-between gap-4">
                  <h3 class="text-[15px] font-medium text-[#333]">Alamat Tersimpan</h3>
                  <span class="text-[12px] text-[#888]">{{ ($addresses ?? collect())->count() }} alamat</span>
                </div>
                @if (($addresses ?? collect())->isEmpty())
                  <p class="text-[13px] leading-6 text-[#777]">Belum ada alamat tersimpan. Tambahkan alamat rumah, kantor, atau tujuan pengiriman favorit.</p>
                @else
                  <div class="space-y-3">
                    @foreach ($addresses as $address)
                      <div class="rounded-[6px] border border-[#e8ded4] bg-white p-4">
                        <div class="mb-2 flex items-center justify-between gap-3">
                          <strong class="text-[14px] text-[#333]">{{ $address->label }} · {{ $address->recipient_name }}</strong>
                          @if ($address->is_default)
                            <span class="rounded-full bg-[var(--cream)] px-2.5 py-1 text-[11px] font-bold text-[var(--brown)]">Alamat utama</span>
                          @endif
                        </div>
                        <p class="text-[13px] leading-6 text-[#666]">{{ $address->phone }} · {{ $address->city }}<br>{{ $address->address }}</p>
                      </div>
                    @endforeach
                  </div>
                @endif
              </div>
              <form method="POST" action="{{ route('account.addresses.store') }}" class="mb-8 grid gap-3 rounded-[8px] border border-[#eee] p-5">
                @csrf
                <h3 class="text-[15px] font-medium text-[#333]">Tambah Alamat Baru</h3>
                <input type="text" name="label" placeholder="Label alamat, contoh: Rumah" class="block h-10 w-full rounded-[4px] border border-[#ddd] bg-white px-3 text-[14px] text-[#333] outline-none focus:border-[#aaa]" required />
                <input type="text" name="recipient_name" placeholder="Nama penerima" class="block h-10 w-full rounded-[4px] border border-[#ddd] bg-white px-3 text-[14px] text-[#333] outline-none focus:border-[#aaa]" required />
                <input type="tel" name="phone" placeholder="Nomor telepon" class="block h-10 w-full rounded-[4px] border border-[#ddd] bg-white px-3 text-[14px] text-[#333] outline-none focus:border-[#aaa]" required />
                <input type="text" name="city" placeholder="Kota / Wilayah" class="block h-10 w-full rounded-[4px] border border-[#ddd] bg-white px-3 text-[14px] text-[#333] outline-none focus:border-[#aaa]" required />
                <textarea name="address" rows="3" placeholder="Alamat lengkap" class="block w-full rounded-[4px] border border-[#ddd] bg-white px-3 py-2 text-[14px] text-[#333] outline-none focus:border-[#aaa]" required></textarea>
                <label class="flex items-center gap-2 text-[13px] text-[#666]"><input type="checkbox" name="is_default" value="1" class="accent-[var(--brown)]" /> Jadikan alamat utama</label>
                <button type="submit" class="inline-flex h-10 items-center justify-center rounded-[4px] bg-[var(--brown)] px-6 text-[13px] font-medium text-white transition-colors hover:bg-[var(--ink)]">Simpan Alamat</button>
              </form>
              <form method="POST" action="{{ route('account.delivery.update') }}" class="space-y-4">
                @csrf
                @method('PATCH')
                <div>
                  <label class="mb-1.5 block text-[13px] text-[#888]">Nama Penerima</label>
                  <input type="text" name="recipient_name" value="{{ old('recipient_name', $user->recipient_name ?? $user->name) }}" class="block h-10 w-full rounded-[4px] border border-[#ddd] bg-white px-3 text-[14px] text-[#333] outline-none focus:border-[#aaa]" required />
                </div>
                <div>
                  <label class="mb-1.5 block text-[13px] text-[#888]">Kota / Wilayah</label>
                  <input type="text" name="city" value="{{ old('city', $user->city) }}" class="block h-10 w-full rounded-[4px] border border-[#ddd] bg-white px-3 text-[14px] text-[#333] outline-none focus:border-[#aaa]" required />
                </div>
                <div>
                  <label class="mb-1.5 block text-[13px] text-[#888]">Alamat Lengkap</label>
                  <textarea name="address" rows="3" class="block w-full rounded-[4px] border border-[#ddd] bg-white px-3 py-2 text-[14px] text-[#333] outline-none focus:border-[#aaa]" required>{{ old('address', $user->address) }}</textarea>
                </div>
                <div class="pt-2">
                  <button type="submit" class="inline-flex h-10 items-center justify-center rounded-[4px] bg-[var(--brown)] px-6 text-[13px] font-medium text-white transition-colors hover:bg-[var(--ink)]">Simpan Pengiriman</button>
                </div>
              </form>
            </div>
          @elseif ($section === 'information')
            <div class="rounded-[8px] bg-white p-8 shadow-sm">
              <h2 class="mb-6 text-[18px] font-medium text-[#333]" style="font-family:sans-serif;">Informasi Akun</h2>
              
              <div class="mb-8 grid grid-cols-3 gap-4 max-sm:grid-cols-1">
                <div class="rounded-[4px] border border-[#eee] p-4 text-center">
                  <p class="text-[12px] text-[#888]">Total Order</p>
                  <p class="mt-1 text-[20px] font-medium text-[#333]">{{ $stats['total'] }}</p>
                </div>
                <div class="rounded-[4px] border border-[#eee] p-4 text-center">
                  <p class="text-[12px] text-[#888]">Order Aktif</p>
                  <p class="mt-1 text-[20px] font-medium text-[#333]">{{ $stats['active'] }}</p>
                </div>
                <div class="rounded-[4px] border border-[#eee] p-4 text-center">
                  <p class="text-[12px] text-[#888]">After-Sales</p>
                  <p class="mt-1 text-[20px] font-medium text-[#333]">{{ $stats['after_sales'] }}</p>
                </div>
              </div>
              
              <div class="space-y-4">
                <div class="flex flex-col border-b border-[#eee] pb-3">
                  <span class="text-[12px] text-[#888]">Email</span>
                  <span class="mt-1 text-[14px] text-[#333]">{{ $user->email }}</span>
                </div>
                <div class="flex flex-col border-b border-[#eee] pb-3">
                  <span class="text-[12px] text-[#888]">Telepon</span>
                  <span class="mt-1 text-[14px] text-[#333]">{{ $user->phone ?: 'Belum diatur' }}</span>
                </div>
                <div class="flex flex-col border-b border-[#eee] pb-3">
                  <span class="text-[12px] text-[#888]">Alamat Default</span>
                  <span class="mt-1 text-[14px] text-[#333]">{{ $user->address ?: 'Belum diatur' }}</span>
                </div>
              </div>
            </div>
          @elseif ($section === 'orders')
            <div class="rounded-[8px] bg-white p-8 shadow-sm">
              <div class="mb-6 flex items-center justify-between border-b border-[#eee] pb-4">
                <h2 class="text-[18px] font-medium text-[#333]" style="font-family:sans-serif;">Pesanan Saya</h2>
              </div>

            @if ($pesanans->isEmpty())
              <div class="py-12 text-center">
                <h3 class="mb-2 text-[18px] text-[#333]">Belum ada pesanan</h3>
                <p class="mb-6 text-[14px] text-[#888]">Anda belum melakukan pemesanan.</p>
                <a href="/shop" class="inline-flex h-10 items-center justify-center rounded-[4px] bg-[var(--brown)] px-6 text-[13px] font-medium text-white transition-colors hover:bg-[var(--ink)]">Mulai Belanja</a>
              </div>
            @else
              <div class="space-y-6">
                @foreach ($pesanans as $pesanan)
                  @php
                    $statusMeta = $orderStatusMap[$pesanan->status] ?? ['label' => ucfirst(str_replace('_', ' ', $pesanan->status))];
                  @endphp
                  <div class="rounded-[6px] border border-[#eee] p-5">
                    <div class="mb-4 flex items-start justify-between border-b border-[#eee] pb-4">
                      <div>
                        <span class="text-[12px] text-[#888]">{{ $pesanan->created_at->translatedFormat('d M Y') }}</span>
                        <h3 class="mt-1 text-[15px] font-medium text-[#333]">#{{ $pesanan->kode_pesanan }}</h3>
                      </div>
                      <div class="text-right">
                        <span class="text-[13px] font-medium" style="color: {{ strpos($statusMeta['class'], 'B42318') !== false ? '#dc3545' : (strpos($statusMeta['class'], '027A48') !== false ? '#28a745' : '#333') }}">{{ $statusMeta['label'] }}</span>
                        <p class="mt-1 text-[15px] font-medium text-[#333]">Rp {{ number_format($pesanan->total, 0, ',', '.') }}</p>
                      </div>
                    </div>

                    <div class="flex items-center justify-between">
                      <div class="text-[13px] text-[#666]">
                        {{ $pesanan->items->count() }} Produk (Total {{ $pesanan->items->sum('jumlah') }} pcs)
                      </div>
                      <div class="flex items-center gap-4">
                        @if ($pesanan->canRequestAfterSales())
                          <a href="{{ route('pesanan.show', $pesanan->kode_pesanan) }}#after-sales" class="text-[13px] text-[#666] hover:underline">After-Sales</a>
                        @endif
                        <a href="{{ route('pesanan.show', $pesanan->kode_pesanan) }}" class="inline-flex h-8 items-center justify-center rounded-[4px] border border-[var(--brown)] bg-[var(--brown)] px-4 text-[12px] font-medium text-white transition-colors hover:bg-[var(--ink)]">Detail</a>
                      </div>
                    </div>

                    @if ($pesanan->after_sales_status)
                      <div class="mt-3 rounded-[4px] bg-[#fff3cd] px-3 py-2 text-[12px] text-[#856404]">
                        Status After-sales: <strong>{{ $afterSalesTypeMap[$pesanan->after_sales_type] ?? ucfirst((string) $pesanan->after_sales_type) }}</strong>
                      </div>
                    @endif
                  </div>
                @endforeach
              </div>

              <div class="mt-6">
                {{ $pesanans->links() }}
              </div>
            @endif
            </div>
          @else
            <div class="rounded-[8px] bg-white p-8 shadow-sm">
              <h2 class="mb-6 text-[18px] font-medium text-[#333]" style="font-family:sans-serif;">Info Profil Saya</h2>
              <form method="POST" action="{{ route('account.profile.update') }}" class="space-y-4">
                @csrf
                @method('PATCH')
                <div>
                  <label class="mb-1.5 block text-[13px] text-[#888]">Nama Lengkap</label>
                  <input type="text" name="name" value="{{ old('name', $user->name) }}" class="block h-10 w-full rounded-[4px] border border-[#ddd] bg-white px-3 text-[14px] text-[#333] outline-none focus:border-[#aaa]" required />
                </div>
                <div>
                  <label class="mb-1.5 block text-[13px] text-[#888]">Email</label>
                  <input type="email" name="email" value="{{ old('email', $user->email) }}" class="block h-10 w-full rounded-[4px] border border-[#ddd] bg-white px-3 text-[14px] text-[#333] outline-none focus:border-[#aaa]" required />
                </div>
                <div>
                  <label class="mb-1.5 block text-[13px] text-[#888]">Nomor Telepon</label>
                  <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" class="block h-10 w-full rounded-[4px] border border-[#ddd] bg-white px-3 text-[14px] text-[#333] outline-none focus:border-[#aaa]" />
                </div>
                <div class="pt-2">
                  <button type="submit" class="inline-flex h-10 items-center justify-center rounded-[4px] bg-[var(--brown)] px-6 text-[13px] font-medium text-white transition-colors hover:bg-[var(--ink)]">Simpan Profil</button>
                </div>
              </form>
            </div>
          @endif
        </section>
      </section>
    </main>

    @include('components.site-footer')
  </body>
</html>
