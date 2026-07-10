<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Create Account — Auraquina</title>
    <meta name="description" content="Buat akun Auraquina untuk pengalaman belanja yang lebih personal." />
    <link rel="icon" href="{{ asset('images/logo.png') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;0,900;1,400&family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>
  <body class="min-h-dvh bg-[var(--warm)] text-[var(--text)] antialiased" style="font-family:Lato,system-ui,sans-serif">
    @include('components.site-header', ['kategoris' => $kategoris ?? collect(), 'backHref' => '/login'])

    <main class="px-5 py-16 max-sm:py-10">
      <div class="mx-auto w-full max-w-[440px]">

        {{-- Title --}}
        <h1 class="mb-10 text-center text-[28px] font-normal tracking-[0.02em] text-[var(--ink)] max-sm:text-[24px]">Create Account</h1>

        {{-- Error messages --}}
        @if ($errors->any())
          <div role="alert" class="mb-6 rounded-[2px] border border-[#C24D3F]/40 bg-[#FBEDEA] px-4 py-3 text-[13px] leading-[18px] text-[#8A2E22]">
            <ul class="list-inside list-disc space-y-1">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        {{-- Form --}}
        <form method="POST" action="{{ route('register.store') }}" class="space-y-6" novalidate>
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

          {{-- Nama Depan --}}
          <div>
            <label for="first_name" class="mb-2.5 block text-[11px] font-bold uppercase tracking-[0.14em] text-[var(--ink)]">Nama Depan</label>
            <input
              type="text"
              id="first_name"
              name="first_name"
              value="{{ old('first_name') }}"
              autocomplete="given-name"
              required
              class="block h-[46px] w-full rounded-[2px] border border-[var(--border)] bg-[var(--white)] px-4 text-[14px] text-[var(--ink)] placeholder:text-[var(--muted)] focus:border-[var(--brown)] focus:outline-none focus:ring-1 focus:ring-[var(--brown)]/20 @error('first_name') border-[#C24D3F] @enderror"
            />
          </div>

          {{-- Nama Belakang --}}
          <div>
            <label for="last_name" class="mb-2.5 block text-[11px] font-bold uppercase tracking-[0.14em] text-[var(--ink)]">Nama Belakang</label>
            <input
              type="text"
              id="last_name"
              name="last_name"
              value="{{ old('last_name') }}"
              autocomplete="family-name"
              class="block h-[46px] w-full rounded-[2px] border border-[var(--border)] bg-[var(--white)] px-4 text-[14px] text-[var(--ink)] placeholder:text-[var(--muted)] focus:border-[var(--brown)] focus:outline-none focus:ring-1 focus:ring-[var(--brown)]/20 @error('last_name') border-[#C24D3F] @enderror"
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
                autocomplete="new-password"
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

          {{-- Konfirmasi Password --}}
          <div>
            <label for="password_confirmation" class="mb-2.5 block text-[11px] font-bold uppercase tracking-[0.14em] text-[var(--ink)]">Konfirmasi Password</label>
            <div class="relative">
              <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                autocomplete="new-password"
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

          {{-- No. Telephone --}}
          <div>
            <label for="phone" class="mb-2.5 block text-[11px] font-bold uppercase tracking-[0.14em] text-[var(--ink)]">No. Telephone</label>
            <div class="flex">
              <span class="flex h-[46px] items-center rounded-l-[2px] border border-r-0 border-[var(--border)] bg-[var(--cream)] px-3 text-[14px] text-[var(--ink)]">+62</span>
              <input
                type="tel"
                id="phone"
                name="phone"
                value="{{ old('phone') }}"
                autocomplete="tel"
                class="block h-[46px] w-full rounded-r-[2px] border border-[var(--border)] bg-[var(--white)] px-4 text-[14px] text-[var(--ink)] placeholder:text-[var(--muted)] focus:border-[var(--brown)] focus:outline-none focus:ring-1 focus:ring-[var(--brown)]/20 @error('phone') border-[#C24D3F] @enderror"
              />
            </div>
          </div>

          {{-- Tanggal Lahir --}}
          <div>
            <label for="date_of_birth" class="mb-2.5 block text-[11px] font-bold uppercase tracking-[0.14em] text-[var(--ink)]">Tanggal Lahir</label>
            <input
              type="date"
              id="date_of_birth"
              name="date_of_birth"
              value="{{ old('date_of_birth') }}"
              autocomplete="bday"
              class="block h-[46px] w-full rounded-[2px] border border-[var(--border)] bg-[var(--white)] px-4 text-[14px] text-[var(--ink)] focus:border-[var(--brown)] focus:outline-none focus:ring-1 focus:ring-[var(--brown)]/20 @error('date_of_birth') border-[#C24D3F] @enderror"
            />
          </div>

          {{-- Jenis Kelamin --}}
          <div>
            <label class="mb-3 block text-[11px] font-bold uppercase tracking-[0.14em] text-[var(--ink)]">Jenis Kelamin</label>
            <div class="flex gap-8">
              <label class="flex cursor-pointer items-center gap-2.5 select-none">
                <input type="radio" name="gender" value="wanita" {{ old('gender') === 'wanita' ? 'checked' : '' }} class="h-4 w-4 accent-[var(--brown)]" />
                <span class="text-[14px] text-[var(--ink)]">Wanita</span>
              </label>
              <label class="flex cursor-pointer items-center gap-2.5 select-none">
                <input type="radio" name="gender" value="pria" {{ old('gender') === 'pria' ? 'checked' : '' }} class="h-4 w-4 accent-[var(--brown)]" />
                <span class="text-[14px] text-[var(--ink)]">Pria</span>
              </label>
            </div>
          </div>

          {{-- Terms --}}
          <label class="flex cursor-pointer items-start gap-2.5 select-none pt-2">
            <input type="checkbox" name="terms" value="1" required class="mt-0.5 h-4 w-4 accent-[var(--brown)]" />
            <span class="text-[13px] leading-[18px] text-[var(--ink)]">
              Dengan mendaftar, Anda menyetujui isi
              <a href="{{ route('pages.show', 'privacy-policy') }}" class="font-bold text-[var(--brown)] underline underline-offset-2 hover:no-underline">Kebijakan Privasi</a>
              dan
              <a href="{{ route('pages.show', 'terms-conditions') }}" class="font-bold text-[var(--brown)] underline underline-offset-2 hover:no-underline">Ketentuan Layanan</a>
              kami.
            </span>
          </label>

          {{-- Sign Up button --}}
          <div class="pt-2">
            <button
              type="submit"
              class="flex h-[46px] min-w-[140px] items-center justify-center rounded-[2px] bg-[var(--brown)] px-7 text-[12px] font-bold uppercase tracking-[0.14em] text-[var(--white)] transition hover:bg-[#6E4332] focus:outline-none focus:ring-2 focus:ring-[var(--brown)]/30"
            >
              Sign up
            </button>
          </div>
        </form>

      </div>
    </main>

    <footer class="mt-auto border-t border-[var(--border)] bg-[var(--warm)] py-6 text-center text-[12px] leading-[18px] text-[var(--muted)]">
      © {{ date('Y') }} Auraquina. Designed for women who find beauty in simplicity.
    </footer>

    <script>
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
