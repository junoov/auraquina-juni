<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Password Baru — Auraquina</title>
    <meta name="description" content="Buat password baru untuk akun Auraquina Anda." />
    <link rel="icon" href="https://d2kchovjbwl1tk.cloudfront.net/vendors/292/assets/image/1769740142660-Untitled-1_resized128-png.webp" />
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,500&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>
  <body class="min-h-dvh bg-[var(--warm)] text-[var(--text)] antialiased [text-rendering:geometricPrecision]">
    @include('components.site-header', ['kategoris' => $kategoris ?? collect(), 'backHref' => '/login'])

    <main class="flex flex-1 items-center justify-center px-5 py-12 max-sm:py-8">
      <section class="w-full max-w-[420px] rounded-[8px] border border-[var(--border)] bg-[var(--white)] px-7 pt-8 pb-7 shadow-[0_8px_32px_rgba(131,81,61,0.08)] max-sm:px-5 max-sm:pt-7 max-sm:pb-6">
        <header class="mb-6 text-center">
          <p class="mb-2 text-[11px] font-bold tracking-[0.18em] uppercase text-[var(--sand)]">Create New Password</p>
          <h1 class="text-[32px] leading-[1.15] font-medium tracking-[-0.01em] text-[var(--ink)] max-sm:text-[28px]" style="font-family:'Plus Jakarta Sans',system-ui,sans-serif">Password <em>Baru</em></h1>
          <p class="mt-2 text-[13px] leading-[18px] text-[var(--muted)]">Masukkan email akun Anda dan pilih password baru untuk melanjutkan.</p>
        </header>

        @if (session('error'))
          <div class="mb-5 rounded-[6px] border border-[#C24D3F]/40 bg-[#FBEDEA] px-4 py-3 text-[13px] leading-[18px] text-[#8A2E22]">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
          <div class="mb-5 rounded-[6px] border border-[#C24D3F]/40 bg-[#FBEDEA] px-4 py-3 text-[13px] leading-[18px] text-[#8A2E22]">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" class="space-y-4" novalidate>
          @csrf
          <input type="hidden" name="token" value="{{ $token }}" />

          <div>
            <label for="email" class="mb-1.5 block text-[12px] font-bold tracking-[0.06em] uppercase text-[var(--ink)]">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email', $email) }}" autocomplete="email" required class="block h-12 w-full rounded-[6px] border border-[var(--border)] bg-[var(--white)] px-4 text-[14px] leading-5 text-[var(--ink)] placeholder:text-[var(--muted)] focus:border-[var(--brown)] focus:outline-none focus:ring-2 focus:ring-[var(--brown)]/20" />
          </div>

          <div>
            <label for="password" class="mb-1.5 block text-[12px] font-bold tracking-[0.06em] uppercase text-[var(--ink)]">Password Baru</label>
            <input type="password" id="password" name="password" autocomplete="new-password" required placeholder="Minimal 8 karakter" class="block h-12 w-full rounded-[6px] border border-[var(--border)] bg-[var(--white)] px-4 text-[14px] leading-5 text-[var(--ink)] placeholder:text-[var(--muted)] focus:border-[var(--brown)] focus:outline-none focus:ring-2 focus:ring-[var(--brown)]/20" />
          </div>

          <div>
            <label for="password_confirmation" class="mb-1.5 block text-[12px] font-bold tracking-[0.06em] uppercase text-[var(--ink)]">Konfirmasi Password Baru</label>
            <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password" required placeholder="Ulangi password baru" class="block h-12 w-full rounded-[6px] border border-[var(--border)] bg-[var(--white)] px-4 text-[14px] leading-5 text-[var(--ink)] placeholder:text-[var(--muted)] focus:border-[var(--brown)] focus:outline-none focus:ring-2 focus:ring-[var(--brown)]/20" />
          </div>

          <button type="submit" class="mt-2 flex h-12 w-full items-center justify-center rounded-[6px] bg-[var(--brown)] text-[13px] font-bold tracking-[0.08em] uppercase text-[var(--white)] transition hover:bg-[#6E4332] focus:outline-none focus:ring-2 focus:ring-[var(--brown)]/30">Simpan Password Baru</button>
        </form>
      </section>
    </main>
  </body>
</html>
