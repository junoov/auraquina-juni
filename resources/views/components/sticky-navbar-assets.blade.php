<style>
/* --- Body Overflow Fix for Sticky positioning --- */
body {
  overflow-x: clip !important;
}

/* --- Premium Sticky Header System --- */
header.sticky {
  position: sticky !important;
  top: 0 !important;
  z-index: 1000 !important;
  width: 100%;
}

/* Base header transitions */
header.sticky > div,
header.sticky #nav-row {
  transition: background-color 0.4s cubic-bezier(0.16, 1, 0.3, 1),
              box-shadow 0.4s cubic-bezier(0.16, 1, 0.3, 1),
              backdrop-filter 0.4s cubic-bezier(0.16, 1, 0.3, 1),
              border-color 0.4s cubic-bezier(0.16, 1, 0.3, 1),
              height 0.4s cubic-bezier(0.16, 1, 0.3, 1) !important;
}

/* Default state for non-transparent header */
header.sticky > div {
  background-color: rgba(247, 242, 233, 0.94) !important; /* var(--soft-cream) at 0.94 opacity */
  backdrop-filter: blur(10px) !important;
  -webkit-backdrop-filter: blur(10px) !important;
  border-bottom: 1px solid var(--border) !important;
}

/* Homepage Transparent Header initial state */
header.header-transparent > div,
header.header-transparent #nav-row {
  background-color: transparent !important;
  backdrop-filter: none !important;
  -webkit-backdrop-filter: none !important;
  box-shadow: none !important;
  border-bottom: 1px solid transparent !important;
}

/* Scrolled state for all sticky headers */
header.is-scrolled > div,
header.is-scrolled #nav-row {
  background-color: rgba(255, 255, 255, 0.95) !important;
  backdrop-filter: blur(12px) !important;
  -webkit-backdrop-filter: blur(12px) !important;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08) !important;
  border-bottom: 1px solid rgba(211, 192, 172, 0.15) !important;
}

/* Smooth shrink height on scroll to look premium */
@media (min-width: 1024px) {
  header.sticky > div,
  header.sticky #nav-row {
    height: 86px;
  }
  header.is-scrolled > div,
  header.is-scrolled #nav-row {
    height: 70px !important;
  }
}
@media (max-width: 1023px) {
  header.sticky > div,
  header.sticky #nav-row {
    height: 72px;
  }
  header.is-scrolled > div,
  header.is-scrolled #nav-row {
    height: 60px !important;
  }
}
</style>

<script>
// --- Sticky Header Scroll Handler ---
(function() {
  function initStickyHeader() {
    const headers = document.querySelectorAll('header.sticky');
    if (headers.length === 0) return;

    function handleScroll() {
      const isScrolled = window.scrollY > 40;
      headers.forEach((header) => {
        header.classList.toggle('is-scrolled', isScrolled);
      });
    }

    window.addEventListener('scroll', handleScroll, { passive: true });
    handleScroll(); // Initial check
  }

  // Run on DOM load or immediately if ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initStickyHeader);
  } else {
    initStickyHeader();
  }
})();
</script>
