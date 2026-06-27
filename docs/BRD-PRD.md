# AURAQUINA — Business & Product Requirements Document

| **Atribut** | **Detail** |
|---|---|
| **Nama Project** | Auraquina E-Commerce Platform |
| **Versi Dokumen** | 2.1 (Updated to Current Implementation) |
| **Tanggal** | 09 Juni 2026 |
| **Status** | Draft for Review |
| **Author** | Product & Engineering Team |
| **Approver** | Founder / Business Owner |
| **Klasifikasi** | Internal — Confidential |

> Dokumen ini adalah **single source of truth** untuk kebutuhan bisnis (BRD), produk (PRD), arsitektur teknis, audit kode, dan roadmap Auraquina E-Commerce. Versi 2.1 memperbarui status implementasi aktual berdasarkan kondisi kode per 09 Juni 2026.

---

## DAFTAR ISI

1. [Executive Summary](#1-executive-summary)
2. [Business Requirements (BRD)](#2-business-requirements-brd)
3. [Product Requirements (PRD)](#3-product-requirements-prd)
4. [Non-Functional Requirements](#4-non-functional-requirements)
5. [User Stories & Use Cases](#5-user-stories--use-cases)
6. [Data Model & Database Design](#6-data-model--database-design)
7. [Technical Architecture](#7-technical-architecture)
8. [UX, Design System & Sitemap](#8-ux-design-system--sitemap)
9. [Audit Kode & Rekomendasi Teknis](#9-audit-kode--rekomendasi-teknis)
10. [Rekomendasi Bisnis](#10-rekomendasi-bisnis)
11. [Roadmap 12 Bulan](#11-roadmap-12-bulan)
12. [Estimasi Effort & Tech Stack Production](#12-estimasi-effort--tech-stack-production)
13. [Pre-Launch & Security Checklist](#13-pre-launch--security-checklist)
14. [Open Questions](#14-open-questions)
15. [Appendix](#15-appendix)

---

## 1. EXECUTIVE SUMMARY

### 1.1 Tentang Auraquina
**Auraquina** adalah brand modest fashion premium asal Malang, Jawa Timur, yang menjual abaya, khimar, one-set, dan aksesoris untuk wanita muslimah modern. Brand ini mengusung filosofi *"Designed for women who find beauty in simplicity"* — desain timeless, minimal, elegan dengan material premium dan estetika *quiet luxury*.

### 1.2 Latar Belakang Bisnis
Saat ini kanal penjualan Auraquina mengandalkan marketplace pihak ketiga (Shopee, TikTok Shop, Instagram) yang membatasi kontrol terhadap brand experience, customer data, dan margin.

**Permasalahan utama:**
1. **Brand experience terfragmentasi** di marketplace — sulit menampilkan storytelling, lookbook, dan editorial.
2. **Margin tergerus** komisi marketplace (5–12%) dan biaya iklan platform.
3. **Tidak ada customer database** sendiri — sulit retargeting, loyalty, direct communication.
4. **Inventory tersebar** di multiple channel tanpa single source of truth.
5. **Keterbatasan kanal langsung** untuk customer loyal — bergantung WhatsApp manual.

### 1.3 Solusi
Membangun **website e-commerce official Auraquina** sebagai *direct-to-consumer (D2C) flagship channel* yang:
- Menjadi pusat brand experience dengan storytelling, lookbook, curated collection.
- Mendukung end-to-end transaksi: katalog → cart → checkout → pembayaran → pengiriman → after-sales.
- Mengumpulkan first-party customer data untuk CRM, retargeting, loyalty.
- Terintegrasi dengan logistik (JNE, J&T, SiCepat, AnterAja) dan payment gateway (QRIS, VA, e-Wallet).

### 1.4 Status Implementasi Saat Ini

| Aspek | Status | Catatan |
|---|---|---|
| Frontend UI (homepage, shop, PDP, cart) | ✅ Selesai | Homepage, shop, PDP, cart, wishlist, search overlay, mobile behaviors aktif |
| Database schema produk & varian | ✅ Selesai | `kategoris`, `produks`, `varian_produks`, `gambar_produks`, `gambar_varian_produks` |
| Cart system (guest, session-based) | ✅ Selesai | `item_keranjangs` aktif, JSON API style |
| Checkout (UI + payload session) | ⚠️ Parsial | Checkout flow aktif, voucher apply aktif, tetapi payment & shipping masih placeholder/manual |
| Order module (DB, persistence) | ⚠️ Parsial | `pesanans` dan `item_pesanans` sudah persist, ada status lifecycle dasar, tetapi belum terhubung payment gateway |
| Payment gateway integration | ❌ Belum ada | **CRITICAL** |
| Shipping API integration | ❌ Belum ada | Ongkir hardcoded `Rp 11.500` |
| Admin dashboard | ⚠️ Parsial | Filament admin sudah ada untuk produk, kategori, pesanan, user, role, stok; voucher/promo management belum surfaced |
| Authentication customer | ⚠️ Parsial | Login, register, logout, account area aktif; forgot password, verification, dan account-depth masih fase lanjut |
| Notifikasi email/WA | ❌ Belum ada | Phase 1 wajib |
| SEO meta & structured data | ⚠️ Minimal | Meta dasar sudah ada di banyak halaman, tetapi belum dynamic SEO penuh / JSON-LD / sitemap |
| Test coverage | ⚠️ Dasar tersedia | Sudah ada feature tests order/account, tetapi coverage masih jauh dari target MVP |

### 1.5 Manfaat Bisnis (Business Value)

| **Kategori** | **Manfaat** | **Estimasi Dampak** |
|---|---|---|
| Margin | Eliminasi komisi marketplace untuk transaksi via website | +8–12% gross margin per transaksi |
| Customer Data | Kepemilikan database pelanggan (email, perilaku, preferensi) | 5K–10K customer dalam 12 bulan |
| AOV | Cross-selling, bundling, free-shipping threshold | +15–25% Average Order Value |
| Brand Equity | Kontrol penuh terhadap presentasi visual & narasi | Long-term brand asset |
| Retention | Loyalty program, repurchase notification, wishlist | Repeat-purchase rate +20% |
| Operasional | Single inventory source, automated order processing | Efisiensi waktu admin 40%+ |

### 1.6 Target Market
- **Demografi:** Wanita muslimah, 20–40 tahun.
- **Lokasi:** Indonesia (fokus Jawa, pengiriman nasional).
- **Segmen:** Middle-upper class (range produk Rp 250K–700K).
- **Pendapatan:** Rp 6–15 juta/bulan.
- **Behavior:** Mobile-first (≥70% traffic mobile), aktif di Instagram & TikTok, prefer belanja online.

---

## 2. BUSINESS REQUIREMENTS (BRD)

### 2.1 Vision
> "Menjadi destinasi utama D2C bagi wanita muslimah Indonesia yang mencari modest fashion dengan estetika tenang, timeless, dan effortless."

### 2.2 Mission (Project Scope)
1. Memberikan brand experience website setara/lebih baik dari benchmark (Buttonscarves, Wearing Klamby, Heaven Lights).
2. Memungkinkan transaksi 24/7 dengan checkout yang mulus dan terpercaya.
3. Mengumpulkan & memanfaatkan data pelanggan untuk personalisasi.
4. Memastikan operasional fulfillment terintegrasi dan terukur.

### 2.3 SMART Objectives (12 Bulan Pertama)

| **#** | **Objective** | **KPI / Target** |
|---|---|---|
| O1 | Launch MVP e-commerce | Go-live dalam 8 minggu |
| O2 | Akuisisi customer baru via website | 3.000 customer terdaftar di 12 bulan |
| O3 | Kontribusi revenue website | 20% total revenue brand di bulan ke-12 |
| O4 | Conversion rate | ≥ 1.8% (industri fashion ID 1.0–2.5%) |
| O5 | Average Order Value (AOV) | ≥ Rp 550.000 |
| O6 | Repeat purchase rate | ≥ 25% di bulan ke-12 |
| O7 | NPS / Customer Satisfaction | ≥ 60 (NPS), ≥ 4.5/5 (rating) |
| O8 | Page Speed | LCP < 2.5s di mobile (real users) |

### 2.4 Critical Success Factors (CSF)
1. Kualitas foto produk & konsistensi visual.
2. Inventory management akurat (real-time stock).
3. Kecepatan response customer service (< 2 jam jam kerja).
4. Reliable payment & shipping integration.
5. Mobile-first experience (≥ 70% traffic mobile).

### 2.5 Stakeholder Analysis

#### Internal
| **Stakeholder** | **Peran** | **Kepentingan** | **Pengaruh** |
|---|---|---|---|
| Founder / Owner | Decision maker utama, brand vision | Tinggi — semua aspek | Sangat Tinggi |
| Tim Marketing | Konten, kampanye, social media | Brand consistency, conversion | Tinggi |
| Tim Operasional | Pemenuhan order, packing, return | Workflow efisien & jelas | Tinggi |
| Tim Customer Service | Tangani pertanyaan & komplain | Tools memadai (CRM, ticket) | Sedang |
| Tim Keuangan | Rekonsiliasi pembayaran & pajak | Laporan akurat & on-time | Sedang |
| Developer / Tech | Implementasi & maintenance | Stabilitas, dokumentasi | Tinggi |

#### External
| **Stakeholder** | **Peran** | **Kepentingan** |
|---|---|---|
| End Customer (B2C) | Pembeli akhir | UX mulus, harga kompetitif, produk berkualitas |
| Reseller / Wholesale | Pembeli grosir | Harga khusus, ketersediaan stok |
| Payment Gateway | Midtrans / Xendit / Doku | SLA, compliance |
| Kurir / Logistik | JNE, J&T, SiCepat, AnterAja | Integrasi API, label otomatis |
| Vendor Hosting | Server provider | Uptime SLA |
| Regulator | Kemenkominfo, PPN, PSE | Kepatuhan PSE Privat, UU PDP |

#### RACI Matrix (Singkat)
| **Aktivitas** | **Founder** | **Marketing** | **Operasional** | **Tech** |
|---|---|---|---|---|
| Penentuan harga & promo | A/R | C | I | I |
| Konten produk & lookbook | A | R | C | I |
| Setup payment & shipping | A | C | R | C |
| Order fulfillment | I | I | R/A | I |
| Maintenance website | C | I | I | R/A |

> R=Responsible, A=Accountable, C=Consulted, I=Informed

### 2.6 Business Rules

#### BR-Pricing & Discount
- Harga produk dalam Rupiah (IDR), bilangan bulat (tanpa desimal).
- Diskon maksimal 50% tanpa approval owner.
- Flash sale hanya bisa dijalankan oleh admin/owner.
- Voucher memiliki batas penggunaan dan masa berlaku.
- **Free shipping threshold: Rp 500.000** (final, dapat diubah owner).

#### BR-Inventory & Stock
- Produk dengan stok ≤ 5 ditandai *"Limited Stock / Tersisa N item"*.
- Produk stok 0 ditandai *"Sold Out"* dan tidak bisa di-checkout.
- Pre-order memiliki estimasi waktu pengiriman terpisah.
- Notifikasi restock ke pelanggan yang subscribe (Phase 2).

#### BR-Order & Payment
- Batas waktu pembayaran: **1 jam** sejak order dibuat.
- Order otomatis dibatalkan jika melewati batas waktu.
- Konfirmasi pembayaran otomatis untuk QRIS, VA, e-Wallet.
- Manual confirmation untuk transfer bank manual.
- COD hanya untuk area tertentu (Phase 2 — keputusan tunda).

#### BR-Shipping
- Partner logistik MVP: **JNE, J&T Express, SiCepat**.
- Pengiriman dari Malang, Jawa Timur (single warehouse di MVP).
- Estimasi pengiriman ditampilkan berdasarkan lokasi pembeli.
- Tracking number dikirim via WhatsApp dan email.
- Cut-off pengiriman: order sebelum jam 14:00 dikirim hari itu juga.

#### BR-Return & Exchange
- Pengembalian dalam **7 hari** setelah diterima.
- Produk harus dalam kondisi original (tag masih ada).
- Penukaran ukuran gratis (ongkir ditanggung brand untuk kasus salah size dari pihak Auraquina).
- Refund via transfer bank dalam **3–5 hari kerja**.

### 2.7 Revenue Model

```
Revenue Streams:
├── Product Sales (primary)
│   ├── Abaya (Rp 459.000 – 529.000)
│   ├── Khimar (Rp 289.000 – 389.000)
│   ├── One Set (Rp 399.000 – 599.000)
│   └── Accessories (Rp 89.000 – 199.000)
├── Shipping Fee (passed to customer)
└── Future: Membership / Loyalty Program
```

### 2.8 Business Requirements (MoSCoW)

| **ID** | **Business Requirement** | **Priority** | **Rationale** |
|---|---|---|---|
| BR-01 | Customer dapat membeli tanpa wajib registrasi (guest checkout) | Must | Mengurangi friction, terbukti meningkatkan konversi 20–35% |
| BR-02 | Sistem mendukung varian produk (ukuran × warna) dengan stok per varian | Must | Sifat fashion item — multi-variant standar |
| BR-03 | Sistem mendukung minimum 5 metode pembayaran populer di Indonesia | Must | QRIS, VA, e-Wallet adalah ekspektasi pasar |
| BR-04 | Sistem terintegrasi dengan kurir nasional minimal JNE, J&T, SiCepat | Must | Coverage geografis & ekspektasi customer |
| BR-05 | Sistem mengirim notifikasi otomatis tiap perubahan status order | Must | Mengurangi inquiry CS, meningkatkan trust |
| BR-06 | Customer dapat melacak status order melalui website | Must | Self-service untuk mengurangi beban CS |
| BR-07 | Admin dapat mengelola produk, stok, dan order via dashboard | Must | Operasional harian |
| BR-08 | Sistem menampilkan estetika brand secara konsisten | Must | Brand equity adalah core value |
| BR-09 | Sistem mobile-friendly & optimal di koneksi 3G/4G | Must | ≥70% traffic dari mobile |
| BR-10 | Sistem memenuhi UU PDP (Pelindungan Data Pribadi) | Must | Compliance hukum — wajib |
| BR-11 | Sistem mengumpulkan data first-party untuk marketing | Should | Foundation CRM jangka panjang |
| BR-12 | Sistem mendukung promo (diskon harga, voucher kode) | Should | Tools marketing standar |
| BR-13 | Sistem menampilkan review/rating produk | Should | Social proof meningkatkan konversi |
| BR-14 | Sistem mendukung bundle / cross-sell suggestion | Could | Meningkatkan AOV |
| BR-15 | Sistem memberikan analytics dashboard untuk owner | Should | Data-driven decision making |
| BR-16 | Sistem mendukung pre-order untuk koleksi terbatas | Could | Strategi launch koleksi baru |
| BR-17 | Sistem mendukung multi-admin dengan role berbeda | Should | Pemisahan tanggung jawab |
| BR-18 | Sistem mengirim invoice/struk digital ke customer | Must | Standar transaksi & klaim/pembukuan customer |

### 2.9 Risk Register

| **ID** | **Risiko** | **Likelihood** | **Impact** | **Mitigasi** |
|---|---|---|---|---|
| R-01 | Konversi rendah karena trust belum terbentuk di brand baru | Tinggi | Tinggi | Testimoni, social proof, payment badge, guarantee policy |
| R-02 | Stock mismatch antara website & marketplace | Tinggi | Tinggi | Phase 2: integrasi inventory sync; Phase 1: SOP cut-off harian |
| R-03 | Cart abandonment > 70% | Tinggi | Sedang | Email/WA recovery (Phase 2), optimasi checkout, transparent shipping |
| R-04 | Fraud / chargeback pembayaran | Sedang | Sedang | 3DS untuk kartu, OTP untuk e-wallet, manual review high-value |
| R-05 | Server down saat peak (kampanye/launching) | Sedang | Tinggi | Auto-scaling, CDN, queue, load test pre-launch |
| R-06 | Ketidakpatuhan UU PDP / PSE | Rendah | Sangat Tinggi | Privacy policy, consent, registrasi PSE, DPA dengan vendor |
| R-07 | Foto/copy produk tidak konsisten | Sedang | Sedang | Style guide, template konten, approval flow |
| R-08 | Kurir delay → komplain customer | Tinggi | Sedang | Multiple courier, tracking visibility, SLA dashboard |
| R-09 | Negative review viral | Rendah | Tinggi | SOP CS responsif (< 2 jam), policy refund/exchange jelas |
| R-10 | Vendor lock-in (payment/shipping) | Sedang | Sedang | Service layer abstraction, kontrak fleksibel, multi-provider design |
| R-11 | Developer single point of failure | Tinggi | Tinggi | Dokumentasi lengkap, code review, CI/CD, knowledge transfer |
| R-12 | Stock overselling (race condition) | Sedang | Tinggi | Database-level locking, stock hold on checkout (TTL 15 menit) |
| R-13 | Konten gambar lambat (CDN external) | Sedang | Sedang | Migrasi ke CDN sendiri (Cloudflare/Bunny), image optimization |
| R-14 | Data loss | Rendah | Sangat Tinggi | Automated backup hourly, multi-region, restore drill kuartalan |

### 2.10 Success Criteria (Bulan ke-3 Setelah Launch)
1. Uptime ≥ 99.5%.
2. Minimal 500 transaksi sukses.
3. Conversion rate ≥ 1.5%.
4. AOV ≥ Rp 500.000.
5. Tidak ada incident keamanan critical.
6. Customer satisfaction (post-purchase) ≥ 4.3/5.
7. Tim operasional dapat menangani order via dashboard tanpa intervensi developer.

---

## 3. PRODUCT REQUIREMENTS (PRD)

### 3.1 Target User Persona

#### Persona 1 — "Aisha, The Mindful Shopper"
- **Demografi:** Wanita, 24–32 tahun, urban (Jakarta/Surabaya/Bandung), kantoran/wiraswasta.
- **Pendapatan:** Rp 6–15 juta/bulan.
- **Behavior:** Aktif di IG & TikTok, mengikuti modest fashion premium, beli 2–4× per kuartal, riset sebelum membeli.
- **Pain Point:** Sering kecewa karena produk tidak sesuai foto, ukuran membingungkan, ongkir mahal.
- **Goal:** Pakaian modest yang elegan, awet, mudah dipadukan.
- **Device:** 80% mobile (iOS/Android), 20% desktop.

#### Persona 2 — "Fatma, The Loyal Customer"
- **Demografi:** Wanita, 30–45 tahun, ibu rumah tangga atau profesional senior.
- **Behavior:** Sudah pernah beli Auraquina di Shopee, ingin koleksi lengkap, sering recommend.
- **Pain Point:** Pricing di marketplace sering berbeda, stok varian sering habis.
- **Goal:** Akses prioritas ke koleksi baru, harga setia/loyalty.
- **Device:** 60% mobile, 40% desktop.

#### Persona 3 — "Nadia, The Discovery Browser"
- **Demografi:** Wanita, 19–25 tahun, mahasiswa atau fresh graduate.
- **Behavior:** Browsing tanpa intent, terinspirasi konten lookbook, sensitif harga.
- **Pain Point:** Budget terbatas, takut ditipu brand baru.
- **Goal:** Inspirasi style, lihat-lihat, mungkin beli aksesoris/entry-level.
- **Device:** 95% mobile.

### 3.2 Product Positioning

| **Aspek** | **Auraquina** | **Buttonscarves** | **Heaven Lights** | **Wearing Klamby** |
|---|---|---|---|---|
| Estetika | Quiet, minimal, neutral | Bold, statement, vibrant | Soft, romantic, feminine | Editorial, modern |
| Range Harga | Rp 250K–700K | Rp 300K–1.2M | Rp 200K–500K | Rp 400K–900K |
| Target Age | 20–35 | 22–40 | 18–28 | 25–40 |
| Hero Product | Abaya, Khimar | Scarves | Daily wear set | Outerwear, dress |

### 3.3 Feature Roadmap Overview

```
Priority Legend: P0 = Must Have (MVP), P1 = Should Have, P2 = Nice to Have

┌─────────────────────────────────────────────────────────────────┐
│ PHASE 1 — MVP (Bulan 1–2)                                       │
├─────────────────────────────────────────────────────────────────┤
│ [P0] Product Catalog (database-driven)                           │
│ [P0] Shopping Cart (guest, session-based)                        │
│ [P0] Checkout Flow (single-page)                                 │
│ [P0] Payment Gateway (Midtrans/Xendit Snap)                      │
│ [P0] Order Module & Persistence                                  │
│ [P0] Shipping Integration (Biteship/RajaOngkir)                  │
│ [P0] Admin Dashboard (Filament: produk, order)                   │
│ [P0] Email + WhatsApp Notification                               │
│ [P0] Order Tracking (guest dengan magic link)                    │
│ [P0] SEO Basic (meta, Schema.org Product, sitemap)               │
├─────────────────────────────────────────────────────────────────┤
│ PHASE 2 — Growth (Bulan 3–6)                                     │
├─────────────────────────────────────────────────────────────────┤
│ [P1] User Authentication & Account                               │
│ [P1] Wishlist (server-side, sync antar device)                   │
│ [P1] Product Reviews & Ratings                                   │
│ [P1] Search & Advanced Filtering (Meilisearch)                   │
│ [P1] Voucher & Promo System                                      │
│ [P1] Abandoned-cart recovery (email + WA)                        │
│ [P1] Back-in-stock notification                                  │
│ [P1] Loyalty Points (basic)                                      │
│ [P1] Analytics Dashboard                                         │
├─────────────────────────────────────────────────────────────────┤
│ PHASE 3 — Personalization (Bulan 7–9)                            │
├─────────────────────────────────────────────────────────────────┤
│ [P2] Loyalty Program (tier benefits)                             │
│ [P2] Product Recommendations                                     │
│ [P2] Lookbook Shoppable                                          │
│ [P2] Blog / Editorial CMS                                        │
│ [P2] Multi-language (ID/EN)                                      │
│ [P2] PWA (Add to Home Screen)                                    │
│ [P2] A/B testing framework                                       │
├─────────────────────────────────────────────────────────────────┤
│ PHASE 4 — Scale (Bulan 10–12)                                    │
├─────────────────────────────────────────────────────────────────┤
│ [P2] Mobile native app (evaluate ROI dulu)                       │
│ [P2] B2B / wholesale portal                                      │
│ [P2] Marketplace integration (Shopee/Tokopedia stock sync)       │
│ [P2] Multi-warehouse / dropship                                  │
│ [P2] International shipping (Singapore, Malaysia)                │
└─────────────────────────────────────────────────────────────────┘
```

### 3.4 Functional Requirements (FR)

> Format: `FR-XX | Title | Priority`. Priority: **P0** = wajib MVP, **P1** = fase awal post-launch, **P2** = nice-to-have.

#### Modul: Katalog & Discovery

##### FR-01 | Browse Halaman Beranda | P0
**Acceptance Criteria:**
- AC-01: Hero carousel auto-rotate setiap 5.6 detik dengan minimal 5 slide.
- AC-02: Hero dapat di-pause saat user hover (desktop) atau touch (mobile).
- AC-03: Featured product menampilkan 1 produk unggulan + 6 produk pendukung.
- AC-04: Kategori navigasi mengambil data dari tabel `kategoris` (`aktif=true`, urutan ASC).
- AC-05: Halaman load < 2.5s pada 4G (LCP).
- AC-06: Mobile menampilkan paginated grid (3 produk per page) + lookbook carousel dengan dots.
- AC-07: Marquee text (modest, timeless, effortless, dst) berjalan continuous.

##### FR-02 | Browse Shop / Catalog | P0
**Acceptance Criteria:**
- AC-01: Hanya produk dengan `aktif=true` yang ditampilkan.
- AC-02: Default sorting: `urutan ASC, created_at DESC`.
- AC-03: Filter kategori tersedia via query string `?category={slug}`.
- AC-04: Search bar mendukung pencarian di `nama`, `deskripsi`, `deskripsi_singkat`, dan nama kategori.
- AC-05: Search bersifat case-insensitive dan partial match (LIKE %term%).
- AC-06: Tampilkan badge produk (`baru`, `terlaris`, `terbatas`, `preorder`) jika ada.
- AC-07: Tampilkan harga coret jika `harga_coret` tidak null.
- AC-08: Empty state ditampilkan jika tidak ada hasil.
- AC-09: Mobile: pagination 8 produk per page.

##### FR-03 | Lihat Detail Produk (PDP) | P0
**Acceptance Criteria:**
- AC-01: URL pattern: `/shop/{slug}` (slug unik per produk).
- AC-02: Halaman 404 jika `slug` tidak ditemukan atau `aktif=false`.
- AC-03: Tampilkan gambar utama produk + thumbnail dari `gambar_produks`.
- AC-04: Saat user pilih warna varian, gambar berubah ke `gambar_varian_produks` jika ada.
- AC-05: Selector varian: ukuran (chips) × warna (color swatch dengan `kode_warna` hex).
- AC-06: Disable / strikethrough varian dengan stok = 0.
- AC-07: Tampilkan badge stok rendah jika stok < 5 ("Tersisa 3 item").
- AC-08: Tampilkan harga = `harga + penyesuaian_harga` varian terpilih.
- AC-09: Section info: bahan, perawatan, info_model, deskripsi panjang (collapsible).
- AC-10: Tampilkan produk terkait (max 4) dari kategori sama; jika kurang, isi dari kategori lain.
- AC-11: Tombol "Tambah ke Keranjang" disabled hingga ukuran & warna dipilih.
- AC-12: Tombol "Beli Sekarang" memicu flow buy-now (skip cart, ke checkout).
- AC-13: Tombol "Tambah ke Wishlist" toggle status (client-side localStorage di MVP).

##### FR-04 | Search Overlay | P0
**Acceptance Criteria:**
- AC-01: Ikon search di header membuka overlay full-screen dengan animasi.
- AC-02: Input search auto-focus saat overlay terbuka.
- AC-03: Esc atau click di backdrop menutup overlay.
- AC-04: Tampilkan "trending search" / "popular product" saat input kosong.
- AC-05: Submit form mengarahkan ke `/shop?search={term}`.

#### Modul: Keranjang (Cart)

##### FR-05 | Tambah ke Keranjang | P0
**Acceptance Criteria:**
- AC-01: Endpoint `POST /keranjang/tambah` dengan payload `{produk_id, varian_id, jumlah}`.
- AC-02: Validasi stok varian — jika stok < jumlah, return HTTP 422 "Stok tidak mencukupi".
- AC-03: Jika item dengan kombinasi (`session_id`, `produk_id`, `varian_id`) sudah ada, increment `jumlah`.
- AC-04: Cart disimpan berbasis `session_id` (guest cart). Phase 2 migrasi cart saat login.
- AC-05: Response success mengembalikan `{success, pesan, total_item}` untuk update badge.
- AC-06: Maksimum jumlah per item = 99.
- AC-07: Toast notification "Berhasil ditambahkan ke keranjang" setelah sukses.
- AC-08: Cart drawer slide-in opsional setelah add-to-cart.

##### FR-06 | Lihat & Update Keranjang | P0
**Acceptance Criteria:**
- AC-01: Halaman `/keranjang` menampilkan daftar item: gambar varian, nama, ukuran, warna, harga satuan, qty, subtotal.
- AC-02: Tombol +/- mengupdate qty dengan validasi stok via `PUT /keranjang/{id}`.
- AC-03: Tombol hapus memanggil `DELETE /keranjang/{id}` dengan konfirmasi modal.
- AC-04: Tampilkan subtotal (sebelum ongkir & pajak).
- AC-05: Empty state dengan CTA "Belanja Sekarang" jika cart kosong.
- AC-06: Cart badge di header sync real-time via `GET /keranjang/jumlah`.
- AC-07: Tombol "Lanjut ke Checkout" disabled jika cart kosong.
- AC-08: Mendukung select item (checkbox) untuk checkout sebagian.

##### FR-07 | Cart Drawer (Mini Cart) | P1
- AC-01: Drawer slide dari kanan dengan transition 250ms.
- AC-02: Tampilkan max 3 item terakhir + link "lihat semua".
- AC-03: Tombol close (X) atau click backdrop menutup drawer.

#### Modul: Wishlist

##### FR-08 | Wishlist | P1 (MVP localStorage, Phase 2 server-side)
- AC-01: MVP: wishlist disimpan di `localStorage` (client-only).
- AC-02: Halaman `/wishlist` menampilkan produk dari localStorage.
- AC-03: Tombol love icon di card produk toggle add/remove.
- AC-04: Wishlist count badge di header sync.
- AC-05: Phase 2: migrate ke server-side jika user login.

#### Modul: Checkout & Pembayaran

##### FR-09 | Buy Now (Direct Checkout) | P0
- AC-01: Endpoint `POST /checkout/buy-now` dengan `{produk_id, varian_id, jumlah}`.
- AC-02: Sistem memvalidasi stok & varian.
- AC-03: Sistem menyimpan `checkout_payload` di session dengan `mode=buy_now`.
- AC-04: Response redirect ke `/checkout`.

##### FR-10 | Checkout from Cart | P0
- AC-01: Endpoint `POST /checkout/from-cart` dengan `{item_ids: []}`.
- AC-02: Sistem memvalidasi item milik `session_id` user.
- AC-03: Sistem menyimpan `selected_ids` di `checkout_payload` mode `cart`.
- AC-04: Response redirect ke `/checkout`.

##### FR-11 | Checkout Page | P0
**Acceptance Criteria:**
- AC-01: Tampilkan ringkasan order di kanan (sticky di desktop, accordion di mobile).
- AC-02: Form alamat: nama lengkap, telepon, provinsi (dropdown), kota/kabupaten, kecamatan, kode pos, alamat lengkap, catatan opsional.
- AC-03: Validasi telepon: format Indonesia (+62 atau 0, 10–14 digit).
- AC-04: Field email opsional di MVP, wajib di Phase 2 untuk notifikasi.
- AC-05: Pilihan kurir: minimal 3 kurir × 2 layanan dengan ETA & ongkir (dari Biteship/RajaOngkir).
- AC-06: Subtotal, ongkir, diskon (jika ada), total dihitung otomatis.
- AC-07: Pilihan pembayaran: QRIS, Virtual Account (BCA, Mandiri, BRI, BNI, BSI), e-Wallet (GoPay, OVO, DANA, ShopeePay), Credit/Debit (Phase 2 opsional), COD (Phase 2).
- AC-08: Tombol "Bayar Sekarang" disabled hingga semua field wajib terisi & metode dipilih.
- AC-09: Klik "Bayar" memicu flow ke payment gateway (snap/redirect).
- AC-10: Order tersimpan dengan status `pending_payment` sebelum pembayaran sukses.

##### FR-12 | Payment Gateway Integration | P0

**Metode Pembayaran:**

| Metode | Provider | Auto-confirm |
|---|---|---|
| QRIS | GoPay, OVO, Dana, ShopeePay | ✅ |
| Virtual Account | BCA, Mandiri, BRI, BNI, BSI, Permata | ✅ |
| E-Wallet | GoPay, ShopeePay | ✅ |
| Credit Card | Visa, Mastercard | ✅ |
| COD | Internal (Phase 2) | ❌ (manual) |

**Acceptance Criteria:**
- AC-01: Sistem create transaction & dapatkan `snap_token` / `payment_url`.
- AC-02: User di-redirect/embed Snap untuk menyelesaikan pembayaran.
- AC-03: Webhook `POST /webhook/payment` menerima notifikasi status pembayaran.
- AC-04: Validasi signature webhook dengan secret key (HMAC).
- AC-05: Status order diupdate: `paid`, `failed`, `expired`, `refunded`.
- AC-06: Idempotency: webhook duplikat tidak memproses ulang order.
- AC-07: Inventory di-decrement atomically saat status `paid`.
- AC-08: Notifikasi email/WA ke customer & admin saat status berubah.

#### Modul: Order Management

##### FR-13 | Order Confirmation Page | P0
- AC-01: Setelah pembayaran sukses, redirect ke `/order/{order_number}/sukses`.
- AC-02: Tampilkan order number, ringkasan item, total, alamat, ETA, tombol "Lacak Order" & "Belanja Lagi".
- AC-03: Email konfirmasi terkirim dengan invoice PDF attached.

##### FR-14 | Order Tracking | P0
- AC-01: URL `/order/{order_number}` dapat diakses guest dengan email/phone verification (OTP atau magic link).
- AC-02: Tampilkan timeline: Pesanan Dibuat → Pembayaran Diterima → Sedang Disiapkan → Dikirim → Diterima.
- AC-03: Jika sudah ship, tampilkan AWB/resi & link ke tracking kurir.
- AC-04: Tombol "Konfirmasi Diterima" jika status `delivered`.
- AC-05: Tombol "Ajukan Komplain/Retur" dalam window 3 hari setelah delivered.

##### FR-15 | Email & WhatsApp Notification | P0

**Trigger Notifications:**

| Event | Pesan |
|---|---|
| Order Created | "Pesanan #AQ-xxx berhasil dibuat. Selesaikan pembayaran dalam 1 jam." |
| Payment Confirmed | "Pembayaran #AQ-xxx telah dikonfirmasi. Pesanan sedang diproses." |
| Order Shipped | "Pesanan #AQ-xxx telah dikirim via [kurir]. No. Resi: [resi]." |
| Order Delivered | "Pesanan #AQ-xxx telah sampai. Terima kasih telah berbelanja." |
| Payment Expired | "Pembayaran #AQ-xxx telah expired. Silakan order ulang." |

**Acceptance Criteria:**
- AC-01: Notifikasi email saat: order placed, payment received, shipped (with AWB), delivered, completed.
- AC-02: Notifikasi WhatsApp via API (Wablas/Fonnte) untuk event yang sama (toggle per customer).
- AC-03: Template menggunakan brand voice & visual identity.
- AC-04: Unsubscribe link untuk marketing email (transactional tidak bisa unsub).

#### Modul: Admin Dashboard

##### FR-16 | Admin Authentication | P0
- AC-01: Login admin di `/admin/login` dengan email + password.
- AC-02: Password hashing bcrypt/argon2id (cost ≥ 12).
- AC-03: Session timeout 2 jam tanpa aktivitas.
- AC-04: 2FA (TOTP) optional di Phase 2.

##### FR-17 | Manage Produk & Varian | P0
- AC-01: CRUD produk: nama, slug auto-generate, kategori, deskripsi, harga, harga coret, SKU, berat, bahan, perawatan, info_model, badge, urutan, aktif, unggulan.
- AC-02: CRUD varian per produk: ukuran, warna, kode_warna, SKU, stok, penyesuaian_harga.
- AC-03: Upload multiple gambar produk + varian (drag-drop, set urutan, set utama).
- AC-04: Bulk action: aktif/nonaktif, set unggulan.
- AC-05: Search & filter produk di dashboard.

##### FR-18 | Manage Order | P0
- AC-01: List order dengan filter status, tanggal, customer.
- AC-02: Detail order: items, customer, alamat, pembayaran, status timeline.
- AC-03: Update status order manual: `processing`, `packed`, `shipped` (input AWB), `delivered`, `cancelled`.
- AC-04: Print invoice & shipping label (thermal-friendly).
- AC-05: Refund button (manual, dengan integrasi gateway).

##### FR-19 | Manage Stok & Inventory | P0
- AC-01: View stok per varian dengan low-stock alert (threshold dapat disetel).
- AC-02: Bulk import/export stok via CSV.
- AC-03: Audit log perubahan stok.

##### FR-20 | Analytics Dashboard | P1
- AC-01: Dashboard menampilkan: total order, revenue, AOV, conversion rate (estimasi), top product, top customer.
- AC-02: Filter periode: hari, minggu, bulan, custom range.
- AC-03: Grafik time-series penjualan & traffic.

#### Modul: Promo & Voucher (Phase 2)

##### FR-21 | Voucher Engine | P1

**Tipe Voucher:**

| Tipe | Contoh |
|---|---|
| Percentage Discount | 10% off all items |
| Fixed Amount | Rp 50.000 off |
| Free Shipping | Gratis ongkir |
| Buy X Get Y | Beli 2 gratis 1 |
| Minimum Purchase | Min. belanja Rp 500.000 |

**Acceptance Criteria:**
- AC-01: Admin buat voucher: kode unik, tipe (% atau fixed), min purchase, max discount, kuota, periode aktif.
- AC-02: Customer input kode voucher di checkout, sistem validasi & hitung diskon.
- AC-03: Single-use vs multi-use, per-customer limit.
- AC-04: Auto-apply voucher (tanpa input kode) jika memenuhi syarat.
- AC-05: Voucher analytics (usage, revenue impact).

#### Modul: User Account (Phase 2)

##### FR-22 | Registrasi & Login Customer | P1
- AC-01: Registrasi via email + password, atau Google OAuth.
- AC-02: Verifikasi email mandatory.
- AC-03: Forgot password via email reset link (token expired 1 jam).
- AC-04: OTP via WhatsApp untuk verifikasi nomor.

##### FR-23 | Customer Dashboard | P1
- AC-01: Halaman `/akun` dengan menu: profil, alamat tersimpan, riwayat order, wishlist, voucher.
- AC-02: Multiple address book (max 5).
- AC-03: Riwayat order dengan filter status & search.
- AC-04: Account deletion (UU PDP compliance).

#### Modul: Shipping Integration

##### FR-24 | Shipping API | P0

**Provider:** Biteship (preferred) atau RajaOngkir Pro.

**Acceptance Criteria:**
- AC-01: Cek ongkir real-time berdasarkan berat & tujuan.
- AC-02: Support multiple kurir: JNE, J&T, SiCepat, Pos Indonesia, AnterAja.
- AC-03: Pilihan layanan per kurir (REG, YES, OKE).
- AC-04: Estimasi waktu pengiriman ditampilkan.
- AC-05: Tracking status via API.
- AC-06: Free shipping rules (min. purchase Rp 500K, area tertentu).
- AC-07: Flat rate fallback Rp 25.000 jika API down.
- AC-08: Origin: Malang, Jawa Timur.

#### Modul: Reviews & Ratings (Phase 2)

##### FR-25 | Product Reviews | P1
- AC-01: Rating 1–5 bintang.
- AC-02: Review text + upload foto (max 3).
- AC-03: Hanya pembeli dengan order `completed` yang bisa review.
- AC-04: Review moderation oleh admin.
- AC-05: Average rating ditampilkan di product card.
- AC-06: Filter review by rating.
- AC-07: "Helpful" vote pada review.
- AC-08: Review reminder via WhatsApp (3 hari setelah delivered).

#### Modul: Loyalty (Phase 2/3)

##### FR-26 | Loyalty Program | P2
**Mekanisme:**
- 1 poin per Rp 10.000 belanja.
- 100 poin = Rp 10.000 discount.
- Bonus poin di hari ulang tahun.
- Tier: Bronze (0–499), Silver (500–1499), Gold (1500+).
- Tier benefits: early access, exclusive discount, free shipping.

#### Modul: SEO & Content

##### FR-27 | SEO Optimization | P0
- AC-01: Setiap halaman memiliki unique `<title>` & `<meta description>`.
- AC-02: PDP menggunakan structured data Schema.org `Product` (price, availability, image, rating).
- AC-03: Sitemap.xml auto-generated & disubmit ke Google Search Console.
- AC-04: Robots.txt mengizinkan crawl untuk public pages, disallow `/admin`, `/checkout`, `/keranjang`.
- AC-05: Open Graph meta tags untuk social sharing.
- AC-06: Canonical URL untuk menghindari duplicate content.

##### FR-28 | Lookbook & Editorial | P1
- AC-01: Halaman lookbook menampilkan koleksi seasonal dengan storytelling.
- AC-02: Shoppable image: hotspot di lookbook yang link ke produk.

---

## 4. NON-FUNCTIONAL REQUIREMENTS

### 4.1 Performance

| **ID** | **Kriteria** | **Target** | **Measurement** |
|---|---|---|---|
| NFR-P01 | LCP (Largest Contentful Paint) | < 2.5s mobile, < 1.8s desktop | RUM (Web Vitals API) |
| NFR-P02 | INP (Interaction to Next Paint) | < 200ms (good) | RUM |
| NFR-P03 | CLS (Cumulative Layout Shift) | < 0.1 | RUM |
| NFR-P04 | FCP (First Contentful Paint) | < 1.5s | Lighthouse |
| NFR-P05 | TTFB (Time To First Byte) | < 600ms | Server-side measurement |
| NFR-P06 | Page weight homepage | < 2 MB compressed | Lighthouse CI |
| NFR-P07 | API response p95 | < 400ms | APM |
| NFR-P08 | Database query p95 | < 50ms | Laravel Debugbar / APM |
| NFR-P09 | Concurrent user support | 500 concurrent w/o degradation | Load test (k6/JMeter) |

### 4.2 Scalability
- NFR-S01: Sistem dapat menangani 10× traffic baseline saat campaign tanpa rewrite.
- NFR-S02: Database mendukung 100K produk, 500K orders/tahun, 1M customer record.
- NFR-S03: Stateless application server — horizontal scaling dimungkinkan.
- NFR-S04: Background queue (Laravel Queue + Redis) untuk task non-realtime: email, webhook, image processing.
- NFR-S05: Database read replicas siap untuk heavy read operations.

### 4.3 Availability & Reliability
- NFR-A01: Uptime SLA 99.5% (≈ 3.6 jam downtime/bulan max).
- NFR-A02: Maintenance window terjadwal max 1× per bulan, di luar jam peak (00:00–05:00 WIB).
- NFR-A03: RTO (Recovery Time Objective) ≤ 4 jam.
- NFR-A04: RPO (Recovery Point Objective) ≤ 1 jam (database backup hourly minimum).
- NFR-A05: Graceful degradation: jika payment gateway down, pesan jelas, tidak crash.

### 4.4 Security
- NFR-SE01: HTTPS-only (HSTS enforced), TLS 1.2+ minimum.
- NFR-SE02: Password hashing dengan Argon2id atau bcrypt cost ≥ 12.
- NFR-SE03: CSRF token di setiap form state-changing.
- NFR-SE04: SQL injection prevention via Eloquent / parameterized query (no raw concat).
- NFR-SE05: XSS prevention: escape output via Blade `{{ }}`, hindari `{!! !!}` kecuali content trusted.
- NFR-SE06: Rate limiting per IP: 60 req/min general, 5 req/min auth endpoints, 10 req/min checkout.
- NFR-SE07: Webhook signature verification (HMAC) untuk payment gateway.
- NFR-SE08: Secrets management via env variables, tidak commit ke repo.
- NFR-SE09: Dependency scanning (composer audit, npm audit) di CI.
- NFR-SE10: OWASP Top 10 mitigations diterapkan.
- NFR-SE11: Session ID regenerasi setelah login admin.
- NFR-SE12: Content Security Policy (CSP) header dikonfigurasi.
- NFR-SE13: PCI-DSS scope: tidak menyimpan data kartu kredit di server (delegasi ke payment gateway).

### 4.5 Privacy & Compliance
- NFR-C01: Patuh **UU 27/2022 tentang Pelindungan Data Pribadi (UU PDP)**.
- NFR-C02: Privacy Policy & Terms of Service tersedia & dapat dipersetujui customer.
- NFR-C03: Cookie consent banner (essential vs analytics vs marketing).
- NFR-C04: Customer dapat request data export & deletion (right to be forgotten).
- NFR-C05: Data retention: order data 10 tahun (pajak), customer profile 3 tahun aktivitas terakhir.
- NFR-C06: Registrasi PSE Lingkup Privat di Kominfo sebelum launch.

### 4.6 Usability & Accessibility
- NFR-U01: Mobile-first responsive: breakpoint utama 360, 768, 1024, 1280, 1536.
- NFR-U02: Touch target minimum 44×44 px (Apple HIG) / 48×48 dp (Material).
- NFR-U03: WCAG 2.1 Level AA compliance (kontras 4.5:1, keyboard nav, alt text, semantic HTML).
- NFR-U04: Keyboard navigation tersedia di seluruh flow critical (search, cart, checkout).
- NFR-U05: Screen reader friendly: ARIA labels, landmarks, live regions.
- NFR-U06: Form error messages jelas & inline (tidak hanya di atas form).
- NFR-U07: Loading state (skeleton/spinner) untuk operasi > 300ms.
- NFR-U08: Focus indicators visible.

### 4.7 Compatibility
- NFR-COM01: Browser support: Chrome/Edge/Firefox 2 versi terakhir, Safari 15+, Samsung Internet.
- NFR-COM02: Tidak mendukung IE11 (sudah EOL).
- NFR-COM03: OS: iOS 14+, Android 9+.
- NFR-COM04: Tested di device populer ID: iPhone (12/13/14), Samsung A-series, Xiaomi Redmi, Oppo A-series.

### 4.8 Maintainability
- NFR-M01: Kode mengikuti **PSR-12** (PHP) & **Airbnb Style Guide** (JS).
- NFR-M02: Static analysis dengan **Larastan/PHPStan** level 6+.
- NFR-M03: Code formatter **Laravel Pint** & **Prettier** ter-enforce di pre-commit hook.
- NFR-M04: Unit & feature test coverage ≥ 60% di MVP, ≥ 75% post-launch 3 bulan.
- NFR-M05: Setiap PR memerlukan minimal 1 reviewer + green CI.
- NFR-M06: Dokumentasi API (jika ada public API) menggunakan OpenAPI/Swagger.
- NFR-M07: Database migration versioned & reversible.

### 4.9 Observability
- NFR-O01: Application logs structured (JSON) dengan trace_id per request.
- NFR-O02: Error tracking via Sentry atau Bugsnag.
- NFR-O03: APM untuk monitoring performance (response time, throughput, error rate).
- NFR-O04: Uptime monitoring eksternal (UptimeRobot / BetterStack).
- NFR-O05: Alerting via Slack/Email saat error rate > 1% atau response p95 > 1s.

### 4.10 Internationalization & Localization
- NFR-I18N01: Bahasa default Indonesia. Arsitektur siap multi-language di Phase 3+.
- NFR-I18N02: Format mata uang: `Rp 489.000` (titik separator ribuan).
- NFR-I18N03: Format tanggal: `23 Mei 2026` atau `23/05/2026`.
- NFR-I18N04: Timezone server: `Asia/Jakarta` (WIB, UTC+7).

### 4.11 Backup & Disaster Recovery
- NFR-B01: Database backup otomatis: hourly incremental, daily full, retained 30 hari.
- NFR-B02: Backup tersimpan di lokasi geografis berbeda (cross-region S3/GCS).
- NFR-B03: Restore drill dilakukan minimal 1× per kuartal.
- NFR-B04: Disaster recovery plan terdokumentasi.

---

## 5. USER STORIES & USE CASES

### 5.1 User Stories

#### Discovery
- **US-01:** As a guest visitor, I want to browse the homepage and see new arrivals, so that I get inspired and discover the brand aesthetic.
- **US-02:** As a shopper, I want to filter products by category and search by keyword, so that I quickly find what I want.
- **US-03:** As a shopper, I want to see multiple angles & variant photos of a product, so that I can assess fit & color accurately.

#### Cart & Checkout
- **US-04:** As a guest shopper, I want to add to cart without registration, so that I don't face friction.
- **US-05:** As a shopper, I want to update quantity & remove items in cart, so that I can finalize my purchase confidently.
- **US-06:** As a shopper, I want to see shipping cost before final checkout, so that there are no surprises.
- **US-07:** As a shopper, I want multiple payment options (QRIS, VA, e-wallet), so that I use my preferred channel.
- **US-08:** As a shopper, I want immediate confirmation after payment, so that I trust my order is processed.

#### Post-Purchase
- **US-09:** As a customer, I want to track my order via website without login, so that I'm aware of delivery status.
- **US-10:** As a customer, I want to receive WhatsApp updates, so that I don't miss notifications.
- **US-11:** As a customer, I want to file a complaint or request return, so that I feel protected.

#### Admin
- **US-12:** As an admin, I want to manage product catalog (add/edit/disable), so that the website reflects current inventory.
- **US-13:** As an admin, I want to view & update order status, so that I can fulfill orders efficiently.
- **US-14:** As an admin, I want to print shipping labels, so that packing & shipping is fast.
- **US-15:** As an owner, I want to see daily revenue & top products, so that I can make business decisions.

### 5.2 Detailed Use Case: UC-01 Checkout Flow

| **Atribut** | **Detail** |
|---|---|
| **Use Case ID** | UC-01 |
| **Name** | Customer Completes Purchase via Checkout |
| **Actor** | Guest Customer |
| **Trigger** | User clicks "Lanjut ke Checkout" from cart, atau "Beli Sekarang" from PDP |
| **Pre-conditions** | Cart memiliki minimal 1 item; semua item memiliki stok cukup |
| **Post-conditions (Success)** | Order tersimpan dengan status `paid`, stok berkurang, notifikasi terkirim |
| **Post-conditions (Failure)** | Order tetap `pending_payment` atau `failed`, stok tidak berubah |

**Main Flow (Happy Path):**
1. Sistem menampilkan halaman checkout dengan items dari cart/buy-now.
2. User mengisi alamat pengiriman.
3. Sistem memvalidasi format input.
4. User memilih kurir → sistem hitung ongkir via API ongkir.
5. User memilih metode pembayaran.
6. Sistem menampilkan total = subtotal + ongkir – diskon.
7. User klik "Bayar Sekarang".
8. Sistem create transaction di payment gateway, dapatkan `snap_token`.
9. Sistem simpan order dengan status `pending_payment`, lock stok via inventory hold.
10. User diarahkan ke Snap/payment page.
11. User menyelesaikan pembayaran.
12. Payment gateway kirim webhook ke sistem.
13. Sistem verifikasi signature, update status `paid`, decrement stok.
14. Sistem trigger notifikasi email & WhatsApp.
15. User redirect ke `/order/{order_number}/sukses`.

**Alternative Flow A — Stok habis saat checkout:**
- 4a. Sebelum payment, sistem cek ulang stok. Jika berubah → tampilkan pesan, suggest alternatif.

**Alternative Flow B — Pembayaran gagal/expired:**
- 13a. Sistem update status `failed` atau `expired`, release stok hold.
- 13b. Kirim email "pembayaran gagal" dengan link retry (window 24 jam).

**Exception:**
- E1. Webhook tidak diterima > 30 menit → cron job polling status payment gateway.
- E2. Ongkir API down → fallback ke flat rate (Rp 25.000) dengan disclaimer.

### 5.3 Customer Purchase Flow (Diagram)

```
┌─────────┐    ┌──────────┐    ┌─────────────┐    ┌──────────┐
│Homepage │───►│  Shop /  │───►│  Product    │───►│  Cart    │
│         │    │ Category │    │  Detail     │    │ (Drawer) │
└─────────┘    └──────────┘    └─────────────┘    └────┬─────┘
                                                       │
                                                       ▼
┌─────────────┐    ┌──────────────┐    ┌──────────────────────┐
│ Order       │◄───│ Payment      │◄───│ Checkout             │
│ Confirmation│    │ (Midtrans)   │    │ (Address+Ship+Pay)   │
└──────┬──────┘    └──────────────┘    └──────────────────────┘
       │
       ▼
┌─────────────┐    ┌──────────────┐
│ Track Order │───►│ Review       │
│             │    │ Product      │
└─────────────┘    └──────────────┘
```

### 5.4 Admin Order Flow

```
┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│ New Order    │───►│ Verify       │───►│ Pack &       │
│ Notification │    │ Payment      │    │ Ship         │
└──────────────┘    └──────────────┘    └──────┬───────┘
                                               │
                                               ▼
                    ┌──────────────┐    ┌──────────────┐
                    │ Handle       │◄───│ Input Resi   │
                    │ Returns      │    │ & Notify     │
                    └──────────────┘    └──────────────┘
```

---

## 6. DATA MODEL & DATABASE DESIGN

### 6.1 Entity Relationship Diagram (Textual)

```
kategoris (1) ─── (N) produks (1) ─── (N) varian_produks (1) ─── (N) gambar_varian_produks
                              │                 │
                              │                 └── (1) ─── (N) item_keranjangs
                              │
                              └── (1) ─── (N) gambar_produks
                              └── (1) ─── (N) item_keranjangs

[NEW Phase 1] orders (1) ─── (N) order_items
              orders (1) ─── (1) shipping_addresses
              orders (1) ─── (1) payments

[NEW Phase 2] users (1) ─── (N) orders
              users (1) ─── (N) addresses
              users (1) ─── (N) reviews
```

### 6.2 Tabel Eksisting (sudah ada di repo)

> Nama tabel di project menggunakan Bahasa Indonesia secara konsisten.

**`kategoris`**: `id, nama, slug (unique), deskripsi, gambar, urutan, aktif, timestamps`

**`produks`**: `id, kategori_id (FK), nama, slug (unique), deskripsi, deskripsi_singkat, harga (int), harga_coret (int nullable), sku (unique), berat (gram), bahan, perawatan, info_model, aktif (bool), unggulan (bool), badge (enum: baru/terlaris/terbatas/preorder), urutan, timestamps`

**`varian_produks`**: `id, produk_id (FK), ukuran, warna, kode_warna (hex), sku (unique), stok (int), penyesuaian_harga (int), timestamps`

**`gambar_produks`**: `id, produk_id (FK), url, alt, urutan, utama (bool), timestamps`

**`gambar_varian_produks`**: `id, varian_produk_id (FK), url, alt, urutan, utama (bool), timestamps`

**`item_keranjangs`**: `id, session_id (string, indexed), produk_id (FK), varian_id (FK nullable), jumlah (int), timestamps`

**`users`**: `id, name, email (unique), email_verified_at, password, remember_token, timestamps` (Laravel default — belum aktif untuk auth customer)

### 6.3 Tabel Baru yang Dibutuhkan (P0 — MVP)

**`orders`**:
```
id, order_number (unique, format: AQ-YYYYMMDD-XXXX),
customer_email, customer_phone, customer_name,
session_id (untuk link guest cart), user_id (nullable, future),
subtotal, shipping_cost, discount, tax, total,
status (enum: pending_payment, paid, processing, packed, shipped, delivered, completed, cancelled, refunded, failed, expired),
payment_method, payment_status, payment_gateway_ref,
notes, expires_at, paid_at, shipped_at, delivered_at,
voucher_id (nullable, Phase 2), timestamps
```

**`order_items`**:
```
id, order_id (FK), produk_id (FK), varian_id (FK nullable),
nama_produk_snapshot, ukuran, warna, sku,
harga_satuan, jumlah, subtotal, timestamps
```

**`shipping_addresses`**:
```
id, order_id (FK), nama_penerima, telepon,
provinsi, kota, kecamatan, kode_pos, alamat_lengkap, catatan,
courier (jne/jnt/sicepat/anteraja), service_code (reg/yes/oke), awb (nullable),
shipping_cost, eta_min_days, eta_max_days, timestamps
```

**`payments`**:
```
id, order_id (FK), gateway (midtrans/xendit), gateway_transaction_id,
method (qris/va_bca/gopay/...), amount, status,
paid_at, expired_at, raw_response (json), timestamps
```

**`audit_logs`**:
```
id, user_id, model_type, model_id, action, old_values (json), new_values (json), ip, user_agent, created_at
```

### 6.4 Tabel Phase 2

**`addresses`** (customer address book):
```
id, user_id (FK), label, nama, telepon,
provinsi, kota, kecamatan, kode_pos, alamat, is_default, timestamps
```

**`vouchers`**:
```
id, code (unique), type (percentage/fixed/free_shipping/buy_x_get_y),
value, min_purchase, max_discount, quota, used_count,
starts_at, ends_at, active, timestamps
```

**`reviews`**:
```
id, produk_id (FK), order_item_id (FK), user_id (FK nullable),
customer_name, rating (1-5), title, body, images (json),
verified_purchase, approved, timestamps
```

**`wishlists`** (server-side):
```
id, user_id (FK), produk_id (FK), timestamps
```

### 6.5 Key Indexes

```sql
-- Performance indexes
CREATE INDEX idx_produks_kategori ON produks(kategori_id, aktif);
CREATE INDEX idx_produks_slug ON produks(slug);
CREATE INDEX idx_produks_unggulan ON produks(unggulan, aktif);
CREATE INDEX idx_varian_produks_stok ON varian_produks(produk_id, stok);
CREATE INDEX idx_item_keranjangs_session ON item_keranjangs(session_id);
CREATE INDEX idx_orders_number ON orders(order_number);
CREATE INDEX idx_orders_status ON orders(status, created_at);
CREATE INDEX idx_orders_user ON orders(user_id, status);
CREATE INDEX idx_orders_expires ON orders(status, expires_at);
```

---

## 7. TECHNICAL ARCHITECTURE

### 7.1 Tech Stack (Current → Target)

| Layer | Current | Target Production | Notes |
|---|---|---|---|
| Backend | Laravel 13.8 | Laravel 13.8 | Sudah optimal |
| PHP | 8.3 | 8.3 + FPM | |
| Frontend | Blade + Tailwind 4 | Blade + Tailwind 4 | Server-side rendering |
| JS | Vanilla JS | + Alpine.js (selektif) | Interaktivitas ringan |
| Build Tool | Vite 8.0 | Vite 8.0 | Asset bundling |
| Database | SQLite (dev) | **PostgreSQL 16** atau MySQL 8 | PG: JSONB, full-text, partial idx |
| Cache & Queue | File / sync | **Redis 7** | Cache + queue + session |
| Web Server | (lokal) | Nginx + PHP-FPM | |
| Hosting | (lokal) | Laravel Forge + DigitalOcean | Forge: simple, deploy mulus |
| CDN | Cloudfront external | Cloudflare | Free tier kuat |
| Image Storage | URL eksternal | **Cloudflare R2** | No egress fee |
| Payment | — | **Midtrans** (rekomendasi MVP) | Snap v2 |
| Shipping | — | **Biteship** (rekomendasi) | Modern API, COD support |
| Email | — | **Resend** | DX bagus, gratis sampai 3K/bulan |
| WhatsApp | — | Wablas / Fonnte | Lokal: lebih murah untuk MVP |
| Search | DB LIKE | **Laravel Scout + Meilisearch** | Self-host, fast |
| Admin Panel | — | **Filament 3.x** | Admin builder Laravel native |
| Monitoring | — | Sentry + Better Stack | Errors + uptime |
| Analytics | — | GA4 + Microsoft Clarity | Clarity: free heatmap |
| CI/CD | — | GitHub Actions | Free tier cukup |

### 7.2 System Architecture

```
┌─────────────────────────────────────────────────────────────────────┐
│                         INTERNET / CDN                                │
│                    (Cloudflare)                                       │
└──────────────────────────────┬──────────────────────────────────────┘
                               │
┌──────────────────────────────▼──────────────────────────────────────┐
│                         LOAD BALANCER                                 │
│                      (Nginx / Laravel Forge)                          │
└──────────────────────────────┬──────────────────────────────────────┘
                               │
┌──────────────────────────────▼──────────────────────────────────────┐
│                      APPLICATION SERVER                               │
│                                                                       │
│  ┌─────────────┐  ┌──────────────┐  ┌────────────────────────────┐  │
│  │  Storefront │  │  Admin Panel │  │  API (Payment Webhooks)    │  │
│  │  (Blade)    │  │  (Filament)  │  │  (REST)                    │  │
│  └─────────────┘  └──────────────┘  └────────────────────────────┘  │
│                                                                       │
│  ┌─────────────────────────────────────────────────────────────────┐ │
│  │                    LARAVEL FRAMEWORK                             │ │
│  │  Controllers → Services → Repositories → Eloquent Models        │ │
│  └─────────────────────────────────────────────────────────────────┘ │
└──────────┬──────────────┬──────────────┬────────────────────────────┘
           │              │              │
    ┌──────▼──────┐ ┌────▼────┐  ┌─────▼─────┐
    │ PostgreSQL  │ │  Redis  │  │   R2/CDN  │
    │  (Primary)  │ │ (Cache) │  │  (Media)  │
    └─────────────┘ └─────────┘  └───────────┘

External Services:
┌────────────┐ ┌──────────────┐ ┌───────────────┐ ┌──────────────┐
│  Midtrans  │ │   Biteship   │ │  Wablas/Fonnte│ │  Meilisearch │
│  (Payment) │ │  (Shipping)  │ │  (WA Notif)   │ │  (Search)    │
└────────────┘ └──────────────┘ └───────────────┘ └──────────────┘
```

### 7.3 Target Directory Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Shop/
│   │   │   ├── HomeController.php
│   │   │   ├── ProductController.php
│   │   │   ├── CartController.php
│   │   │   ├── CheckoutController.php
│   │   │   └── OrderController.php
│   │   ├── Auth/
│   │   │   ├── LoginController.php
│   │   │   └── RegisterController.php
│   │   └── Webhook/
│   │       └── MidtransController.php
│   ├── Middleware/
│   ├── Requests/
│   │   ├── CheckoutBuyNowRequest.php
│   │   ├── CheckoutFromCartRequest.php
│   │   └── ...
│   └── Resources/
│       ├── CartItemResource.php
│       ├── OrderResource.php
│       └── ...
├── Models/
│   ├── Produk.php (existing)
│   ├── VarianProduk.php (existing)
│   ├── Kategori.php (existing)
│   ├── GambarProduk.php (existing)
│   ├── GambarVarianProduk.php (existing)
│   ├── ItemKeranjang.php (existing)
│   ├── Order.php (NEW)
│   ├── OrderItem.php (NEW)
│   ├── ShippingAddress.php (NEW)
│   ├── Payment.php (NEW)
│   ├── Voucher.php (Phase 2)
│   ├── Review.php (Phase 2)
│   └── User.php (existing)
├── Services/
│   ├── CartService.php
│   ├── CheckoutService.php
│   ├── PaymentService.php
│   ├── ShippingService.php
│   ├── OrderService.php
│   ├── InventoryService.php
│   ├── VoucherService.php (Phase 2)
│   └── NotificationService.php
├── Actions/
│   └── CreateOrderFromCart.php
├── DataTransferObjects/
│   └── CheckoutPayload.php
├── Enums/
│   ├── OrderStatus.php
│   └── PaymentMethod.php
├── Jobs/
│   ├── SendOrderNotification.php
│   ├── ExpireUnpaidOrders.php
│   └── AutoCompleteOrders.php
├── Events/
│   ├── OrderPlaced.php
│   ├── PaymentReceived.php
│   └── OrderShipped.php
├── Listeners/
├── Notifications/
└── Filament/
    ├── Resources/
    │   ├── ProductResource.php
    │   ├── OrderResource.php
    │   └── CustomerResource.php
    └── Widgets/
        ├── RevenueChart.php
        └── OrderStats.php
```

### 7.4 Existing Routes (web.php)

```
GET    /                          → ProdukController@home
GET    /shop                      → ProdukController@index
GET    /shop/{slug}               → ProdukController@show
GET    /wishlist                  → ProdukController@wishlist

GET    /keranjang                 → KeranjangController@index
POST   /keranjang/tambah          → KeranjangController@tambah
PUT    /keranjang/{id}            → KeranjangController@update
DELETE /keranjang/{id}            → KeranjangController@hapus
GET    /keranjang/jumlah          → KeranjangController@jumlah

GET    /checkout                  → CheckoutController@show
POST   /checkout/buy-now          → CheckoutController@buyNow
POST   /checkout/from-cart        → CheckoutController@fromCart

GET    /products                  → redirect /shop
```

### 7.5 Routes yang Perlu Ditambahkan (P0 MVP)

```
POST   /checkout/place-order      → buat order, redirect ke payment
GET    /payment/{order}/process   → trigger Snap/redirect
POST   /webhook/payment           → webhook handler (CSRF exempt, signature verify)
GET    /order/{order_number}      → tracking guest
GET    /order/{order_number}/sukses → success page

GET    /admin/login               → Filament
/admin/*                          → Filament resources
```

### 7.6 API Endpoints (untuk reference / Phase 2 mobile app)

```
STOREFRONT:
GET    /api/products
GET    /api/products/{slug}
GET    /api/categories
POST   /api/cart
PUT    /api/cart/{id}
DELETE /api/cart/{id}
GET    /api/cart
POST   /api/checkout
GET    /api/shipping/rates
POST   /api/voucher/validate
GET    /api/orders/{number}

AUTH (Phase 2):
POST   /api/auth/register
POST   /api/auth/login
POST   /api/auth/logout
POST   /api/auth/forgot-password
POST   /api/auth/reset-password
```

---

## 8. UX, DESIGN SYSTEM & SITEMAP

### 8.1 Color Palette (sudah ada di `app.css`)

| Token | Hex | Penggunaan |
|---|---|---|
| `--white` | `#FFFEFC` | Card, modal |
| `--cream` | `#F5F0EA` | Surface secondary, hover state |
| `--warm` | `#F7F2E9` | Background utama |
| `--sand` | `#D3C0AC` | Border accent, dividers |
| `--brown` | `#83513D` | Brand primary, CTA |
| `--ink` | `#201916` | Text primary |
| `--text` | `#3D332D` | Body text |
| `--muted` | `#71665D` | Secondary text |
| `--border` | `rgba(211,192,172,0.38)` | Borders |

### 8.2 Typography

| Element | Font | Weight | Size |
|---|---|---|---|
| Body | Lato | 400 | 14px |
| Headings (display) | Cormorant Garamond | 400/500/600 | 28–40px |
| Buttons | Lato | 700 | 13–14px |
| Labels | Lato | 700 | 11–12px (uppercase tracking) |
| Caption | Lato | 400 | 12px |

**Type scale:**
- H1 (hero): 40 / 32 / 28px (desktop / tablet / mobile)
- H2 (section): 28 / 24px
- H3 (subsection): 20 / 18px
- Body: 14px
- Caption: 12–13px
- Micro: 11px

### 8.3 Component Library

**Sudah ada:**
- Header (sticky transparent → solid on scroll)
- Mobile drawer menu
- Search overlay
- Hero carousel (auto-rotate)
- Product card (desktop & mobile variant)
- Lookbook carousel (mobile)
- Marquee text
- Cart drawer
- Footer multi-column
- Floating WhatsApp button

**Perlu dibuat (P0):**
- Variant selector (size chips, color swatches)
- Quantity stepper
- Address form
- Courier picker
- Payment method picker
- Order timeline
- Toast notification system
- Modal confirmation
- Empty state illustrations
- Skeleton loader

### 8.4 Sitemap

```
/
├── /shop
│   ├── /shop?category={slug}
│   ├── /shop?search={term}
│   └── /shop/{slug}                  (PDP)
├── /keranjang                        (Cart)
├── /wishlist
├── /checkout                         (Single-page)
├── /order/{order_number}             (Tracking, guest magic link)
├── /order/{order_number}/sukses      (Success page)
├── /lookbook                         (Phase 2)
│   └── /lookbook/{slug}
├── /tentang                          (About)
├── /kontak                           (Contact)
├── /faq
├── /kebijakan-privasi
├── /syarat-ketentuan
├── /pengembalian-retur
├── /panduan-ukuran                   (Size guide)
├── /admin                            (Filament — admin auth)
│   ├── /admin/dashboard
│   ├── /admin/produk
│   ├── /admin/order
│   ├── /admin/customer               (Phase 2)
│   ├── /admin/voucher                (Phase 2)
│   └── /admin/laporan
└── /akun                             (Phase 2 — customer auth)
    ├── /akun/profil
    ├── /akun/order
    ├── /akun/alamat
    └── /akun/wishlist
```

### 8.5 Page Structure

```
HOMEPAGE (/)
├── Hero Carousel (auto-rotate 5.6s)
├── Marquee Running Text
├── New Essentials Section
│   ├── Featured Product Card
│   └── Product Grid (6 items)
├── Lookbook Section (3 editorial)
├── Brand Philosophy Quote
├── Customer Favorites (4 best sellers)
├── Instagram Feed Section
├── Service Strip (4 benefits)
└── Footer

SHOP (/shop)
├── Header + Breadcrumb
├── Filter Sidebar (desktop) / Bottom Sheet (mobile)
├── Sort Dropdown
├── Product Grid (3 col desktop, 2 col mobile)
├── Pagination
└── Footer

PRODUCT DETAIL (/shop/{slug})
├── Breadcrumb
├── Image Gallery (thumbnails + main, sync varian)
├── Product Info
│   ├── Name, Price, Badge
│   ├── Size Selector (chips)
│   ├── Color Selector (swatches dengan kode_warna)
│   ├── Quantity stepper + Add to Cart + Buy Now + Wishlist
│   └── Accordions (Deskripsi, Bahan, Perawatan, Info Model, Panduan Ukuran)
├── Related Products (4)
└── Footer

CHECKOUT (/checkout)
├── Address Form / Saved Address (Phase 2)
├── Shipping Method Selector (real-time ongkir)
├── Payment Method Selector
├── Order Summary
│   ├── Cart Items
│   ├── Voucher Input (Phase 2)
│   ├── Subtotal, Shipping, Discount, Total
│   └── Bayar Sekarang Button
└── → Payment Gateway Redirect / Snap Embed
```

---

## 9. AUDIT KODE & REKOMENDASI TEKNIS

### 9.1 Hal yang Sudah Baik
- Stack modern: Laravel 13.8 + Tailwind 4 + Vite 8 — fondasi sehat untuk skala.
- Struktur data yang sudah dipikirkan: `produks → varian_produks → gambar_varian_produks` (multi-foto per warna).
- UI/UX homepage memiliki identitas brand kuat (palette, typography, lookbook, marquee).
- Cart sudah berbasis session — cocok untuk guest checkout.
- Modular: kontroler dipisah per concern (`ProdukController`, `KeranjangController`, `CheckoutController`).
- Penamaan tabel & route konsisten dalam Bahasa Indonesia.

### 9.2 Gap yang Harus Ditutup
- ⚠️ Modul **Order** sudah ada dan persist ke DB, tetapi belum production-ready karena belum terhubung payment gateway, shipment, dan notifikasi.
- ❌ Belum ada integrasi **payment gateway** — flow stop di halaman checkout.
- ❌ Belum ada integrasi **shipping** — ongkir hardcoded `Rp 11.500`.
- ⚠️ **Admin dashboard** sudah ada via Filament, tetapi belum lengkap untuk promo/voucher dan reporting owner.
- ⚠️ **Otentikasi customer** sudah aktif (login, register, account, order center dasar), tetapi belum lengkap untuk forgot password, verification, dan address book penuh.
- ⚠️ **SEO meta minimal** — sudah ada meta dasar, tetapi belum ada structured data, sitemap, canonical, dan OpenGraph lengkap.
- ⚠️ **Test coverage** masih tipis — sudah ada feature tests untuk order access, stock reserve, account, dan after-sales, tetapi flow cart/search/payment belum ter-cover memadai.
- ⚠️ **Inline styles berlebih** di `checkout.blade.php` & `index.blade.php` — kurangi maintainability.
- ⚠️ **Hardcoded content** masih bercampur di Blade (hero images, lookbook, footer links, payment labels, shipment labels).

### 9.3 Database & Domain Model

#### R-T01 | Hardening modul Order yang sudah ada — **CRITICAL**
**Masalah:** modul order sudah berjalan dan menyimpan `pesanans` + `item_pesanans`, tetapi status `pending_payment` masih berdiri sendiri tanpa transaksi gateway, shipment object, audit log domain khusus, atau notification pipeline.

**Saran:**
- Pertahankan `pesanans` dan `item_pesanans` yang sudah ada sebagai fondasi; tambahkan `payment_transactions`, `shipments`, dan `pesanan_logs` sesuai target arsitektur.
- Tetapkan boundary yang jelas: order created -> payment pending -> paid -> processing -> packed -> shipped -> delivered -> completed.
- Hubungkan signed order detail yang sudah ada dengan payment retry, status notification, dan shipment tracking.
- Tambahkan audit trail domain agar transisi pesanan tidak hanya bergantung pada activity log generik.

#### R-T02 | Inventory Management dengan Stock Hold — **HIGH**
**Masalah:** stock reserve dasar sudah ada saat place order, tetapi belum menjadi sistem hold yang lengkap dan terpisah dari lifecycle payment/shipping production. Risiko race condition masih perlu dibuktikan di skenario real webhook/concurrent checkout.

**Saran:**
- Lengkapi reserve yang ada menjadi stock-hold formal dengan TTL, release terjadwal, dan pembuktian concurrency di test.
- Pertimbangkan tabel `stock_holds` terpisah agar hold lifecycle dapat diaudit, di-release, dan di-reconcile lebih aman.
- Pastikan release stok terjadi konsisten untuk `expired`, `cancelled`, dan `refunded` lintas jalur sistem.
- Tambahkan test concurrency / webhook replay sebelum launch production.

#### R-T03 | Migrasi dari SQLite ke PostgreSQL/MySQL — **HIGH**
**Masalah:** SQLite tidak cocok untuk production e-commerce (write concurrency terbatas, no row-level locking).

**Saran:**
- Production: **PostgreSQL 16** (rekomendasi: JSONB, full-text, partial index) atau MySQL 8.
- SQLite tetap untuk testing in-memory (sudah dikonfigurasi di `phpunit.xml`).

### 9.4 Code Quality & Architecture

#### R-T04 | Pisahkan Business Logic ke Service Layer — **HIGH**
**Masalah:** Logic checkout (`mapCheckoutItem`, `cartItems`) berada di Controller. Sulit di-reuse, sulit di-test.

**Saran:** Service layer `CartService`, `CheckoutService`, `ShippingService`, `PaymentService`, `InventoryService`, `NotificationService` (lihat §7.3). Controller tipis: validate → call service → return response.

#### R-T05 | Form Request Validation — **MEDIUM**
**Masalah:** Validasi inline di controller. Sulit di-reuse.

**Saran:** `php artisan make:request CheckoutBuyNowRequest`. Semua aturan validasi & authorization di sana.

#### R-T06 | Eloquent Resource untuk JSON API — **MEDIUM**
**Masalah:** Response JSON di `KeranjangController::index()` ditulis manual dengan `->map(fn ...)`. Inconsistent.

**Saran:** `App\Http\Resources\CartItemResource`, `OrderResource`. Single source of truth untuk shape JSON.

#### R-T07 | Eliminasi Inline Styles di Blade — **MEDIUM**
**Masalah:** `checkout.blade.php` menggunakan `style="..."` masif. `app.css` & Tailwind sudah disetup tapi tidak dipakai konsisten.

**Saran:** Migrasikan inline style ke utility class Tailwind. Komponenkan repeat pattern: `<x-form-input>`, `<x-section-heading>`, `<x-btn-primary>`. Custom style di `@layer components` di `app.css`.

#### R-T08 | Komponenkan Layout Shell — **MEDIUM**
**Masalah:** Header, footer, search overlay, mobile menu di-copy paste di setiap blade view.

**Saran:**
- `resources/views/layouts/app.blade.php` master layout.
- `<x-header>`, `<x-footer>`, `<x-floating-whatsapp>`.
- Setiap halaman: `@extends('layouts.app')` + `@section('content')`.

### 9.5 Security

#### R-T09 | CSRF Protection di AJAX — **CRITICAL**
**Masalah:** Routes `POST /checkout/buy-now`, `POST /keranjang/tambah` butuh CSRF token. Perlu verifikasi JS auto-attach `X-CSRF-TOKEN`.

**Saran:**
- `<meta name="csrf-token" content="{{ csrf_token() }}">` di layout.
- Configure fetch wrapper auto-attach CSRF token.
- Pastikan `VerifyCsrfToken` middleware aktif (default Laravel).

#### R-T10 | Rate Limiting — **HIGH**
**Saran:**
```php
Route::middleware(['throttle:60,1'])->group(function () { ... });
Route::post('/checkout/buy-now', ...)->middleware('throttle:10,1');
```

#### R-T11 | Webhook Signature Verification — **CRITICAL** (saat integrasi gateway)
Webhook payment **wajib** verifikasi signature HMAC. Jangan trust apapun dari payload tanpa verifikasi.

#### R-T12 | Sanitasi Output untuk SVG Inline — **MEDIUM**
**Masalah:** Di `index.blade.php` ada `{!! $icon !!}` (raw output) untuk SVG path.

**Saran:** Pastikan icon set di-source dari array PHP yang trusted (saat ini sudah benar). Jika nanti di-pull dari DB, escape atau whitelist tag SVG.

#### R-T13 | Session Configuration — **HIGH**
- Production: gunakan `database` atau `redis` driver (bukan `file`).
- Set `SESSION_SECURE_COOKIE=true`, `SESSION_HTTP_ONLY=true`, `SESSION_SAME_SITE=lax`.
- Lifetime cart: 7 hari.

### 9.6 Performance

#### R-T14 | Image Optimization Pipeline — **HIGH**
**Masalah:** Gambar diserve dari `cloudfront.net` external (warisan import Shopee). Resolusi 2048 dilemparkan ke browser tanpa responsive sizing.

**Saran:**
- **Short-term:** tambahkan `srcset` & `sizes` di `<img>`.
- **Mid-term:** pindah hosting gambar ke Cloudflare R2 / Bunny CDN dengan transform on-the-fly.
- **Long-term:** Image service (Imgix, Cloudinary) atau `intervention/image` + queue.
- Format: WebP/AVIF dengan fallback JPEG.
- Lazy loading: sudah dipakai (`loading="lazy"`) — pertahankan.

#### R-T15 | N+1 Query Prevention — **HIGH**
**Saran:**
- `ProdukController::index()` sudah pakai `with(['gambarUtama', 'kategori', 'varians'])` — bagus.
- Aktifkan `Model::preventLazyLoading()` di `AppServiceProvider::boot()` non-production.
- Add Laravel Debugbar / Telescope di local untuk monitor query count.

#### R-T16 | Cache Strategy — **MEDIUM**
**Saran:**
- Cache halaman publik (homepage, shop, PDP) selama 5–15 menit dengan `Cache::remember`.
- Cache list kategori (jarang berubah) selama 1 jam.
- Tag-based cache invalidation saat produk di-update.
- Production: gunakan Redis sebagai cache & queue driver.

#### R-T17 | Asset Bundling & Code Splitting — **MEDIUM**
**Saran:**
- Vite production build: `npm run build` dengan minify & tree-shaking.
- Pisahkan JS per halaman: `app.js` (global), `pdp.js`, `checkout.js`.
- Vite multiple entry points.

### 9.7 Frontend & UX

#### R-T18 | Pisahkan Data dari Template — **HIGH**
**Masalah:** `index.blade.php` baris 33–135 menyimpan array hardcoded (`$products`, `$lookbooks`, `$bestSellers`).

**Saran:**
- Pindahkan ke config: `config/lookbook.php`, `config/payments.php`, `config/footer-links.php`.
- Atau migrasikan ke DB dengan model `Lookbook`, `Highlight`, `FooterLink`.
- Yang dinamis (`$products`) ganti dengan `$produkUnggulan` dari controller.

#### R-T19 | Komponen Reusable — **MEDIUM**
- `<x-product-card :produk="$produk" :variant="'default|featured|compact'" />`
- `<x-section-header title="..." subtitle="..." cta="..." />`
- `<x-icon-btn :icon="..." :label="..." :badge-count="..." />`
- `<x-empty-state :message="..." :cta="..." />`

#### R-T20 | JavaScript Modularisasi — **MEDIUM**
```
resources/js/
├── app.js                          ← entry, header sync, mobile menu
├── modules/
│   ├── hero-carousel.js
│   ├── product-pagination.js
│   ├── lookbook-carousel.js
│   ├── search-overlay.js
│   ├── cart-drawer.js
│   ├── variant-selector.js         ← PDP
│   ├── add-to-cart.js
│   └── checkout-form.js
└── utils/
    ├── api.js                      ← fetch wrapper dengan CSRF
    ├── format-currency.js
    └── toast.js
```

#### R-T21 | Accessibility Audit — **HIGH**
- Gunakan `<button>` untuk action, `<a href>` untuk navigasi.
- Semua form input punya `<label>` (visible atau `aria-label`).
- Modal/drawer trap focus + restore focus saat close.
- Skip-to-content link untuk keyboard user.
- Run `axe-core` di Playwright untuk automated check.

#### R-T22 | Loading & Error States — **HIGH**
- Skeleton loader saat fetch produk/cart (avoid layout shift).
- Error boundary: tampilkan "Coba lagi" CTA jika fetch gagal.
- Optimistic UI: tambahkan ke cart langsung di UI, rollback jika API error.

### 9.8 Testing

#### R-T23 | Test Coverage Strategy — **HIGH**
**Prioritas:**
1. **Feature tests** flow critical:
   - Add to cart → cart updated
   - Stok validation → fail with proper status
   - Checkout buy-now → redirect ke checkout
   - Order creation → DB state correct
2. **Unit tests** service layer (CartService, CheckoutService, InventoryService).
3. **Browser tests** (Laravel Dusk / Playwright):
   - Happy-path checkout end-to-end
   - Variant selector behavior
   - Mobile pagination
4. **Performance test** k6 sebelum launch.

#### R-T24 | CI/CD Pipeline — **HIGH**
**GitHub Actions:**
```yaml
- Lint (Pint + Prettier)
- Static analysis (PHPStan level 6)
- Unit + feature tests
- Browser tests (Playwright headless)
- Lighthouse CI (performance budget)
- Security scan (composer audit)
- Build assets
- Deploy ke staging on PR merge
- Manual approval gate untuk production
```

---

## 10. REKOMENDASI BISNIS

### 10.1 Strategi Akuisisi & Konversi

#### R-B01 | Trust Signal di Above-the-Fold
- Badge "Dikirim dari Malang", "Garansi 7 hari retur", "Dipercaya 5K+ pelanggan".
- Logo payment partner & shipping partner di footer.
- Testimoni terverifikasi di homepage (carousel).

#### R-B02 | Reduce Checkout Friction
- Guest checkout (sudah direncanakan) — wajib.
- Auto-fill alamat dari kode pos (gunakan API ekspedisi).
- Saved address di session (jika user belum login).
- Real-time validation: tampilkan error di blur, bukan di submit.
- Progress indicator: 1. Alamat → 2. Pengiriman → 3. Pembayaran.

#### R-B03 | Recovery Flow
- **Cart abandonment email** (Phase 2): kirim 1 jam, 24 jam, 72 jam.
- **Failed payment retry**: link untuk lanjutkan pembayaran (window 24 jam).
- **Back-in-stock notification**: customer subscribe ke varian habis.

#### R-B04 | Pricing & Promo Strategy
- **Free shipping threshold:** Rp 500.000 (mendorong AOV).
- **First-time discount:** kode `WELCOME10` 10% (max Rp 50K).
- **Bundling:** abaya + khimar matching dengan diskon 5–10%.
- **Loyalty points** (Phase 2/3).

### 10.2 Retention & CRM

#### R-B05 | Email Marketing Foundation
- Service: Resend / Brevo / Mailchimp.
- List building: capture email di newsletter, post-purchase, abandoned cart.
- Segmentasi: new vs returning, by category preference, by AOV bracket.
- Automation: welcome series (3 email), birthday discount, win-back (60 hari tidak transaksi).

#### R-B06 | WhatsApp Marketing (Indonesia-specific)
- WA Business API untuk transactional + opt-in marketing.
- Broadcast koleksi baru ke segmented list.
- Care: respon cepat di jam kerja, auto-reply di luar jam.

#### R-B07 | Review & UGC
- Auto-trigger email review 7 hari setelah delivered.
- Insentif: voucher Rp 25K untuk review dengan foto.
- Display review terverifikasi di PDP — SEO + social proof.

### 10.3 SEO & Content

#### R-B08 | Content Marketing
- **Blog/journal** (Phase 2): "Cara Memilih Abaya untuk Body Type", "Inspirasi Lebaran".
- **Lookbook editorial** dengan storytelling per koleksi.
- **Style guide** & **size guide** sebagai pillar content.
- Konten ranking organic untuk long-tail keyword.

#### R-B09 | Local SEO & Schema
- `Schema.org/Product` di setiap PDP.
- `Schema.org/Organization` + `LocalBusiness` di footer/about.
- Google My Business untuk lokasi Malang (jika ada offline pickup).

### 10.4 Operasional

#### R-B10 | SOP Fulfillment
- Cut-off pengiriman: order sebelum jam 14:00 dikirim hari itu juga.
- Packing standard: dust bag + thank-you card + voucher next purchase.
- Quality check sebelum packing (size double-check).
- Photo bukti kondisi sebelum kirim (untuk handle komplain).

#### R-B11 | Customer Service Tools
- **Phase 1:** WhatsApp + Email manual.
- **Phase 2:** Helpdesk (Tawk.to / Crisp / Freshdesk) dengan SLA tracking.
- FAQ comprehensive di website.

---

## 11. ROADMAP 12 BULAN

### Phase 1 — MVP Launch (Minggu 1–8)
**Tujuan:** Go-live dengan flow transaksi end-to-end yang fungsional.

```
Minggu 1–2: Foundation
├── Database schema & migrations (orders, order_items, shipping_addresses, payments)
├── Service layer foundation (CartService, CheckoutService, InventoryService)
├── Form Request, Eloquent Resources
├── Layout components (<x-header>, <x-footer>)
└── Filament setup

Minggu 3–4: Core Commerce
├── Order persistence (create order from cart/buy-now, atomic transaction)
├── Cart drawer integration
├── Checkout polish (form validation, courier picker, payment picker)
└── Admin produk & varian (Filament resources)

Minggu 5–6: Payment & Shipping
├── Midtrans Snap integration + webhook
├── Biteship/RajaOngkir integration (real-time ongkir, multi-courier)
├── Order management admin (status update, AWB input, print label)
├── Stock hold mechanism (TTL 15 menit)
└── Auto-cancel expired orders (scheduled job)

Minggu 7: Notifications & Tracking
├── Email transactional (Resend) — order placed, paid, shipped, delivered
├── WhatsApp notification (Wablas/Fonnte)
├── Order tracking page (guest dengan magic link)
└── SEO meta + Schema.org Product + sitemap

Minggu 8: Hardening & Launch
├── Privacy Policy, T&C, FAQ, Return Policy
├── Performance tuning + image optimization
├── Security hardening (rate limit, CSP, session)
├── Test (feature + browser) + load test
└── Soft launch → Beta → Full launch
```

**Success Metric:** 100 transaksi pertama dalam 30 hari pasca-launch.

### Phase 2 — Growth & Retention (Bulan 3–6)
**Tujuan:** Optimasi konversi, retain customer, foundation CRM.

- Customer registrasi & login (email + Google OAuth)
- Customer dashboard (orders, addresses, wishlist)
- Voucher engine (% / fixed, min purchase, kuota)
- Abandoned cart recovery (email + WA)
- Review & rating per produk (verified-purchase badge)
- Wishlist server-side (sync antar device)
- Back-in-stock notification
- Email automation (welcome series, post-purchase, win-back)
- Loyalty points basic
- Analytics dashboard (revenue, AOV, top product, conversion funnel)
- Search Meilisearch
- Shipping integration RajaOngkir Pro / Biteship (full features)

**Success Metric:** Repeat-purchase rate ≥ 25%, AOV +15% dari baseline.

### Phase 3 — Personalization & Editorial (Bulan 7–9)
**Tujuan:** Brand experience yang lebih kaya, personalisasi, scaling konten.

- Lookbook shoppable (hotspot di gambar)
- Blog/journal dengan CMS sederhana
- Recommendation engine (frequently bought together, similar products)
- Personalisasi homepage berdasarkan riwayat
- PWA (Add to Home Screen, offline catalog)
- Multi-admin role-based access (manager, packer, finance)
- Live chat integration
- Bundling & cross-sell otomatis
- A/B testing framework (LaunchDarkly / Growthbook)
- Multi-language (ID/EN)

**Success Metric:** Time on site +30%, recommendation CTR ≥ 8%.

### Phase 4 — Scale & Diversification (Bulan 10–12)
**Tujuan:** Channel expansion, B2B, automation.

- Mobile native app (iOS + Android) — evaluate ROI
- B2B / wholesale portal (custom pricing tier)
- Marketplace integration (Shopee/Tokopedia API untuk sync stok)
- Multi-warehouse / dropship support
- Advanced inventory: forecasting, reorder point, supplier mgmt
- Customer Lifetime Value (CLV) modeling & RFM segmentation
- International shipping (Singapore, Malaysia)
- Multi-currency

**Success Metric:** 30% revenue dari kanal D2C website, B2B contributing 10%.

---

## 12. ESTIMASI EFFORT & TECH STACK PRODUCTION

### 12.1 Estimasi Effort MVP (1 senior fullstack + 0.5 UI/UX + 0.5 QA)

| **Module** | **Effort (md)** |
|---|---|
| Refactor: layout components, service layer | 5 |
| Order module (DB, service, flow) | 8 |
| Payment integration (Midtrans Snap + webhook) | 6 |
| Shipping integration (Biteship multi-courier) | 4 |
| Checkout page polish (form, validation, UX) | 5 |
| Admin dashboard (CRUD produk + order, Filament) | 10 |
| Email transactional + template | 3 |
| WhatsApp integration | 2 |
| Order tracking page | 2 |
| SEO meta + structured data + sitemap | 2 |
| Wishlist (localStorage) | 1 |
| Cart drawer integration | 2 |
| Performance tuning + image optimization | 3 |
| Security hardening + rate limit | 2 |
| Test (feature + browser) | 6 |
| Bug fixing & polish | 5 |
| **Total MVP** | **~66 md** |

> ~13 minggu kalender (1 dev), atau ~7 minggu (2 dev paralel).

### 12.2 Estimasi Biaya Bulanan Production

| Service | Purpose | Pricing Estimate |
|---|---|---|
| Midtrans | Payment Gateway | 0.7%–4.4% per transaksi (no monthly fee) |
| Biteship | Shipping API | Pay-as-you-use (~Rp 500/cek ongkir) |
| RajaOngkir Pro (alt) | Shipping API | Rp 150.000/bulan |
| Fonnte / Wablas | WhatsApp API | Rp 100.000–200.000/bulan |
| Meilisearch Cloud (Phase 2) | Search | Free tier (10K docs) |
| Cloudflare R2 + CDN | Media Storage | ~Rp 50.000/bulan |
| Laravel Forge | Server Management | $12/bulan |
| DigitalOcean Droplet | Hosting | $24/bulan (4GB RAM) |
| Sentry | Error Tracking | Free tier |
| Better Stack | Uptime monitoring | Free tier / $10/bulan |
| Resend | Email | Free sampai 3K/bulan |
| **Estimasi Total** | | **Rp 800.000 – 1.500.000/bulan** |

---

## 13. PRE-LAUNCH & SECURITY CHECKLIST

### 13.1 Security Checklist
- [ ] HTTPS + HSTS enforced
- [ ] CSRF protection di semua POST/PUT/DELETE
- [ ] Rate limiting di endpoint sensitif (login, checkout, webhook)
- [ ] Secrets di `.env`, tidak di-commit
- [ ] Database backup otomatis + restore drill
- [ ] Error tracking aktif (Sentry)
- [ ] Webhook signature verification
- [ ] Dependency audit (`composer audit`, `npm audit`)
- [ ] Content Security Policy header
- [ ] X-Frame-Options, X-Content-Type-Options, Referrer-Policy
- [ ] Cookie secure + httpOnly + sameSite
- [ ] Admin route IP whitelist (opsional, untuk tim kecil)
- [ ] Penetration test sederhana (OWASP ZAP scan)
- [ ] PSE Lingkup Privat terdaftar di Kominfo
- [ ] Privacy Policy & T&C ter-publish

### 13.2 Pre-Launch Readiness

#### Konten
- [ ] Min 30 produk siap publish dengan foto multi-angle
- [ ] Deskripsi produk konsisten tone & format
- [ ] Size guide finalized
- [ ] FAQ minimal 20 pertanyaan
- [ ] Privacy Policy, T&C, Return Policy reviewed by legal

#### Operasional
- [ ] Akun Midtrans/Xendit production approved
- [ ] Akun Biteship/RajaOngkir aktif
- [ ] Rekening bank atas nama badan usaha
- [ ] Stok fisik dihitung & match dengan database
- [ ] SOP packing & shipping distribusikan ke tim
- [ ] Customer service hours & escalation defined

#### Marketing
- [ ] Landing page launch siap
- [ ] Email & WA template untuk launching
- [ ] Konten sosmed launch (carousel, reels)
- [ ] Voucher launch siap
- [ ] Influencer / affiliate brief disiapkan

#### Teknis
- [ ] Production environment di-set up & tested
- [ ] Staging environment untuk QA
- [ ] Load test passed (target: 500 concurrent)
- [ ] Lighthouse score ≥ 85 mobile
- [ ] All P0 bugs closed
- [ ] Backup & DR procedure tested
- [ ] Monitoring & alerting configured
- [ ] Domain & SSL active
- [ ] Google Analytics & Meta Pixel terpasang
- [ ] Sitemap submitted ke Google Search Console

### 13.3 Definition of Ready (DoR)
- User story memiliki acceptance criteria.
- Design mockup tersedia (Figma) atau wireframe disepakati.
- Dependencies terdaftar.
- Estimasi effort sudah dilakukan.

### 13.4 Definition of Done (DoD)
- Kode merge ke `main` via PR.
- Minimal 1 reviewer approve, CI green.
- Unit/feature test ditambah dan lulus.
- Manual QA pass di staging untuk semua acceptance criteria.
- Tidak ada regression di test suite eksisting.
- Dokumentasi (jika applicable) di-update.
- Lighthouse score ≥ 85 (performance) di staging.
- Tidak ada accessibility critical issue (axe-core).
- Demo ke product owner.

### 13.5 Launch Strategy
- **Soft Launch:** invite-only ke 50 loyal customer (data dari Shopee/IG), feedback 1 minggu.
- **Beta Launch:** umumkan via Instagram, batas 500 transaksi pertama dengan voucher exclusive.
- **Full Launch:** bertepatan dengan launching koleksi baru, full marketing campaign.

---

## 14. OPEN QUESTIONS

> Keputusan yang membutuhkan input owner sebelum kickoff Phase 1.

1. **Payment Gateway:** Midtrans atau Xendit?
   - **Midtrans:** dominan, ekosistem matang, Snap UI bagus, fee 2.0% (QRIS) – 4.4% (Credit Card).
   - **Xendit:** lebih flexible API, lebih murah untuk volume tinggi, dashboard modern.
   - **Rekomendasi:** Midtrans untuk MVP karena onboarding lebih simple di skala kecil.

2. **Shipping API:** Biteship atau RajaOngkir?
   - **Biteship:** modern, multi-courier dalam 1 API, support COD, label PDF generated.
   - **RajaOngkir:** legacy tapi cakupan luas.
   - **Rekomendasi:** Biteship.

3. **WhatsApp Provider:** Unofficial (Wablas/Fonnte) atau Official API?
   - **Unofficial:** murah (~Rp 200K/bulan), risiko diblokir WhatsApp.
   - **Official (via BSP Wati/Qiscus):** patuh aturan, mahal (Rp 1–3 juta/bulan + per-message).
   - **Rekomendasi:** mulai Fonnte/Wablas untuk MVP, naik ke Official saat skala.

4. **COD (Cash on Delivery):** Aktif di MVP?
   - **Pro:** akses customer non-banked, marketshare lebih luas.
   - **Kontra:** retur rate tinggi (15–25%), cashflow tidak instant, fraud risk.
   - **Rekomendasi:** Tunda ke Phase 2, fokus prepaid dulu di MVP.

5. **Hosting:** Forge + DigitalOcean atau VPS sendiri?
   - **Forge + DO Droplet $24/bulan:** simple, auto-deploy, best practice.
   - **VPS sendiri:** lebih murah ($6/bulan) tapi butuh sysadmin.
   - **Rekomendasi:** Forge + DO untuk peace-of-mind di tahap awal.

6. **Free Shipping Threshold:** Rp 500K (rekomendasi default) — final atau ada usulan?
7. **Kebijakan retur:** free return atau berbayar (kecuali kasus salah size)?
8. **Size guide:** standar internasional atau ukuran custom Auraquina?
9. **Live chat (Tawk.to / Crisp) di MVP?** — Tunda Phase 2 untuk fokus core.

---

## 15. APPENDIX

### 15.1 Glossary Status Order

| Status | Trigger | Visible to Customer |
|---|---|---|
| `pending_payment` | Order created, awaiting payment | Yes (Menunggu Pembayaran) |
| `paid` | Payment confirmed via webhook | Yes (Pembayaran Diterima) |
| `processing` | Admin acknowledged | Yes (Sedang Diproses) |
| `packed` | Items packed, awaiting pickup | Yes (Sedang Disiapkan) |
| `shipped` | Handed to courier, AWB issued | Yes (Dalam Pengiriman) |
| `delivered` | Courier marked delivered | Yes (Diterima) |
| `completed` | Customer confirmed atau auto setelah 7 hari | Yes (Selesai) |
| `cancelled` | Cancelled by customer atau admin | Yes (Dibatalkan) |
| `refunded` | Refund processed | Yes (Dikembalikan) |
| `failed` | Payment failed | Yes (Pembayaran Gagal) |
| `expired` | Payment window expired | Yes (Kedaluwarsa) |

### 15.2 Definisi & Istilah

| **Istilah** | **Definisi** |
|---|---|
| Produk | Item fashion (abaya, khimar, one-set, aksesoris) |
| Varian | Kombinasi unik dari ukuran × warna untuk satu produk |
| SKU | Stock Keeping Unit, kode unik per varian |
| Cart / Keranjang | Wadah sementara item yang akan di-checkout |
| Guest Checkout | Pembelian tanpa registrasi akun |
| Session-Cart | Cart yang disimpan berbasis Laravel session ID |
| PDP | Product Detail Page |
| PLP | Product List Page (shop/catalog) |
| AOV | Average Order Value |
| LCP | Largest Contentful Paint (Web Vitals) |
| RUM | Real User Monitoring |
| HMAC | Hash-based Message Authentication Code (untuk webhook signature) |

### 15.3 Asumsi & Batasan

#### Asumsi
- Tim sudah memiliki foto produk berkualitas tinggi (hasil photoshoot).
- Stok fisik dikelola di satu warehouse di Malang.
- Volume order awal ≤ 200 order/hari (asumsi load).
- Tim memiliki akses akun payment gateway atas nama PT/CV.
- Domain `auraquina.id` atau sejenisnya sudah dimiliki.
- Brand sudah memiliki rekening bank atas nama badan usaha.

#### Batasan
- **Anggaran:** project di-bootstrapping dari modal sendiri.
- **Waktu:** target launch sebelum momentum Ramadhan/Lebaran berikutnya.
- **Tim:** founder + 1–2 developer + 1–2 admin operasional.
- **Teknologi:** stack saat ini Laravel + Tailwind + SQLite (development).
- **Logistik:** belum ada gudang/warehouse partner — semua dari satu lokasi.

#### Dependencies
- Approval payment gateway (typical 7–14 hari kerja untuk Midtrans/Xendit).
- API key shipping provider (RajaOngkir/Biteship).
- Penyiapan PSE (Penyelenggara Sistem Elektronik) Lingkup Privat di Kominfo.
- SSL certificate & hosting production-ready.
- Konten produk (deskripsi, foto, lookbook) finalized.

### 15.4 Competitive Analysis

| Feature | Auraquina (Target) | Hijup | Buttonscarves | Wearing Klamby |
|---|---|---|---|---|
| Custom Website | ✅ | ✅ | ✅ | ✅ |
| Mobile App | PWA (Phase 3) | ✅ | ❌ | ❌ |
| Live Chat | WhatsApp | In-app | WhatsApp | WhatsApp |
| Loyalty Program | ✅ (Phase 2/3) | ✅ | ✅ | ❌ |
| Size Guide | ✅ | ✅ | ✅ | ✅ |
| COD | Phase 2 | ✅ | ❌ | ✅ |
| International Ship | Phase 4 | ✅ | ✅ | ❌ |

### 15.5 Success Metrics Tracking

| Metric | Baseline | Target (3 bulan) | Target (6 bulan) | Target (12 bulan) |
|---|---|---|---|---|
| Monthly Revenue (website) | Rp 0 | Rp 25 juta | Rp 50 juta | Rp 100 juta |
| Registered Users | 0 | 500 | 1.500 | 3.000 |
| Conversion Rate | — | 1.5% | 2.0% | 2.5% |
| Average Order Value | — | Rp 550.000 | Rp 600.000 | Rp 650.000 |
| Cart Abandonment | — | < 70% | < 65% | < 60% |
| Page Load Time (LCP) | — | < 3s | < 2.5s | < 2s |
| Customer Satisfaction | — | 4.2/5 | 4.4/5 | 4.5/5 |
| Repeat Purchase Rate | — | 15% | 25% | 30% |
| % Revenue dari Website | 0% | 10% | 20% | 40% |

### 15.6 Prinsip Kerja untuk Tim

1. **Mobile-first, always.** Kualitas mobile experience adalah priority #1.
2. **Performance is a feature.** Setiap PR ukur impact ke Lighthouse.
3. **Data over opinion.** Keputusan UX divalidasi A/B test atau analytic.
4. **Brand consistency.** Setiap pixel mencerminkan estetika quiet & timeless.
5. **Customer-centric, but safe.** Bias ke customer experience, tapi tidak mengorbankan keamanan.
6. **Document as you go.** Decision log, ADR (Architecture Decision Record).
7. **Small batch, frequent release.** Daily/weekly deploy lebih baik daripada big release.
8. **Test the critical path.** 100% coverage tidak perlu, tapi flow checkout wajib hijau.

### 15.7 Referensi Kompetitor (Benchmark UX)
- **buttonscarves.com** — variant selector, lookbook
- **wearingklamby.com** — checkout flow, product story
- **heavenlights.com** — homepage editorial
- **shopee.co.id** — checkout best practice (Indonesia context)

### 15.8 Approval

| **Role** | **Nama** | **Tanggal** | **Tanda Tangan** |
|---|---|---|---|
| Business Owner / Founder | _________________ | _________________ | _________________ |
| Product Lead | _________________ | _________________ | _________________ |
| Tech Lead | _________________ | _________________ | _________________ |

---

## CHANGELOG

| Versi | Tanggal | Perubahan |
|---|---|---|
| 1.0 | 20 Mei 2026 | Initial document creation (BRD-PRD.md awal) |
| 1.0 | 23 Mei 2026 | Pemisahan ke BRD-Auraquina, PRD-Auraquina, Recommendations-Roadmap |
| **2.0** | **23 Mei 2026** | **Konsolidasi semua dokumen menjadi 1 file (single source of truth)** |

---

**End of Document.**

*Document maintained by Auraquina Development Team.*
