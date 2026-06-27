# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

**Initial setup:**
```bash
composer run setup
```

**Development (runs Laravel server, queue, logs, and Vite concurrently):**
```bash
composer run dev
```

**Run tests:**
```bash
composer run test
```

**Frontend build only:**
```bash
npm run build
npm run dev
```

## Architecture

Auraquina is a Laravel 13.8 + Vite + Tailwind CSS 4.0 e-commerce frontend for a modest fashion brand (abayas and khimars). The backend is minimal — routes just render Blade views with hardcoded product data; no database queries for products yet.

**Stack:** PHP 8.3+, Laravel 13.8, Vite 8.0, Tailwind CSS 4.0 (via `@tailwindcss/vite`), vanilla JS, SQLite (default).

**Routes** (`routes/web.php`): 4 routes — home, shop index, product detail, and a redirect. All render Blade views directly.

**Views** (`resources/views/`): 4 Blade templates — `index.blade.php` (homepage), `shop.blade.php`, `product-detail.blade.php`, `welcome.blade.php`.

**Frontend JS** (`resources/js/app.js`): Vanilla JS handling hero carousel (5.6s auto-rotate), mobile menu toggle, product grid pagination (2 items/page on mobile), lookbook carousel with dot navigation, and header scroll effect.

**CSS** (`resources/css/app.css`): Tailwind imports plus custom CSS variables for the brand palette (`--cream`, `--sand`, `--brown`, `--ink`, etc.) and animations.

**Testing:** PHPUnit with in-memory SQLite (`phpunit.xml`). Tests live in `tests/Feature/` and `tests/Unit/`.

**No models for products** — product data is currently hardcoded in Blade templates. The `User` model and auth scaffolding exist but are unused in the current views.
