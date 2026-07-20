<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Auraquina</title>
    <meta name="description" content="Auraquina" />
    <link rel="icon" href="{{ asset('images/logo.png') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;1,300&family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&family=Alex+Brush&family=Great+Vibes&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>
  <body class="min-h-full overflow-x-clip bg-[var(--warm)] text-[var(--text)] antialiased [text-rendering:geometricPrecision]">
    @php
      $heroImages = collect([
          [
              'desktop' => asset("images/hero-baru/hero-1-desktop.webp"),
              'mobile' => asset("images/hero-baru/hero-1-mobile.webp"),
              'fallback' => asset("images/hero-baru/hero-1.png"),
              'tag' => 'New Collection',
              'title' => 'The Harmony of Simplicity',
              'desc' => 'Koleksi modest wear eksklusif yang memadukan keanggunan klasik dengan sentuhan bersahaja.',
          ],
          [
              'desktop' => asset("images/hero-baru/hero-2-desktop.webp"),
              'mobile' => asset("images/hero-baru/hero-2-mobile.webp"),
              'fallback' => asset("images/hero-baru/hero-2.png"),
              'tag' => 'Philosophy',
              'title' => 'Crafted to Perfection',
              'desc' => 'Setiap helai kain dipilih untuk kenyamanan maksimal dan penampilan yang abadi.',
          ],
          [
              'desktop' => asset("images/hero-baru/hero-3-desktop.webp"),
              'mobile' => asset("images/hero-baru/hero-3-mobile.webp"),
              'fallback' => asset("images/hero-baru/hero-3.png"),
              'tag' => 'Lookbook',
              'title' => 'Graceful Movement',
              'desc' => 'Didesain untuk melengkapi setiap langkahmu dengan kelembutan dan kesopanan.',
          ],
          [
              'desktop' => asset("images/hero-baru/hero-4-desktop.webp"),
              'mobile' => asset("images/hero-baru/hero-4-mobile.webp"),
              'fallback' => asset("images/hero-baru/hero-4.png"),
              'tag' => 'Best Seller',
              'title' => 'Classic Elegance',
              'desc' => 'Temukan potongan favorit pelanggan kami yang dirancang untuk kecantikan alami.',
          ],
      ]);
      $imageVariants = app(\App\Services\ProductImageVariantService::class);
      $productImageUrl = fn (?string $path) => $imageVariants->url($path, 'card');
      $productImageSrcset = fn (?string $path) => $imageVariants->srcset($path, ['card' => 600, 'detail' => 1200]);
      $homeProductCards = ($produkUnggulan ?? collect())->map(fn ($produk) => [
          'name' => $produk->nama,
          'price' => $produk->hargaFormatted(),
          'price_coret' => $produk->hargaCoretFormatted(),
          'has_discount' => $produk->hasDiscount(),
          'discount_percent' => $produk->discountPercent(),
          'img' => $productImageUrl($produk->gambarUtama?->url) ?? '',
          'srcset' => $productImageSrcset($produk->gambarUtama?->url),
          'href' => '/shop/'.$produk->slug,
          'desc' => $produk->deskripsi_singkat ?: Str::limit((string) $produk->deskripsi, 130),
          'badge' => $produk->badge ? (strtolower($produk->badge) === 'new arrival' ? 'Terbaru' : Str::title($produk->badge)) : 'Terbaru',
      ])->values();
      $featuredProduct = $homeProductCards->first();
      $products = $homeProductCards->skip(1)->take(6)->values();
      if ($products->isEmpty()) {
          $products = $homeProductCards->take(6)->values();
      }
      $bestSellers = $homeProductCards->take(4)->values();
      $serviceItems = [
          [
              'title' => 'Bahan Premium',
              'desc' => 'Nyaman dipakai seharian.',
              'icon' => '<path d="M12 2 4 6.5v9L12 20l8-4.5v-9L12 2Zm0 0v8.8m8-4.3-8 4.3m-8-4.3 8 4.3m0 9.2v-9.2" />',
          ],
          [
              'title' => 'Desain Abadi',
              'desc' => 'Gaya minimal yang selalu relevan.',
              'icon' => '<path d="M12 7v5l3 2m5-2a8 8 0 1 1-16 0 8 8 0 0 1 16 0ZM9 2h6" />',
          ],
          [
              'title' => 'Mudah Dipadukan',
              'desc' => 'Mudah dipadukan untuk setiap kesempatan.',
              'icon' => '<path d="M4 14a8 8 0 0 1 16 0v5a2 2 0 0 1-2 2h-2v-7h4M4 14h4v7H6a2 2 0 0 1-2-2v-5Z" />',
          ],
      ];
      $socialIcons = [
          ['label' => 'Instagram', 'href' => 'https://www.instagram.com/auraquina/', 'icon' => '<rect x="4" y="4" width="16" height="16" rx="4" /><circle cx="12" cy="12" r="3.5" /><path d="M17 7h.01" />'],
          ['label' => 'TikTok', 'href' => 'https://www.tiktok.com/@auraquina_', 'icon' => '<path d="M14 3v11.5a4 4 0 1 1-4-4" /><path d="M14 6a6 6 0 0 0 5 3" />'],
      ];
      $payments = [
          [
              'name' => 'QRIS',
              'svg' => '<svg fill="none" viewBox="0 0 80 30" style="height: 18px; width: auto; opacity: 0.95;"><g fill="#000" clip-path="url(#clip0_3802_26543)"><path d="M76.723 10.477H64.024V7.936h12.699v-5.08H56.405v12.699h12.699v2.541H56.405v5.08h20.318zM53.867 2.856h-5.08v20.317h5.08zm-27.937 0v5.08h15.238v2.541H25.931v12.699h5.077v-7.544l7.621 7.544h7.619l-7.95-7.621h7.95V2.856zM10.69 15.555h5.067v-5.067h-5.066zm1.273-3.808H14.5v2.538h-2.538z"/><path d="M8.152 2.856H3.707a.64.64 0 0 0-.587.393.6.6 0 0 0-.048.244V22.54a.635.635 0 0 0 .635.634H15.77v-5.066H8.152zm14.603 0H10.69v5.08h7.621v7.619h5.067V3.493a.64.64 0 0 0-.179-.45.65.65 0 0 0-.445-.187m.638 15.24h-5.08v11.43h5.08z"/><path d="M10.16 0H.635A.637.637 0 0 0 0 .635v9.525h1.27V1.893a.637.637 0 0 1 .634-.624h8.267zm68.57 16.507v8.266a.637.637 0 0 1-.634.635h-8.267v1.259h9.526a.64.64 0 0 0 .645-.635v-9.525z"/></g><defs><clipPath id="clip0_3802_26543"><path fill="#fff" d="M0 0h80v29.525H0z"/></clipPath></defs></svg>',
          ],
          [
              'name' => 'BCA',
              'svg' => '<svg fill="none" viewBox="0 0 80 26" style="height: 18px; width: auto; opacity: 0.95;"><g fill="#0060AF" clip-path="url(#clip0_3802_10653)"><path d="M11.788 19.03c0-.999.01-3.67-.014-3.999.022-3.974-2.868-6.778-4.694-6.56-1.263.11-2.322.625-2.89 2.107-.527 1.381-.056 3.218 1.695 3.65 1.874.462 2.967.847 3.759 1.39.97.666 1.762 1.937 1.783 3.414"/><path d="M12.535 25.072c-3.302 0-6.697-.813-10.086-2.422l-.084-.041-.04-.085C.806 19.314 0 15.801 0 12.365c0-3.43.771-6.793 2.294-10l.042-.085.084-.042C5.556.752 8.93 0 12.45 0c3.279 0 6.78.838 10.126 2.427l.086.038.04.087c1.55 3.27 2.367 6.781 2.367 10.16 0 3.366-.785 6.731-2.337 10l-.041.085-.086.04c-3.088 1.462-6.57 2.235-10.07 2.235M2.762 22.21c3.293 1.549 6.578 2.33 9.773 2.33 3.39 0 6.76-.74 9.76-2.144 1.49-3.167 2.245-6.426 2.245-9.684 0-3.27-.789-6.673-2.28-9.847C19.014 1.34 15.624.53 12.45.53c-3.409 0-6.675.723-9.716 2.15C1.274 5.786.53 9.043.53 12.364c0 3.327.772 6.73 2.231 9.845"/><path d="M11.005 19.032c.006-1.28-.709-2.413-1.643-3.022-.83-.538-1.942-.891-3.737-1.347-.555-.142-1.135-.457-1.315-.859-.475.48-.562 1.556-.478 2.185.097.729.948 1.929 2.23 1.976.782.031 1.771-.169 2.246-.27.818-.176 2.113.336 2.32 1.336M12.45 2.277c-2.173 0-4.05 1.433-4.043 3.912.007 2.085 1.684 3.201 2.282 3.999.904 1.201 1.393 2.623 1.444 4.799.04 1.731.038 3.441.047 4.047h.48c-.01-.634-.031-2.449-.006-4.1.032-2.177.54-3.545 1.444-4.746.603-.798 2.279-1.914 2.283-3.999.008-2.479-1.868-3.912-4.039-3.912"/><path d="M13.001 19.03c0-.999-.011-3.67.013-3.999-.021-3.974 2.867-6.778 4.694-6.56 1.263.11 2.32.625 2.891 2.107.526 1.381.053 3.218-1.697 3.65-1.874.462-2.967.847-3.76 1.39-.969.666-1.705 1.937-1.728 3.414"/><path d="M13.783 19.032c-.006-1.28.708-2.413 1.64-3.022.832-.538 1.946-.891 3.74-1.347.556-.142 1.136-.457 1.312-.859.478.48.564 1.556.48 2.185-.099.729-.948 1.929-2.227 1.976-.782.031-1.777-.169-2.25-.27-.814-.176-2.113.336-2.32 1.336m.296 4.137-.279-2.03.672-.1c.163-.023.362.005.442.108a.57.57 0 0 1 .131.335c.026.173-.025.374-.22.474v.006c.218 0 .35.157.388.421.006.056.022.19.006.303-.044.268-.204.354-.474.392zm.431-.364c.08-.011.16-.015.224-.055.096-.064.088-.198.074-.299-.034-.22-.09-.303-.324-.269l-.147.023.093.612zm-.14-.937c.089-.015.21-.025.26-.11.026-.056.06-.1.037-.226-.027-.148-.076-.24-.265-.204l-.176.028.07.52m2.689.072q.01.059.014.118c.054.37-.014.676-.43.76-.614.119-.732-.263-.84-.798l-.058-.29c-.085-.51-.121-.898.479-1.018.337-.061.56.073.654.41.014.05.032.1.039.15l-.368.076c-.042-.127-.099-.352-.264-.333-.297.035-.199.405-.168.56l.11.555c.033.168.1.436.356.384.209-.042.118-.367.1-.5m.966.54-.127-2.1.495-.15 1.035 1.826-.389.116-.245-.464-.432.13.056.528zm.3-.98.313-.091-.415-.85m-12.401.334c.154-.495.293-.86.883-.697.316.088.511.227.502.594-.001.081-.028.165-.046.245l-.367-.101c.048-.203.078-.363-.171-.44-.289-.08-.359.27-.399.421l-.15.55c-.047.164-.104.434.15.504.21.056.337-.15.413-.45l-.257-.068.09-.319.603.195-.287 1.06-.278-.075.063-.224h-.008a.39.39 0 0 1-.42.176c-.605-.163-.542-.558-.398-1.086m2.326.611-.182.863-.41-.088.437-2.016.698.158c.408.088.532.27.474.645-.033.215-.14.447-.398.428l-.003-.004c.22.077.238.187.2.378-.017.081-.13.574-.052.653l.002.06-.423-.11c-.018-.137.042-.382.066-.518.024-.12.062-.29-.06-.353-.096-.051-.131-.049-.24-.074zm.07-.312.275.074c.168.024.26-.062.294-.265.03-.185-.009-.258-.16-.294l-.295-.06m2.443.067.405.047-.175 1.416c-.085.45-.258.646-.752.584-.503-.063-.622-.292-.592-.745l.176-1.415.408.047-.175 1.383c-.019.15-.054.373.214.4.237.018.29-.139.317-.334m1.025.742.122-2.026.778.034c.368.018.464.318.453.605-.01.174-.065.369-.217.474a.68.68 0 0 1-.433.104l-.254-.014-.05.849zm.46-1.132.207.012c.167.006.278-.06.293-.306.008-.237-.082-.277-.298-.286l-.164-.006m54.39-17.252L63.269 9.85c-1.168-.948-2.593-1.646-4.412-1.646-4.304 0-6.053 3.209-6.053 5.469 0 1.678 1.099 4.153 4.929 4.153 1.607 0 3.893-1.119 4.55-1.627l-3.059 6.513c-1.458.29-1.937.471-3.171.51-6.855.204-9.625-4.007-9.623-8.31.005-5.688 5.062-12.606 13.446-12.606.514 0 1.142.177 1.68.374l.542-.694m17.026.174L80 22.566h-6.518l-.004-3.5h-4.445l-1.463 3.5h-7.069l7.39-14.57-1.666-.011 3.167-5.823zm-5.688 6.243-2.513 5.935h2.588"/><path d="M42.315 2.16c3.228.019 5.052 1.771 5.052 4.302 0 2.334-1.924 4.4-4.036 5.467 2.174.8 2.363 2.761 2.363 4.15 0 3.353-3.365 6.486-7.74 6.486h-9.538l3.72-14.367-1.528-.01 3.125-6.027s5.957-.018 8.582 0m-3.167 8.274c.668 0 1.847-.17 2.141-1.462.323-1.402-.783-1.44-1.314-1.44l-1.896-.008-.662 2.91zm-2.681 3.605-.873 3.354h2.233c.878 0 2.075-.436 2.369-1.528.29-1.094-.547-1.826-1.423-1.826"/></g><defs><clipPath id="clip0_3802_10653"><path fill="#fff" d="M0 0h80v25.072H0z"/></clipPath></defs></svg>',
          ],
          [
              'name' => 'Mandiri',
              'svg' => '<svg fill="none" viewBox="0 0 80 25" style="height: 18px; width: auto; opacity: 0.95;"><g fill-rule="evenodd" clip-path="url(#clip0_3802_8197)" clip-rule="evenodd"><path fill="url(#paint0_linear_3802_8197)" d="M58.899.896c-1.54.921-5.172 3.073-6.529 3.875-.826.435-2.74.624-3.822-.883-.02-.028-1.44-1.86-1.498-1.93-.041-.049-.96-1.456-3.005-1.5-.303-.007-1.806-.015-3.273.846L34.29 5.13q0 .002-.004.003l-3.316 1.96 1.715 2.176c.803 1.03 2.613 1.826 4.182.895 0 0 5.8-3.469 5.821-3.479 2.508-1.42 4.444-1.42 5.728-.892 1.153.503 2.156 1.757 2.156 1.757s1.31 1.678 1.542 1.972c.746.948 1.98.576 1.98.576s.458-.054 1.148-.477l5.62-3.365c1.784-1.081 3.42-1.283 4.256-1.204 2.62.246 3.433 2.136 4.568 3.454.669.776 1.272 1.216 2.195 1.194.606-.013 1.291-.393 1.392-.462L80 5.228s-.69-1.073-2.104-2.744c-1.265-1.49-2.609-.816-3.68-.268-.45.23-2.08 1.35-3.697 2.132-1.15.557-2.805.39-3.71-.767-.055-.07-1.521-1.904-1.675-2.13C64.542.686 63.387 0 61.907 0c-.9 0-1.92.253-3.008.896"/><path fill="#003A70" d="M.084 16.573A45 45 0 0 0 0 13.475h2.194l.103 1.54h.062c.497-.814 1.406-1.776 3.103-1.776 1.324 0 2.358.771 2.792 1.925h.043c.352-.577.764-1.004 1.24-1.304.559-.406 1.2-.62 2.028-.62 1.675 0 3.37 1.174 3.37 4.508v6.132h-2.482v-5.746c0-1.731-.578-2.758-1.798-2.758-.87 0-1.512.642-1.78 1.39a3.8 3.8 0 0 0-.124.874v6.24H6.268v-6.025c0-1.452-.558-2.479-1.737-2.479-.952 0-1.592.77-1.82 1.495a2.3 2.3 0 0 0-.146.855v6.154H.085zm24.521 4.807c0 .94.041 1.858.145 2.5h-2.297l-.165-1.153h-.062c-.62.813-1.676 1.389-2.979 1.389-2.027 0-3.165-1.516-3.165-3.098 0-2.63 2.254-3.953 5.978-3.931v-.172c0-.684-.27-1.816-2.048-1.816-.993 0-2.028.321-2.71.769l-.497-1.708c.746-.47 2.05-.92 3.642-.92 3.228 0 4.158 2.116 4.158 4.38zm-2.483-2.584c-1.8-.043-3.516.363-3.516 1.944 0 1.025.641 1.496 1.448 1.496a2.07 2.07 0 0 0 1.985-1.432c.062-.192.083-.407.083-.577zm4.242-2.223a47 47 0 0 0-.083-3.098h2.232l.125 1.56h.061c.435-.81 1.531-1.796 3.207-1.796 1.758 0 3.579 1.175 3.579 4.467v6.174H32.94v-5.875c0-1.496-.538-2.629-1.924-2.629-1.014 0-1.717.748-1.986 1.539-.082.236-.103.557-.103.854v6.111h-2.564zm20.152-7.278v11.657c0 1.068.04 2.224.082 2.929h-2.276l-.102-1.645h-.042c-.6 1.154-1.822 1.88-3.29 1.88-2.398 0-4.301-2.115-4.301-5.32-.022-3.483 2.088-5.556 4.508-5.556 1.386 0 2.38.599 2.834 1.369h.042V9.295zM43.97 17.81c0-.211-.021-.469-.061-.682-.228-1.025-1.035-1.859-2.194-1.859-1.634 0-2.544 1.496-2.544 3.44 0 1.902.91 3.29 2.524 3.29 1.033 0 1.944-.725 2.192-1.857a3 3 0 0 0 .083-.771zm4.343 6.07h2.568V13.476h-2.568zm4.366-7.05c0-1.41-.022-2.416-.083-3.355h2.214l.08 1.986h.086c.496-1.474 1.675-1.986 2.75-1.986.248 0 .393-.044.6 0v2.308a3.4 3.4 0 0 0-.745-.085c-1.22 0-2.048.811-2.275 1.985-.04.236-.082.515-.082.815v5.383h-2.545zm6.826 7.05h2.564V13.476h-2.564z"/></g><defs><linearGradient id="paint0_linear_3802_8197" x1="0" x2="80" y1="24.116" y2="24.116" gradientUnits="userSpaceOnUse"><stop stop-color="#FFCA06"/><stop offset=".331" stop-color="#FBAA18"/><stop offset=".695" stop-color="#FFC907"/><stop offset="1" stop-color="#FAA619"/></linearGradient><clipPath id="clip0_3802_8197"><path fill="#fff" d="M0 0h80v24.116H0z"/></clipPath></defs></svg>',
          ],
          [
              'name' => 'BNI',
              'svg' => '<svg fill="none" viewBox="0 0 80 24" style="height: 18px; width: auto; opacity: 0.95;"><g fill-rule="evenodd" clip-path="url(#clip0_3802_11102)" clip-rule="evenodd"><path fill="#F15A23" d="m0 8.135 6.749 8.514L0 22.24zm2.716 14.106 5.079-4.248 3.51 4.248zM0 3.58l1.761 2.228 7.242 9.048 2.346-1.868s-1.948-2.294-3.256-4.853C5.536 3.132 7.346.016 7.346.016H0zm10.035 12.673 2.39-1.974s2.282 3.005 4.929 3.714c3.468.93 5.003-1.344 5.003-1.344v5.592H14.74l1.717-1.485s-.896.598-3.088-.92c-1.293-.896-3.334-3.583-3.334-3.583M10.255 0s-.763 1.039-.22 3.58c.58 2.716 2.887 5.59 2.882 5.526 0 0 .148-2.854 1.823-4.53 3.57-3.57 7.617-1.444 7.617-1.444V.016z"/><path fill="#F15A23" d="M22.357 6.896S20.1 4.96 18.137 4.96c-2.512 0-3.883 2.168-3.883 3.741 0 2.29 1.1 3.564 2.204 4.667 1.585 1.586 3.515 3.297 5.9 1.92z"/><path fill="#005E6A" d="M48.072.233H53.4s4.72 7.147 7.04 10.1a341 341 0 0 1 4.816 6.316V1.834c0-.64-1.68-1.601-1.68-1.601h6.145s-2.017.828-2.017 1.6v21.553s-1.934-1.072-4.128-3.621C61.111 16.9 52.344 5.338 52.344 5.338v14.976c0 .767 1.68 1.927 1.68 1.927h-5.952s1.632-1.17 1.632-1.927V1.834c0-.68-1.632-1.601-1.632-1.601m25.09 0H80s-1.836.87-1.836 1.6v18.481c0 .799 1.836 1.927 1.836 1.927h-6.838s1.71-1.154 1.71-1.927V1.834c0-.703-1.71-1.601-1.71-1.601m-45.209 0s1.773.884 1.773 1.6v18.481c0 .786-1.773 1.927-1.773 1.927h10.13c.634 0 7.725-1.16 7.725-6.627S40.3 9.635 40.3 9.635s3.356-.933 3.356-4.674c0-4.032-4.94-4.728-5.572-4.728zm5.382 9.04v-6.88h4.115c.633 0 2.85.739 2.85 3.481 0 2.261-2.216 3.399-2.85 3.399zm0 1.804h4.749c.633 0 4.368.97 4.368 4.212 0 3.304-3.735 4.476-4.368 4.476h-4.75z"/></g><defs><clipPath id="clip0_3802_11102"><path fill="#fff" d="M0 0h80v23.386H0z"/></clipPath></defs></svg>',
          ],
          [
              'name' => 'BRI',
              'svg' => '<svg fill="none" viewBox="0 0 80 31" style="height: 18px; width: auto; opacity: 0.95;"><g fill="#00529C" clip-path="url(#clip0_3802_11089)"><path d="M25.636 0H4.616C2.069 0 0 2.118 0 4.734v20.81c0 2.593 2.027 4.697 4.548 4.735H25.62c2.549 0 4.622-2.118 4.622-4.734l.01-20.81C30.253 2.117 28.184 0 25.636 0M7.115 27.853l-1.147.014c-1.39 0-2.51-1.143-2.51-2.549l-.009-.202V5.898l.009-1.036c.056-1.36 1.087-2.458 2.44-2.458h2.33c2.116 0 3.827 1.838 3.827 4.002a3.96 3.96 0 0 1-1.102 2.755l-5.945 6.03 5.574 5.678c.716.742 1.15 1.716 1.15 2.78 0 2.322-2.064 4.204-4.617 4.204m17.138-.017-10.394-.003s1.201-2.6 1.201-4.207c0-1.959-.655-3.712-1.68-4.87l-3.553-3.624 3.623-3.746c1.085-1.019 1.8-2.821 1.8-4.872 0-1.625-.448-3.064-1.175-4.11h2.52c2.114 0 3.826 1.838 3.826 4.002a3.97 3.97 0 0 1-1.098 2.755l-5.835 5.968 12.115 12.355c-.386.242-.869.352-1.35.352m2.535-3.859-8.683-8.85 4.135-4.2c.843-1.056 1.38-2.641 1.38-4.413 0-1.634-.458-3.112-1.188-4.157l1.849.058c1.386 0 2.514 1.14 2.514 2.55zm22.095-10.259q.255-.255.6-.653.345-.4.653-.945t.526-1.216.218-1.434q0-1.488-.508-2.777a6 6 0 0 0-1.561-2.252q-1.053-.96-2.633-1.507-1.578-.544-3.685-.544h-9.258v25.415H43.4q2.214 0 3.867-.599 1.651-.6 2.74-1.652a6.6 6.6 0 0 0 1.617-2.45q.526-1.397.526-2.996 0-2.141-.98-3.812t-2.288-2.578m-4.495-6.671q.516.194.851.514.867.831.868 1.988 0 1.05-.362 1.736-.362.688-.723 1.048h-7.067V6.747h4.393q1.233 0 2.04.3m-.733 9.685q1.919 0 2.845.993.871.976.872 2.31 0 1.374-.994 2.35t-3.272.977h-5.151v-6.63zm25.733.127a7.8 7.8 0 0 0 1.906-1.816 7.2 7.2 0 0 0 1.126-2.287 9.2 9.2 0 0 0 .363-2.56q0-1.706-.563-3.14a6.6 6.6 0 0 0-1.688-2.47q-1.126-1.035-2.814-1.615t-3.903-.581H53.81v25.415h4.812v-9.44h2.683l6.636 9.44h5.64l-6.862-9.766q1.524-.4 2.668-1.18M63.85 6.746q.453.002.86.054 1.288.193 2.099.922 1.103.996 1.103 2.44-.001.724-.236 1.411a3.2 3.2 0 0 1-.74 1.211q-.508.525-1.302.85-.796.325-1.917.325h-5.095V6.747zM75.28 2.39v.007c-.049-.001-.097-.007-.145-.007h-.041v25.415H80V2.39z"/></g><defs><clipPath id="clip0_3802_11089"><path fill="#fff" d="M0 0h80v30.279H0z"/></clipPath></defs></svg>',
          ],
          [
              'name' => 'BSI',
              'svg' => '<svg fill="none" viewBox="0 0 80 49" style="height: 18px; width: auto; opacity: 0.95;"><g clip-path="url(#clip0_3802_10552)"><path fill="#00A39D" d="M13.656 42.307q1.588 0 2.584-.407 1.02-.407 1.589-1.055t.782-1.462a6.5 6.5 0 0 0 .213-1.654 5.3 5.3 0 0 0-.26-1.725 2.73 2.73 0 0 0-.854-1.295q-.593-.527-1.588-.815t-2.514-.287H8.014v8.7zM8.013 19.561v8.63h4.173q1.327 0 2.395-.193 1.066-.192 1.801-.67a2.97 2.97 0 0 0 1.138-1.319q.404-.839.403-2.11 0-1.245-.308-2.06-.307-.84-.948-1.343-.64-.503-1.636-.719-.973-.215-2.323-.216zm4.695-6.063q3.579 0 6.093.67 2.512.672 4.101 1.894 1.59 1.223 2.3 2.972.735 1.75.735 3.907 0 1.175-.332 2.277a6.7 6.7 0 0 1-1.043 2.037q-.712.936-1.826 1.726-1.114.791-2.679 1.366 3.414.84 5.05 2.709t1.636 4.841q0 2.229-.854 4.147-.853 1.918-2.513 3.355-1.636 1.414-4.054 2.23-2.418.79-5.524.79H0V13.498zm38.651 7.122q-.355.574-.759.862-.38.287-.995.287-.546 0-1.186-.335-.616-.36-1.422-.79a10.6 10.6 0 0 0-1.802-.766q-1.02-.36-2.324-.36-2.251 0-3.366.982-1.09.959-1.09 2.61 0 1.053.663 1.748.665.693 1.73 1.197 1.092.503 2.466.933 1.4.408 2.845.934 1.447.502 2.822 1.197a9.2 9.2 0 0 1 2.465 1.772q1.09 1.077 1.755 2.633.663 1.533.663 3.711 0 2.418-.83 4.525a10.6 10.6 0 0 1-2.417 3.687q-1.566 1.556-3.889 2.466-2.3.886-5.24.886-1.61 0-3.295-.336a20 20 0 0 1-3.224-.933 19.5 19.5 0 0 1-2.94-1.46 12.5 12.5 0 0 1-2.394-1.868l2.37-3.783q.285-.407.735-.67a1.93 1.93 0 0 1 1.02-.288q.711 0 1.422.455.736.456 1.636 1.006.925.55 2.11 1.005 1.185.456 2.798.455 2.181 0 3.39-.957 1.21-.982 1.21-3.089 0-1.22-.665-1.987-.663-.765-1.754-1.269a15.5 15.5 0 0 0-2.442-.885 89 89 0 0 1-2.821-.838 23 23 0 0 1-2.821-1.15 9.4 9.4 0 0 1-2.466-1.795q-1.067-1.125-1.73-2.777-.665-1.677-.665-4.118 0-1.964.783-3.83a9.9 9.9 0 0 1 2.3-3.328q1.517-1.46 3.722-2.323 2.205-.885 5.05-.885 1.588 0 3.082.263 1.517.24 2.868.742 1.352.48 2.513 1.173 1.185.67 2.11 1.532z"/><path fill="#F8AD3C" fill-rule="evenodd" d="M80 12.053c-6.153-.6-8.316 2.789-9.523 4.464-1.54-6.699-4.143-6.17-6.928-7.858C67.575 6.322 68.7 2.69 68.59 0c3.943 2.89 6.692 2.58 10.097 1.643C76.992 6.7 78.06 8.125 80 12.053" clip-rule="evenodd"/><path fill="#00A39D" d="M67.132 48.434h-8.333V12.722c5.214.706 8.34 5.02 8.333 9.584z"/></g><defs><clipPath id="clip0_3802_10552"><path fill="#fff" d="M0 0h80v48.798H0z"/></clipPath></defs></svg>',
          ],
          [
              'name' => 'Visa',
              'svg' => '<svg fill="none" viewBox="0 0 80 26" style="height: 18px; width: auto; opacity: 0.95;"><g clip-path="url(#clip0_3802_27891)"><path fill="#1434CB" d="M52.055 0C46.38 0 41.309 2.941 41.309 8.375c0 6.233 8.994 6.663 8.994 9.794 0 1.318-1.51 2.498-4.091 2.498-3.662 0-6.399-1.649-6.399-1.649l-1.17 5.484s3.152 1.393 7.338 1.393c6.204 0 11.086-3.086 11.086-8.613 0-6.585-9.031-7.003-9.031-9.909 0-1.033 1.24-2.164 3.813-2.164 2.903 0 5.271 1.2 5.271 1.2l1.146-5.297S55.69 0 52.055 0M.137.4 0 1.2s2.387.436 4.538 1.308c2.768 1 2.965 1.58 3.432 3.388l5.08 19.587h6.811L30.354.4H23.56l-6.742 17.054-2.752-14.456C13.813 1.343 12.535.4 10.971.4zm32.95 0-5.331 25.083h6.48L39.548.4zm36.14 0c-1.562 0-2.39.836-2.998 2.298l-9.493 22.785h6.795l1.315-3.798h8.279l.8 3.798h5.995L74.69.4zm.884 6.776 2.014 9.413H66.73z"/></g><defs><clipPath id="clip0_3802_27891"><path fill="#fff" d="M0 0h79.92v25.895H0z"/></clipPath></defs></svg>',
          ],
          [
              'name' => 'Mastercard',
              'svg' => '<svg fill="none" viewBox="0 0 80 50" style="height: 18px; width: auto; opacity: 0.95;"><g clip-path="url(#clip0_3802_26327)"><path fill="#FF5F00" d="M50.815 5.288h-21.63v38.867h21.63z"/><path fill="#EB001B" d="M30.558 24.721c0-7.897 3.708-14.901 9.408-19.433A24.57 24.57 0 0 0 24.72 0C11.056 0 0 11.056 0 24.721s11.056 24.721 24.721 24.721a24.57 24.57 0 0 0 15.245-5.287c-5.7-4.464-9.408-11.537-9.408-19.434"/><path fill="#F79E1B" d="M80 24.721c0 13.665-11.056 24.721-24.721 24.721a24.57 24.57 0 0 1-15.245-5.287c5.769-4.533 9.408-11.537 9.408-19.434S45.734 9.82 40.034 5.288A24.57 24.57 0 0 1 55.28 0C68.944 0 80 11.125 80 24.721"/></g><defs><clipPath id="clip0_3802_26327"><path fill="#fff" d="M0 0h80v49.442H0z"/></clipPath></defs></svg>',
          ],
      ];
      $footerLinks1 = [
          'Pengiriman' => '/pages/shipping-policy',
          'Retur & Penukaran' => '/pages/return-exchange',
          'Pertanyaan Umum' => '/pages/faq',
      ];
      $footerLinks2 = [
          'Panduan Pemeliharaan Produk' => '/pages/product-care',
          'Kebijakan Privasi' => '/pages/privacy-policy',
          'Syarat & Ketentuan' => '/pages/terms-conditions',
      ];
      $containerClass = 'mx-auto w-[min(1184px,calc(100vw-32px))] max-lg:w-[calc(100vw-28px)]';
      $arrowIcon = '<svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-[18px] w-[18px] stroke-[1.8]"><path d="M5 12h14M13 6l6 6-6 6" /></svg>';
    @endphp
    @include('components.site-header', ['transparent' => true, 'kategoris' => $kategoris])
    <main>
      <section class="relative z-[1] -mt-[86px] overflow-hidden bg-[var(--warm)] max-lg:-mt-[72px]" aria-label="Featured collections">
        <div class="hero-frame relative w-full" style="aspect-ratio: 1672 / 941;">
          {{-- Gradient overlay for header readability on mobile only --}}
          <div class="absolute inset-0 pointer-events-none z-10 hero-gradient-overlay"></div>
          <div id="hero-track" class="absolute inset-0 flex transition-transform duration-[450ms] ease-out cursor-grab active:cursor-grabbing select-none">
            @foreach ($heroImages as $index => $image)
              <div class="hero-slide relative h-full min-w-full shrink-0 grow-0 basis-full">
                <picture>
                  <source srcset="{{ $image['mobile'] }}" media="(max-width: 767px)" type="image/webp" />
                  <source srcset="{{ $image['desktop'] }}" type="image/webp" />
                  <img
                    src="{{ $image['fallback'] }}"
                    alt=""
                    loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
                    fetchpriority="{{ $index === 0 ? 'high' : 'auto' }}"
                    decoding="async"
                    width="1672"
                    height="941"
                    class="hero-slide-image absolute inset-0 h-full w-full pointer-events-none"
                    draggable="false"
                  />
                </picture>
                {{-- Aesthetic overlay untuk Banner Auraquina (slide pertama): hapus logo sparkle + warm cinematic tone --}}
                @if ($index === 0)
                  {{-- Warm cinematic vignette overlay --}}
                  <div class="absolute inset-0 pointer-events-none" style="background: radial-gradient(ellipse at center, transparent 40%, rgba(60,35,15,0.18) 100%);"></div>
                  {{-- Cover logo sparkle pojok kanan bawah dengan gradient floor-tone --}}
                  <div class="absolute bottom-0 right-0 pointer-events-none" style="width: 18%; height: 12%; background: linear-gradient(135deg, transparent 0%, rgba(195,175,155,0.92) 50%, rgba(195,175,155,1) 100%);"></div>
                  {{-- Subtle warm tone film overlay --}}
                  <div class="absolute inset-0 pointer-events-none" style="background: linear-gradient(180deg, rgba(210,170,120,0.06) 0%, transparent 40%, rgba(130,90,55,0.10) 100%);"></div>
                @endif
                {{-- Text Overlay --}}
                <div class="absolute inset-0 bg-gradient-to-t from-[rgba(26,20,18,0.7)] via-[rgba(26,20,18,0.15)] to-transparent pointer-events-none z-10 lg:hidden"></div>
                <div class="absolute bottom-14 left-6 right-6 z-20 text-[var(--white)] max-sm:bottom-10 max-sm:left-5 max-sm:right-5 lg:hidden">
                  <p class="mb-1.5 text-[10px] tracking-[0.25em] uppercase text-[rgba(255,255,255,0.85)] font-bold">{{ $image['tag'] }}</p>
                  <h2 class="text-[32px] font-medium leading-[1.15] tracking-[-0.02em] font-serif text-[var(--white)] max-sm:text-[26px]">{{ $image['title'] }}</h2>
                  <p class="mt-2 text-[13px] leading-[1.6] text-[rgba(255,255,255,0.8)] font-light max-w-[420px] max-sm:text-[12px]">{{ $image['desc'] }}</p>
                  <a href="/shop" class="mt-4 inline-flex items-center gap-2 border-b border-[var(--white)] pb-0.5 text-[12px] font-bold tracking-wide uppercase text-[var(--white)] hover:text-[var(--gold-beige)] hover:border-[var(--gold-beige)] transition duration-200">
                    Shop Now
                    <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-3.5 w-3.5 stroke-[2]"><path d="M5 12h14M13 6l6 6-6 6" /></svg>
                  </a>
                </div>
              </div>
            @endforeach
          </div>
        </div>
        {{-- Hero dots pagination --}}
        <div id="hero-dots" class="hero-dots">
          @foreach ($heroImages as $i => $image)
            <button type="button" class="hero-dot {{ $i === 0 ? 'is-active' : '' }}" data-hero-dot="{{ $i }}" aria-label="Go to slide {{ $i + 1 }}"></button>
          @endforeach
        </div>
      </section>
      <div class="bg-[var(--warm)] text-[var(--ink)]">
        {{-- New Collection — horizontal scroll carousel --}}
        <section aria-labelledby="new-collection-title" class="{{ $containerClass }} py-16 pb-14 max-lg:py-12 max-sm:py-10">
          <div class="mb-10 text-center max-lg:mb-8 max-sm:mb-7">
            <h2 id="new-collection-title" class="text-[13px] leading-5 font-bold tracking-[0.18em] uppercase text-[var(--ink)]" style="font-family:'Lato',sans-serif">Koleksi Terbaru: Auraquina</h2>
          </div>
          <div class="product-carousel-wrapper">
            <button id="product-carousel-prev" type="button" aria-label="Previous" class="carousel-arrow carousel-arrow--prev">
              <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-[18px] w-[18px] stroke-[1.8]"><path d="M19 12H5M12 5l-7 7 7 7" /></svg>
            </button>
            <button id="product-carousel-next" type="button" aria-label="Next" class="carousel-arrow carousel-arrow--next">
              <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-[18px] w-[18px] stroke-[1.8]"><path d="M5 12h14M12 5l7 7-7 7" /></svg>
            </button>
            <div id="product-carousel-track" class="product-carousel-track">
              @foreach ($homeProductCards as $product)
                <a href="{{ $product['href'] }}" class="product-carousel-item clean-product-card">
                  <span class="clean-product-card__image">
                    <img src="{{ $product['img'] }}" @if (! empty($product['srcset'])) srcset="{{ $product['srcset'] }}" sizes="(max-width: 640px) 72vw, (max-width: 1024px) 36vw, 280px" @endif alt="{{ $product['name'] }}" loading="{{ $loop->index < 4 ? 'eager' : 'lazy' }}" />
                    @if ($product['has_discount'])
                      <span class="sale-badge">Hemat {{ $product['discount_percent'] }}%</span>
                    @else
                      <span class="clean-product-card__badge">{{ $product['badge'] }}</span>
                    @endif
                  </span>
                  <span class="clean-product-card__info">
                    <p class="clean-product-card__name">{{ $product['name'] }}</p>
                    @if ($product['has_discount'])
                      <span class="sale-price">
                        <span class="sale-price__current">{{ $product['price'] }}</span>
                        <del class="sale-price__compare">{{ $product['price_coret'] }}</del>
                      </span>
                    @else
                      <p class="clean-product-card__price">{{ $product['price'] }}</p>
                    @endif
                  </span>
                </a>
              @endforeach
            </div>
          </div>
        </section>

        <section class="bg-[var(--cream)] py-24 max-sm:py-16 relative overflow-hidden" aria-label="Brand philosophy">
          <div class="absolute inset-0 pointer-events-none select-none flex items-center justify-center" style="opacity: 0.03;">
            <span class="font-serif font-bold text-[var(--brown)]" style="font-size: clamp(100px, 15vw, 220px); line-height: 1;">AQ</span>
          </div>
          <div class="{{ $containerClass }} text-center relative z-10 py-4">
            <div class="inline-flex flex-col items-center justify-center">
              <h2 class="block text-[var(--brown)] mb-2 select-none" style="font-family: 'Great Vibes', 'Alex Brush', cursive; font-size: clamp(52px, 8vw, 76px); font-weight: 400; line-height: 1.1; letter-spacing: normal;">
                Daily Wear
              </h2>
              <p class="block text-[var(--brown)] tracking-[0.25em] font-medium uppercase select-none" style="font-family: 'Lato', sans-serif; font-size: clamp(10px, 1.8vw, 13px); line-height: 1.5; opacity: 0.9;">
                Anggun Dalam Sederhana
              </p>
            </div>
          </div>
        </section>

        {{-- Best Seller / Customer Picks --}}
        <section aria-labelledby="bestseller-title" class="{{ $containerClass }} py-14 max-sm:py-8">
          <div class="mb-7 flex items-center justify-between gap-5 max-sm:mb-5 max-sm:flex-col max-sm:items-start">
            <div>
              <p class="mb-1.5 text-[11px] font-bold tracking-[0.18em] uppercase text-[var(--brown)]">Paling Disukai</p>
              <h2 id="bestseller-title" class="text-[28px] leading-[1.15] font-medium text-[var(--ink)] max-sm:text-[24px]" style="font-family:'Cormorant Garamond',Georgia,serif">Pilihan Pelanggan</h2>
            </div>
            <a class="inline-flex items-center gap-7 whitespace-nowrap text-[14px] leading-5 font-bold text-[var(--brown)]" href="/shop">Lihat Semua {!! $arrowIcon !!}</a>
          </div>
          <div class="grid grid-cols-4 gap-5 max-lg:grid-cols-2 max-lg:gap-[14px] max-sm:grid-cols-2 max-sm:auto-rows-fr max-sm:gap-4">
            @foreach ($bestSellers as $index => $item)
              <a class="group flex h-full flex-col overflow-hidden bg-[var(--white)] text-[var(--ink)]" href="{{ $item['href'] }}">
                <span class="relative block aspect-[4/5] overflow-hidden rounded-[4px]" style="background:#f5f5f5">
                  <img src="{{ $item['img'] }}" @if (! empty($item['srcset'])) srcset="{{ $item['srcset'] }}" sizes="(max-width: 640px) 50vw, (max-width: 1024px) 50vw, 25vw" @endif alt="{{ $item['name'] }}" loading="lazy" class="h-full w-full object-cover object-top transition-transform duration-300 ease-out group-hover:scale-[1.03]" />
                  @if ($item['has_discount'])
                    <span class="sale-badge">Hemat {{ $item['discount_percent'] }}%</span>
                  @else
                    <span class="clean-product-card__badge">{{ $item['badge'] }}</span>
                  @endif
                </span>
                <span class="pt-3">
                  <p class="clean-product-card__name max-sm:text-[13px]">{{ $item['name'] }}</p>
                  @if ($item['has_discount'])
                    <span class="sale-price">
                      <span class="sale-price__current">{{ $item['price'] }}</span>
                      <del class="sale-price__compare">{{ $item['price_coret'] }}</del>
                    </span>
                  @else
                    <p class="clean-product-card__price max-sm:text-[13px]">{{ $item['price'] }}</p>
                  @endif
                </span>
              </a>
            @endforeach
          </div>
        </section>

        {{-- Instagram Feed Section --}}
        <section aria-labelledby="instagram-title" class="border-t border-[var(--border)] bg-[var(--warm)] pt-12 pb-0 max-sm:pt-8 max-sm:pb-0">
          <div class="{{ $containerClass }} mb-7 flex items-center justify-between gap-5 max-sm:mb-5 max-sm:flex-col max-sm:items-start">
            <div>
              <p class="mb-1.5 text-[11px] font-bold tracking-[0.18em] uppercase text-[var(--brown)]">Komunitas</p>
              <h2 id="instagram-title" class="text-[28px] leading-[1.15] font-medium text-[var(--ink)] max-sm:text-[24px]" style="font-family:'Cormorant Garamond',Georgia,serif">Ikuti Kami di Instagram</h2>
              <p class="mt-1.5 text-[14px] leading-5 text-[var(--muted)] max-sm:text-[13px]">Lihat bagaimana komunitas kami memadukan gaya Auraquina</p>
            </div>
            <a class="inline-flex items-center gap-3 text-[14px] font-bold text-[var(--brown)] max-sm:text-[13px]" href="https://www.instagram.com/auraquina/" target="_blank" rel="noopener">
              <svg aria-hidden="true" viewBox="0 0 24 24" class="h-[18px] w-[18px] fill-none stroke-[var(--brown)] stroke-[1.8]"><rect x="4" y="4" width="16" height="16" rx="4" /><circle cx="12" cy="12" r="3.5" /><path d="M17 7h.01" /></svg>
              @auraquina
            </a>
          </div>
        </section>

        <section class="border-y border-[var(--border)] bg-[var(--cream)] py-[34px] max-sm:py-6" aria-label="Store benefits">
          <div class="{{ $containerClass }} grid grid-cols-3 gap-0 max-sm:grid-cols-1 max-sm:gap-y-5">
            @foreach ($serviceItems as $service)
              <div class="grid grid-cols-[34px_minmax(0,1fr)] items-center gap-3.5 px-6 {{ $loop->first ? 'pl-0' : '' }} {{ $loop->last ? 'pr-0' : 'border-r border-[var(--border)] max-sm:border-r-0' }} max-sm:px-0">
                <svg aria-hidden="true" viewBox="0 0 24 24" class="h-[26px] w-[26px] fill-none stroke-[var(--brown)] stroke-[1.7]">{!! $service['icon'] !!}</svg>
                <span>
                  <strong class="mb-1 block text-[13px] leading-[18px] text-[var(--ink)]">{{ $service['title'] }}</strong>
                  <span class="block text-[12px] leading-[18px] text-[var(--muted)]">{{ $service['desc'] }}</span>
                </span>
              </div>
            @endforeach
          </div>
        </section>
      </div>
      {{-- Newsletter removed --}}
      @include('components.site-footer')
    <a class="fixed right-[22px] bottom-[18px] z-[90] flex h-11 w-11 items-center justify-center rounded-xl bg-[var(--brown)] text-[var(--white)] max-lg:right-3 max-lg:bottom-3" href="https://wa.me/6287711516373" aria-label="WhatsApp">
        <svg aria-hidden="true" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
          <path d="M20.52 3.48A11.93 11.93 0 0 0 12 0C5.37 0 0 5.37 0 12a11.93 11.93 0 0 0 1.64 6.06L0 24l6.16-1.61A11.93 11.93 0 0 0 12 24c6.63 0 12-5.37 12-12 0-3.19-1.25-6.2-3.48-8.52zM12 21.8a9.78 9.78 0 0 1-5-1.37l-.36-.21-3.66.96.98-3.57-.23-.37A9.8 9.8 0 1 1 21.8 12 9.8 9.8 0 0 1 12 21.8zm5.36-7.34c-.29-.15-1.74-.86-2-.96s-.46-.15-.66.15-.76.96-.93 1.16-.34.22-.63.07a8.06 8.06 0 0 1-2.36-1.46 8.86 8.86 0 0 1-1.63-2.04c-.17-.29 0-.45.13-.6s.29-.34.43-.5a2 2 0 0 0 .29-.5.55.55 0 0 0 0-.5c-.07-.15-.66-1.6-.91-2.18s-.48-.5-.66-.5h-.57a1.1 1.1 0 0 0-.8.37 3.36 3.36 0 0 0-1.05 2.5 5.83 5.83 0 0 0 1.22 3.1 13.34 13.34 0 0 0 5.13 4.53c.71.31 1.27.5 1.7.64a4.13 4.13 0 0 0 1.88.12 3.07 3.07 0 0 0 2-1.42 2.5 2.5 0 0 0 .17-1.42c-.07-.12-.27-.2-.56-.34z" />
        </svg>
      </a>

  </body>
</html>
