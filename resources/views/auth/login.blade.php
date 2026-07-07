<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Login — Auraquina</title>
    <meta name="description" content="Masuk ke akun Auraquina untuk melanjutkan belanja." />
    <link rel="icon" href="https://d2kchovjbwl1tk.cloudfront.net/vendors/292/assets/image/1769740142660-Untitled-1_resized128-png.webp" />
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;0,900;1,400&family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>
  <body class="min-h-dvh bg-[var(--warm)] text-[var(--text)] antialiased" style="font-family:Lato,system-ui,sans-serif">
    @include('components.site-header', ['kategoris' => $kategoris ?? collect(), 'backHref' => '/'])

    <main class="px-5 py-16 max-sm:py-10">
      <div class="mx-auto w-full max-w-[440px]">

        {{-- Title --}}
        <h1 class="mb-10 text-center text-[28px] font-normal tracking-[0.02em] text-[var(--ink)] max-sm:text-[24px]">Login</h1>

        {{-- Error messages --}}
        @if (session('status'))
          <div class="mb-6 rounded-[2px] border border-[var(--border)] bg-[var(--cream)] px-4 py-3 text-[13px] leading-[18px] text-[var(--ink)]">
            {{ session('status') }}
          </div>
        @endif

        @if ($errors->any())
          <div role="alert" class="mb-6 rounded-[2px] border border-[#C24D3F]/40 bg-[#FBEDEA] px-4 py-3 text-[13px] leading-[18px] text-[#8A2E22]">
            {{ $errors->first() }}
          </div>
        @endif

        {{-- Form --}}
        <form method="POST" action="{{ route('login.attempt') }}" class="space-y-6" novalidate>
          @csrf

          {{-- Email --}}
          <div>
            <label for="email" class="mb-2.5 block text-[11px] font-bold uppercase tracking-[0.14em] text-[var(--ink)]">Email</label>
            <input
              type="email"
              id="email"
              name="email"
              value="{{ old('email') }}"
              autocomplete="email"
              autofocus
              required
              class="block h-[46px] w-full rounded-[2px] border border-[var(--border)] bg-[var(--white)] px-4 text-[14px] text-[var(--ink)] placeholder:text-[var(--muted)] focus:border-[var(--brown)] focus:outline-none focus:ring-1 focus:ring-[var(--brown)]/20 @error('email') border-[#C24D3F] @enderror"
            />
          </div>

          {{-- Password --}}
          <div>
            <label for="password" class="mb-2.5 block text-[11px] font-bold uppercase tracking-[0.14em] text-[var(--ink)]">Password</label>
            <div class="relative">
              <input
                type="password"
                id="password"
                name="password"
                autocomplete="current-password"
                required
                class="block h-[46px] w-full rounded-[2px] border border-[var(--border)] bg-[var(--white)] pr-12 pl-4 text-[14px] text-[var(--ink)] placeholder:text-[var(--muted)] focus:border-[var(--brown)] focus:outline-none focus:ring-1 focus:ring-[var(--brown)]/20 @error('password') border-[#C24D3F] @enderror"
              />
              <button
                type="button"
                data-toggle-password
                aria-label="Tampilkan password"
                class="absolute top-1/2 right-2 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded text-[var(--muted)] transition hover:bg-[var(--cream)] hover:text-[var(--ink)]"
              >
                <svg data-icon-eye aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-[18px] w-[18px] stroke-[1.6]">
                  <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z" /><circle cx="12" cy="12" r="3" />
                </svg>
                <svg data-icon-eye-off aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="hidden h-[18px] w-[18px] stroke-[1.6]">
                  <path d="m3 3 18 18M10.6 5.1A9.4 9.4 0 0 1 12 5c6.5 0 10 7 10 7a17 17 0 0 1-3.2 4M6.7 6.7A17 17 0 0 0 2 12s3.5 7 10 7a9.4 9.4 0 0 0 4.3-1.1M9.9 9.9a3 3 0 0 0 4.2 4.2" />
                </svg>
              </button>
            </div>
          </div>

          {{-- Forgot password --}}
          <div class="pt-1">
            <a href="{{ route('password.request') }}" class="text-[11px] font-bold uppercase tracking-[0.14em] text-[var(--ink)] underline underline-offset-2 hover:text-[var(--brown)] transition">Forgot your password?</a>
          </div>

          {{-- Buttons --}}
          <div class="flex items-center gap-4 pt-2 max-sm:flex-col max-sm:gap-3">
            <button
              type="submit"
              class="flex h-[46px] min-w-[140px] items-center justify-center rounded-[2px] bg-[var(--brown)] px-7 text-[12px] font-bold uppercase tracking-[0.14em] text-[var(--white)] transition hover:bg-[#6E4332] focus:outline-none focus:ring-2 focus:ring-[var(--brown)]/30 max-sm:w-full"
            >
              Login
            </button>
            <a
              href="/register"
              class="flex h-[46px] min-w-[140px] items-center justify-center rounded-[2px] border-2 border-[var(--brown)] bg-transparent px-7 text-[12px] font-bold uppercase tracking-[0.14em] text-[var(--brown)] transition hover:bg-[var(--brown)] hover:text-[var(--white)] focus:outline-none focus:ring-2 focus:ring-[var(--brown)]/30 max-sm:w-full"
            >
              Sign up
            </a>
          </div>
        </form>

      </div>
    </main>

    <footer class="mt-auto border-t border-[var(--border)] bg-[var(--warm)] py-6 text-center text-[12px] leading-[18px] text-[var(--muted)]">
      © {{ date('Y') }} Auraquina. Designed for women who find beauty in simplicity.
    </footer>

    <script>
      // Toggle password visibility
      document.querySelectorAll('[data-toggle-password]').forEach((btn) => {
        btn.addEventListener('click', () => {
          const wrapper = btn.closest('.relative');
          if (!wrapper) return;
          const input = wrapper.querySelector('input[type="password"], input[type="text"]');
          if (!input) return;
          const eye = btn.querySelector('[data-icon-eye]');
          const eyeOff = btn.querySelector('[data-icon-eye-off]');
          const showing = input.type === 'text';
          input.type = showing ? 'password' : 'text';
          btn.setAttribute('aria-label', showing ? 'Tampilkan password' : 'Sembunyikan password');
          if (eye && eyeOff) {
            eye.classList.toggle('hidden', !showing);
            eyeOff.classList.toggle('hidden', showing);
          }
        });
      });
    </script>
  </body>
</html>
