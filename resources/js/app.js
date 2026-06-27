const heroTrack = document.getElementById('hero-track');
const heroSlides = heroTrack ? Array.from(heroTrack.children) : [];
const heroDots = Array.from(document.querySelectorAll('[data-hero-dot]'));

let slide = 0;
let heroCarouselComplete = false;
let heroTimer;

function updateHeroDots(index) {
  heroDots.forEach((dot, i) => {
    dot.classList.toggle('is-active', i === index);
  });
}

function setSlide(next) {
  if (!heroTrack || heroSlides.length === 0) {
    return;
  }

  slide = Math.max(0, Math.min(next, heroSlides.length - 1));
  heroTrack.style.transform = `translateX(-${slide * 100}%)`;
  heroSlides.forEach((heroSlide, index) => heroSlide.classList.toggle('active', index === slide));
  updateHeroDots(slide);

  if (slide === heroSlides.length - 1) {
    heroCarouselComplete = true;
    window.clearInterval(heroTimer);
    syncHeader();
  }
}

setSlide(slide);

if (heroSlides.length > 1) {
  heroTimer = window.setInterval(() => setSlide(slide + 1), 5600);
}

// Hero dots click navigation
heroDots.forEach((dot) => {
  dot.addEventListener('click', () => {
    const index = parseInt(dot.dataset.heroDot, 10);
    if (!isNaN(index)) {
      setSlide(index);
    }
  });
});

const appbar = document.getElementById('appbar');
const navRow = document.getElementById('nav-row');
const heroSection = heroTrack?.closest('section');

function syncHeader() {
  if (!appbar || !navRow) {
    return;
  }

  const heroBottom = heroSection
    ? heroSection.offsetTop + heroSection.offsetHeight
    : 20;
  const isSolid = heroCarouselComplete || window.scrollY >= heroBottom - navRow.offsetHeight;

  appbar.classList.toggle('is-solid', isSolid);
  navRow.classList.toggle('bg-[rgba(255,254,252,0.94)]', isSolid);
  navRow.classList.toggle('shadow-[0_1px_0_var(--border)]', isSolid);
  navRow.classList.toggle('backdrop-blur-[10px]', isSolid);
}

syncHeader();
window.addEventListener('scroll', syncHeader, { passive: true });

// --- Product Carousel (horizontal scroll with arrows) ---
function initProductCarousel() {
  const track = document.getElementById('product-carousel-track');
  const prevBtn = document.getElementById('product-carousel-prev');
  const nextBtn = document.getElementById('product-carousel-next');

  if (!track || !prevBtn || !nextBtn) return;

  const scrollAmount = () => {
    const item = track.querySelector('.product-carousel-item');
    if (!item) return 300;
    const gap = parseInt(getComputedStyle(track).gap) || 20;
    return item.offsetWidth + gap;
  };

  function updateArrows() {
    const maxScroll = track.scrollWidth - track.clientWidth;
    prevBtn.disabled = track.scrollLeft <= 4;
    nextBtn.disabled = track.scrollLeft >= maxScroll - 4;
  }

  prevBtn.addEventListener('click', () => {
    track.scrollBy({ left: -scrollAmount(), behavior: 'smooth' });
  });

  nextBtn.addEventListener('click', () => {
    track.scrollBy({ left: scrollAmount(), behavior: 'smooth' });
  });

  track.addEventListener('scroll', updateArrows, { passive: true });
  updateArrows();
}

initProductCarousel();

// --- Lookbook Carousel (mobile only) ---
function initCarousel(trackId, dotsId, dotClass) {
  const track = document.getElementById(trackId);
  const dotsContainer = document.getElementById(dotsId);
  if (!track || !dotsContainer) return;

  const cards = Array.from(track.children).filter((el) => el.tagName === 'A');
  const dots = Array.from(dotsContainer.querySelectorAll(`.${dotClass}`));

  function updateDots(activeIndex) {
    dots.forEach((dot, i) => {
      if (i === activeIndex) {
        dot.classList.add('bg-[var(--brown)]', 'w-5');
        dot.classList.remove('bg-[var(--sand)]', 'w-2');
      } else {
        dot.classList.remove('bg-[var(--brown)]', 'w-5');
        dot.classList.add('bg-[var(--sand)]', 'w-2');
      }
    });
  }

  dots.forEach((dot, i) => {
    dot.addEventListener('click', () => {
      if (cards[i]) {
        cards[i].scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
      }
    });
  });

  let scrollTimeout;
  track.addEventListener(
    'scroll',
    () => {
      clearTimeout(scrollTimeout);
      scrollTimeout = setTimeout(() => {
        const trackRect = track.getBoundingClientRect();
        const trackCenter = trackRect.left + trackRect.width / 2;

        let closestIndex = 0;
        let closestDist = Infinity;

        cards.forEach((card, i) => {
          const cardRect = card.getBoundingClientRect();
          const cardCenter = cardRect.left + cardRect.width / 2;
          const dist = Math.abs(cardCenter - trackCenter);
          if (dist < closestDist) {
            closestDist = dist;
            closestIndex = i;
          }
        });

        updateDots(closestIndex);
      }, 60);
    },
    { passive: true }
  );
}

initCarousel('lookbook-carousel-track', 'lookbook-dots', 'lookbook-dot');

function initSearchOverlay() {
  const overlay = document.getElementById('search-overlay');
  const input = document.getElementById('search-input');
  const results = overlay?.querySelector('[data-search-results]');

  if (!overlay) return;

  const closeButtons = overlay.querySelectorAll('[data-search-close]');
  const triggers = document.querySelectorAll('[data-search-trigger]');
  let searchTimer;
  let searchRequest;
  let activeResultIndex = -1;

  function setResults(html) {
    if (!results) return;
    results.innerHTML = html;
    results.classList.toggle('hidden', html === '');
    activeResultIndex = -1;
  }

  function renderState(message) {
    setResults(`<div class="px-4 py-3 text-[13px] text-[var(--muted)]">${message}</div>`);
  }

  function renderResults(items) {
    if (!items.length) {
      renderState('Produk tidak ditemukan. Coba kata kunci lain.');
      return;
    }

    setResults(items.map((item, index) => `
      <a href="${item.url}" data-search-result-item data-index="${index}" class="flex items-center gap-3 border-b border-[var(--border)] px-3 py-2.5 last:border-b-0 hover:bg-[var(--cream)] focus:bg-[var(--cream)] focus:outline-none" role="option">
        <span class="h-12 w-12 shrink-0 overflow-hidden rounded-[4px] bg-[var(--sand)]">
          ${item.gambar ? `<img src="${item.gambar}" alt="" class="h-full w-full object-cover" />` : ''}
        </span>
        <span class="min-w-0 flex-1">
          <span class="block truncate text-[13px] font-bold text-[var(--ink)]">${item.nama}</span>
          <span class="block truncate text-[11px] text-[var(--muted)]">${item.kategori || 'Produk'} · ${item.harga}</span>
        </span>
      </a>
    `).join(''));
  }

  function searchProducts(term) {
    const query = term.trim();

    window.clearTimeout(searchTimer);
    searchRequest?.abort();

    if (query.length < 2) {
      setResults('');
      return;
    }

    searchTimer = window.setTimeout(() => {
      searchRequest = new AbortController();
      renderState('Mencari...');

      fetch(`/api/search?q=${encodeURIComponent(query)}`, {
        headers: { Accept: 'application/json' },
        signal: searchRequest.signal,
      })
        .then((response) => response.ok ? response.json() : Promise.reject())
        .then((data) => renderResults(Array.isArray(data.items) ? data.items : []))
        .catch((error) => {
          if (error.name !== 'AbortError') renderState('Pencarian gagal. Coba lagi.');
        });
    }, 250);
  }

  function moveActiveResult(direction) {
    const items = [...(results?.querySelectorAll('[data-search-result-item]') || [])];
    if (!items.length) return;

    activeResultIndex = (activeResultIndex + direction + items.length) % items.length;
    items.forEach((item, index) => item.classList.toggle('bg-[var(--cream)]', index === activeResultIndex));
    items[activeResultIndex].focus();
  }

  function openOverlay() {
    overlay.classList.remove('hidden', 'pointer-events-none');
    overlay.classList.add('pointer-events-auto');
    requestAnimationFrame(() => overlay.classList.add('opacity-100'));
    document.body.style.overflow = 'hidden';
    window.setTimeout(() => input?.focus(), 60);
    searchProducts(input?.value || '');
  }

  function closeOverlay() {
    overlay.classList.remove('opacity-100');
    overlay.classList.add('pointer-events-none');
    document.body.style.overflow = '';
    searchRequest?.abort();
    window.clearTimeout(searchTimer);
    setResults('');
    window.setTimeout(() => overlay.classList.add('hidden'), 200);
  }

  input?.addEventListener('input', () => searchProducts(input.value));
  input?.addEventListener('keydown', (event) => {
    if (event.key === 'ArrowDown') {
      event.preventDefault();
      moveActiveResult(1);
    } else if (event.key === 'ArrowUp') {
      event.preventDefault();
      moveActiveResult(-1);
    }
  });

  triggers.forEach((trigger) => {
    trigger.addEventListener('click', (event) => {
      event.preventDefault();
      openOverlay();
    });
  });

  closeButtons.forEach((button) => button.addEventListener('click', closeOverlay));

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && !overlay.classList.contains('hidden')) {
      closeOverlay();
    }
  });
}

function formatBadgeCount(count) {
  return count > 99 ? '99+' : String(count);
}

function syncCountBadges(selector, count) {
  document.querySelectorAll(selector).forEach((badge) => {
    badge.textContent = formatBadgeCount(count);
    badge.classList.toggle('is-hidden', count === 0);
  });
}

function syncCartBadges(count) {
  syncCountBadges('[data-cart-count-badge]', Number(count) || 0);
}

async function refreshCartBadges() {
  if (!document.querySelector('[data-cart-count-badge]')) {
    return;
  }

  const isAuth = document.querySelector('[data-cart-count-badge]')?.dataset.auth === 'true';

  try {
    const response = await fetch('/keranjang/jumlah', { headers: { Accept: 'application/json' } });
    const data = await response.json();
    syncCartBadges(data.total_item);
  } catch {
    syncCartBadges(0);
  }
}

window.addEventListener('cart:changed', (event) => syncCartBadges(event.detail?.totalItem));

refreshCartBadges();

initSearchOverlay();
