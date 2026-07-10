<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Lupa Password — Auraquina</title>
    <meta name="description" content="Minta tautan reset password Auraquina melalui email Anda." />
    <link rel="icon" href="{{ asset('images/logo.png') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,500&family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>
  <body class="min-h-dvh bg-[var(--warm)] text-[var(--text)] antialiased [text-rendering:geometricPrecision]">
    @include('components.site-header', ['kategoris' => $kategoris ?? collect(), 'backHref' => '/login'])

    <main class="flex flex-1 items-center justify-center px-5 py-12 max-sm:py-8">
      <section class="w-full max-w-[420px] rounded-[8px] border border-[var(--border)] bg-[var(--white)] px-7 pt-8 pb-7 shadow-[0_8px_32px_rgba(131,81,61,0.08)] max-sm:px-5 max-sm:pt-7 max-sm:pb-6">
        <header class="mb-6 text-center">
          <p class="mb-2 text-[11px] font-bold tracking-[0.18em] uppercase text-[var(--sand)]">Password Recovery</p>
          <h1 class="text-[32px] leading-[1.15] font-medium tracking-[-0.01em] text-[var(--ink)] max-sm:text-[28px]" style="font-family:'Plus Jakarta Sans',system-ui,sans-serif">Reset <em>Password</em></h1>
          <p class="mt-2 text-[13px] leading-[18px] text-[var(--muted)]">Masukkan email akun Anda dan kami akan mengirimkan tautan untuk membuat password baru.</p>
        </header>

        @if (session('status'))
          <div class="mb-5 rounded-[6px] border border-[var(--border)] bg-[var(--cream)] px-4 py-3 text-[13px] leading-[18px] text-[var(--ink)]">{{ session('status') }}</div>
        @endif
        @if (session('error'))
          <div class="mb-5 rounded-[6px] border border-[#C24D3F]/40 bg-[#FBEDEA] px-4 py-3 text-[13px] leading-[18px] text-[#8A2E22]">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
          <div class="mb-5 rounded-[6px] border border-[#C24D3F]/40 bg-[#FBEDEA] px-4 py-3 text-[13px] leading-[18px] text-[#8A2E22]">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4" novalidate>
          @csrf
          <div>
            <label for="email" class="mb-1.5 block text-[12px] font-bold tracking-[0.06em] uppercase text-[var(--ink)]">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" autocomplete="email" required placeholder="nama@email.com" class="block h-12 w-full rounded-[6px] border border-[var(--border)] bg-[var(--white)] px-4 text-[14px] leading-5 text-[var(--ink)] placeholder:text-[var(--muted)] focus:border-[var(--brown)] focus:outline-none focus:ring-2 focus:ring-[var(--brown)]/20" />
          </div>
          <button type="submit" class="mt-2 flex h-12 w-full items-center justify-center rounded-[6px] bg-[var(--brown)] text-[13px] font-bold tracking-[0.08em] uppercase text-[var(--white)] transition hover:bg-[#6E4332] focus:outline-none focus:ring-2 focus:ring-[var(--brown)]/30">Kirim Tautan Reset</button>
        </form>

        <p class="mt-6 text-center text-[13px] leading-[20px] text-[var(--muted)]">
          Sudah ingat password?
          <a href="{{ route('login') }}" class="font-bold text-[var(--brown)] hover:underline">Kembali masuk</a>
        </p>
      </section>
    </main>
  </body>
</html>
