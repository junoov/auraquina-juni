<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $page['title'] }} - Auraquina</title>
    <meta name="description" content="{{ $page['description'] }}" />
    <link rel="icon" href="https://d2kchovjbwl1tk.cloudfront.net/vendors/292/assets/image/1769740142660-Untitled-1_resized128-png.webp" />
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,500&family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>
  <body class="min-h-dvh bg-[var(--warm)] text-[var(--text)] antialiased [text-rendering:geometricPrecision]">
    @include('components.site-header', ['kategoris' => $kategoris, 'backHref' => '/'])

    <main class="mx-auto w-[min(860px,calc(100vw-32px))] py-10 max-sm:w-[calc(100vw-24px)] max-sm:py-7">
      <section class="rounded-[8px] border border-[var(--border)] bg-[var(--white)] px-7 py-8 shadow-[0_8px_28px_rgba(131,81,61,0.06)] max-sm:px-5 max-sm:py-6">
        <p class="mb-2 text-[11px] font-bold uppercase tracking-[0.18em] text-[var(--brown)]">{{ $page['eyebrow'] }}</p>
        <h1 class="text-[34px] leading-[1.1] text-[var(--ink)] max-sm:text-[28px]" style="font-family:'Plus Jakarta Sans',system-ui,sans-serif;font-weight:500;">{{ $page['title'] }}</h1>
        <p class="mt-3 max-w-[640px] text-[14px] leading-7 text-[var(--muted)]">{{ $page['description'] }}</p>

        <div class="mt-8 space-y-7">
          @foreach ($page['sections'] as $section)
            <section>
              <h2 class="mb-2 text-[18px] text-[var(--ink)]" style="font-family:'Plus Jakarta Sans',system-ui,sans-serif;font-weight:600;">{{ $section['heading'] }}</h2>
              <div class="space-y-2 text-[14px] leading-7 text-[var(--muted)]">
                @foreach ($section['body'] as $paragraph)
                  <p>{{ $paragraph }}</p>
                @endforeach
              </div>
            </section>
          @endforeach
        </div>
      </section>
    </main>
  </body>
</html>
