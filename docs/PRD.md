# AURAQUINA — Product Requirements Document (PRD)

| **Atribut** | **Detail** |
|---|---|
| **Nama Project** | Auraquina E-Commerce Platform |
| **Versi Dokumen** | 1.0 |
| **Tanggal** | 24 Mei 2026 |
| **Status** | Draft for Review |
| **Author** | Product & Engineering Team |
| **Approver** | Founder / Business Owner |
| **Klasifikasi** | Internal — Confidential |
| **Referensi** | `docs/BRD-PRD.md` (BRD v2.0), `docs/SRS.md` (SRS v1.0) |

> Dokumen ini adalah **product spec** untuk Auraquina E-Commerce. Fokus pada *what* dibangun, *why*-nya, *user flow*, dan *acceptance criteria*. Untuk detail teknis (API, schema, state machine, integrasi) lihat `docs/SRS.md`. Untuk justifikasi bisnis lihat `docs/BRD-PRD.md`.

---

## DAFTAR ISI

1. [Audit Status Saat Ini](#1-audit-status-saat-ini)
2. [Vision & Goals](#2-vision--goals)
3. [Personas & Jobs-to-be-Done](#3-personas--jobs-to-be-done)
4. [Scope Matrix](#4-scope-matrix)
5. [User Flows](#5-user-flows)
6. [Functional Requirements per Modul](#6-functional-requirements-per-modul)
7. [Non-Functional Requirements](#7-non-functional-requirements)
8. [Acceptance Criteria Master List](#8-acceptance-criteria-master-list)
9. [Release Plan](#9-release-plan)
10. [Metrics & Tracking Plan](#10-metrics--tracking-plan)
11. [Open Questions](#11-open-questions)

---

## 1. AUDIT STATUS SAAT INI

### 1.1 Yang Sudah Ada

| Area | Status | Catatan |
|---|---|---|
| Homepage UI | ✅ | `index.blade.php`, hero carousel, mobile pagination, lookbook carousel |
| Shop list UI | ✅ | `shop.blade.php`, filter kategori, search query string |
| Product Detail UI | ✅ | `product-detail.blade.php`, varian (size/color), gallery, swipe boundary → next color |
| Cart UI + API | ✅ | `KeranjangController` JSON, guest session-based |
| Checkout UI | ✅ | `checkout.blade.php` (form alamat + ringkasan) |
| Order persistence | ⚠️ Parsial | `Pesanan` & `ItemPesanan` ter-create, **belum decrement stok**, **belum payment**, **belum status flow lanjutan** |
| Auth login/register | ⚠️ Parsial | Route ada, account area belum dipakai, order belum tied ke user |
| Wishlist | ⚠️ Parsial | Hanya client `localStorage` |
| DB schema | ✅ Dasar | `produks`, `varian_produks`, `gambar_*`, `kategoris`, `item_keranjangs`, `pesanans`, `item_pesanans` |
| Search overlay & API | ✅ | `/api/search` endpoint |

### 1.2 Gap Kritis vs BRD

| ID | Gap | BRD | Priority |
|---|---|---|---|
| G-01 | **Payment gateway** belum terintegrasi (`metode_pembayaran` masih text label) | BR-03 | **P0** |
| G-02 | **Shipping API** belum ada — ongkir hardcoded `11500` | BR-04 | **P0** |
| G-03 | **Stock decrement** belum terjadi saat checkout/payment paid | BR-Inventory | **P0** |
| G-04 | **Stock locking** belum ada — race condition oversell | R-12 | **P0** |
| G-05 | **Order auto-cancel 1 jam** belum ada | BR-Order | **P0** |
| G-06 | **Admin dashboard** belum ada | BR-07 | **P0** |
| G-07 | **Notifikasi email/WA** belum ada | BR-05 | **P0** |
| G-08 | **Invoice digital** belum ada | BR-18 | **P0** |
| G-09 | **Customer account** (order history, address book) belum dipakai | — | **P1** |
| G-10 | **Wishlist server-side** belum ada | FR-08 | **P1** |
| G-11 | **Voucher/promo engine** belum ada | BR-12 | **P1** |
| G-12 | **Free shipping threshold Rp 500.000** belum ada | BR-Pricing | **P1** |
| G-13 | **SEO meta dinamis** + **Schema.org Product/Breadcrumb** belum ada | O8 | **P1** |
| G-14 | **Analytics (GA4/Pixel)** belum ada | O3, O4 | **P1** |
| G-15 | **Review/rating** belum ada | BR-13 | **P1** |
| G-16 | **Test coverage** masih `ExampleTest` | NFR | **P1** |
| G-17 | **Size guide / shipping policy / FAQ / return policy** content belum real | Trust | **P1** |
| G-18 | **Restock notification** belum ada | BR-Inventory | **P2** |
| G-19 | **Loyalty / membership** | BR-11 | **P2** |
| G-20 | **Bundle / cross-sell** | BR-14 | **P2** |

### 1.3 Inkonsistensi BRD ↔ Implementasi

| Inkonsistensi | Fakta Kode | Fakta BRD | Resolusi |
|---|---|---|---|
| Order module status | `pesanans` & `item_pesanans` sudah dibuat & disimpan (lihat `CheckoutController::placeOrder`) | BRD bilang ❌ Belum ada | Update BRD §1.4 jadi ⚠️ Parsial |
| Auth | `LoginController`, `RegisterController` aktif, route guarded | BRD bilang ❌ Belum aktif | Update BRD §1.4 jadi ⚠️ Parsial |
| Ongkir | Hardcode `11500` di `CheckoutController` & migrasi default | BRD bilang `Rp 11.500` hardcoded | Konsisten — perlu replace dgn shipping API |
| Notifikasi | Tidak ada handler email/WA | BRD bilang ❌ | Konsisten |

---

## 2. VISION & GOALS

### 2.1 Product Vision
Auraquina menjadi **D2C flagship channel** brand modest fashion yang menghadirkan brand experience setara *Buttonscarves / Wearing Klamby*, dengan checkout 24/7 yang ringan, terpercaya, dan mobile-first.

### 2.2 Product Goals (12 Bulan)

| **Goal** | **Metric** | **Target** |
|---|---|---|
| Launchable MVP | Time-to-launch | 8 minggu kerja |
| Conversion | Conversion rate | ≥ 1.8% |
| AOV | Avg Order Value | ≥ Rp 550.000 |
| Repeat | Repeat purchase rate (12M) | ≥ 25% |
| Performance | LCP mobile (real users) | < 2.5s |
| Reliability | Uptime | ≥ 99.5% |
| Support | First-response time CS | < 2 jam jam kerja |

### 2.3 Non-Goals (Eksplisit Tidak Dibangun di MVP)
- Multi-warehouse fulfillment
- International shipping
- Marketplace inventory sync (Shopee/Tokopedia)
- Native mobile app
- Multi-language (ID-only di MVP)
- Subscription / recurring billing
- Live chat (WhatsApp link cukup)

---

## 3. PERSONAS & JOBS-TO-BE-DONE

### 3.1 Persona Ringkas

| Persona | Aisha — Mindful Shopper | Fatma — Loyal Customer | Nadia — Discovery Browser |
|---|---|---|---|
| Umur | 24–32 | 30–45 | 19–25 |
| Income/bulan | Rp 6–15jt | Rp 8–20jt | Rp 2–5jt |
| Device | 80% mobile | 60% mobile | 95% mobile |
| Jobs-to-be-Done | "Beli outfit kerja modest yang bisa dipakai 3 tahun" | "Lengkapi koleksi & dapat akses koleksi baru duluan" | "Cari inspirasi outfit + maybe beli aksesoris" |
| Frequency | 2–4× kuartal | 1–2× bulan | Browsing tanpa pasti beli |
| Pain | Ukuran tidak akurat, foto menipu | Harga marketplace inkonsisten, varian habis | Takut brand baru, budget tipis |
| Trust Driver | Detail material, review, return policy | Cepat respon, prioritas akses | Social proof, hashtag, reels |

### 3.2 Top JTBDs (Prioritized)

1. **Konversi MVP utama:** "Saya mau pesan abaya size M warna sand dengan checkout < 3 menit."
2. "Saya mau lacak pesanan tanpa harus chat admin."
3. "Saya mau coba produk → kalau salah size, bisa ditukar mudah."
4. "Saya mau dapat notif kalau koleksi baru drop."
5. "Saya mau beli tanpa register account."

---

## 4. SCOPE MATRIX

### 4.1 MoSCoW per Modul

| Modul | Must (MVP) | Should (Phase 2) | Could (Phase 3+) |
|---|---|---|---|
| Catalog | Browse, PDP, varian, search | Filter advanced (Meilisearch), sort | Personalized rec |
| Cart | Guest cart, mini drawer | Cart merge on login | Saved cart |
| Checkout | Single-page, address form, shipping pick, payment pick | Address book, multi-shipping option preset | Express checkout (Apple/Google Pay) |
| Payment | Midtrans Snap (QRIS, VA, e-wallet, CC) | COD select-area | Cicilan/PayLater |
| Shipping | Biteship/RajaOngkir live rate (JNE/J&T/SiCepat) | Multi-warehouse | Same-day instant courier |
| Order | Persistence, status lifecycle, auto-cancel 1h, tracking page | Self-service return request | Subscription |
| Notification | Email transactional + WA via Fonnte/Wablas | Email marketing (Mailchimp), abandoned cart | Push notif (PWA) |
| Account | Optional register, order history, address book | Profile, password reset | Social login |
| Wishlist | localStorage MVP | Server-side | Share wishlist |
| Admin | Filament dashboard: produk, varian, order, stok, customer | Reports, bulk import CSV | Multi-role granular |
| Promo | Free shipping threshold Rp 500K | Voucher code engine | Tier loyalty discount |
| SEO | Meta dynamic, sitemap, JSON-LD Product/Breadcrumb | Blog/editorial CMS | hreflang ID/EN |
| Analytics | GA4 + Meta Pixel + TikTok Pixel + e-commerce events | Looker dashboard | A/B testing |
| Review | — | Star + text + photo | Verified-purchase only |
| Trust | Size guide, return/shipping policy, FAQ | Customer testimonial wall | UGC reels |

---

## 5. USER FLOWS

### 5.1 Happy Path: Guest Checkout

```
[Home / Shop / IG Ads]
        │
        ▼
[Product Detail]
        │ pilih size + warna + qty
        ▼
[Add to Cart] ──or── [Buy Now]
        │                    │
        ▼                    │
[Cart Page]                  │
        │ pilih item          │
        ▼                    ▼
        └──────► [Checkout]
                      │
                      │ isi alamat + pilih kurir + pilih payment
                      ▼
              [Bayar via Snap]
                      │
                      ▼
              [Webhook → status=paid]
                      │
                      ▼
        [Email + WA: Pembayaran diterima + Invoice]
                      │
                      ▼
              [Tracking Page (magic link)]
```

### 5.2 Edge Flows

- **Stok habis saat checkout:** validasi ulang stok → 422 dgn alasan → user diarahkan kembali ke PDP/cart.
- **Payment expired (>1 jam):** order status `expired`, stok yang ter-hold dilepas, kirim notif `payment_expired`.
- **Payment gagal:** redirect kembali dgn pesan, tombol "Coba Bayar Lagi" → re-create transaction (id sama, attempt baru).
- **Webhook delay/duplikat:** idempotent by `order_id + transaction_status` snapshot.
- **Refund:** admin trigger → call gateway refund API → status `refunded` → notif.
- **Return request:** customer trigger di tracking page (window 7 hari) → admin review → status `return_requested` → manual refund / replacement.

### 5.3 Mobile Gallery Logic (PDP)

- Scroll/swipe gambar → dot aktif update.
- Swipe melewati gambar terakhir varian aktif → auto pilih warna berikutnya, gallery ter-render ulang dari index 0.
- Swipe sebelum gambar pertama → pilih warna sebelumnya, jump ke gambar terakhir varian baru.
- Behavior konsisten dengan tombol panah desktop.

---

## 6. FUNCTIONAL REQUIREMENTS PER MODUL

> Format: `FR-XX | Title | Priority`. AC = Acceptance Criteria.

### 6.1 Catalog & Discovery

#### FR-01 Homepage `P0`
- AC-01 Hero auto-rotate 5.6s, ≥ 5 slide, pause on hover/touch.
- AC-02 Featured 1 produk + grid 6 produk pendukung dari `produk.unggulan=true`.
- AC-03 Mobile: pagination 4 produk/page (sebelumnya 3, dinaikkan agar selaras grid 2 kolom).
- AC-04 Lookbook carousel mobile dengan dot navigation.
- AC-05 LCP < 2.5s di 4G mid-tier device.
- AC-06 Frame produk fixed `aspect-[4/5]` `object-contain`.

#### FR-02 Shop List `P0`
- AC-01 Hanya tampil produk `aktif=true`, urutan `urutan ASC, created_at DESC`.
- AC-02 Filter kategori via `?category={slug}`.
- AC-03 Search keyword (≥ 2 karakter), partial match LIKE, multi-keyword AND.
- AC-04 Empty state + suggestion dari `produkSaran` (max 4 unggulan).
- AC-05 Mobile pagination ≤ 8 produk/page.
- AC-06 Tampil badge bila `produks.badge` ada.
- AC-07 Strikethrough `harga_coret` jika tidak null.

#### FR-03 Product Detail `P0`
- AC-01 URL `/shop/{slug}` 404 jika tidak ada / `aktif=false`.
- AC-02 Galeri = `produks.gambars ∪ varian terpilih.gambarVarians`, deduped.
- AC-03 Pilih warna update gallery + reset index = 0.
- AC-04 Pilih ukuran disable jika tidak ada `varian` valid utk warna terpilih.
- AC-05 Stok rendah badge bila `varian.stok < 5` ("Tersisa N item").
- AC-06 Stok 0 → tombol Add/Buy disabled.
- AC-07 Harga = `produk.harga + varian.penyesuaian_harga`.
- AC-08 Mobile swipe boundary → pindah warna otomatis (FR-03b).
- AC-09 Tombol Add to Cart disabled hingga `varian` valid.
- AC-10 Buy Now memicu `POST /checkout/buy-now` → redirect `/checkout`.
- AC-11 Wishlist toggle (localStorage MVP), badge count sync.
- AC-12 Section description + bahan + perawatan + info_model collapsible.
- AC-13 Related max 4, fallback ke kategori lain bila kurang.

#### FR-04 Search `P0`
- AC-01 Header search ikon → overlay full-screen, autofocus.
- AC-02 Esc / klik backdrop tutup overlay.
- AC-03 GET `/api/search?q={term}` (≥ 2 char), debounce 200ms.
- AC-04 Submit → `/shop?search={term}`.
- AC-05 Tampilkan max 8 hasil dengan thumbnail, nama, kategori, harga.

### 6.2 Cart

#### FR-05 Add to Cart `P0`
- AC-01 `POST /keranjang/tambah {produk_id, varian_id, jumlah}`.
- AC-02 Stok `varian` dicek; jika `varian.stok < jumlah` → 422 "Stok tidak mencukupi".
- AC-03 Existing item (`session_id, produk_id, varian_id`) → increment `jumlah`.
- AC-04 Maks 99 per item.
- AC-05 Response `{success, pesan, total_item}` → update header badge.
- AC-06 Toast "Berhasil ditambahkan ke keranjang".

#### FR-06 Cart Page `P0`
- AC-01 List item dengan thumbnail varian, nama, label varian, harga, qty, subtotal.
- AC-02 +/- qty via `PUT /keranjang/{id}` validasi stok.
- AC-03 Hapus via `DELETE /keranjang/{id}` dengan modal konfirmasi.
- AC-04 Multi-select untuk partial checkout.
- AC-05 Subtotal otomatis (sebelum ongkir & promo).
- AC-06 CTA "Lanjut Checkout" disabled bila kosong.
- AC-07 Empty state + CTA "Belanja Sekarang".

#### FR-07 Mini Cart Drawer `P1`
- AC-01 Slide kanan, transition 250ms.
- AC-02 Close via X atau klik backdrop.
- AC-03 Tampilkan max 3 item terbaru + link "Lihat Semua".

### 6.3 Checkout

#### FR-08 Buy Now `P0`
- AC-01 `POST /checkout/buy-now {produk_id, varian_id, jumlah}`.
- AC-02 Validasi stok & varian milik produk.
- AC-03 Simpan `checkout_payload.mode=buy_now` di session.
- AC-04 Response `{success, redirect: /checkout}`.

#### FR-09 Checkout from Cart `P0`
- AC-01 `POST /checkout/from-cart {item_ids[]}`.
- AC-02 Validasi item milik `session_id`.
- AC-03 Simpan `checkout_payload.mode=cart, selected_ids`.
- AC-04 Response `{success, redirect: /checkout}`.

#### FR-10 Checkout Page `P0`
- AC-01 Layout 2 kolom desktop, single-column + sticky bottom CTA mobile.
- AC-02 Form alamat: nama_penerima, telepon (regex ID `^(\+62|0)8\d{8,12}$`), provinsi, kota, kecamatan, kode_pos, alamat_lengkap, catatan opsional.
- AC-03 Email opsional di MVP, **disarankan wajib di Phase 2** untuk notifikasi.
- AC-04 Pilihan kurir live rate (Biteship): minimal JNE, J&T, SiCepat × layanan REG/EXPRESS.
- AC-05 Pilihan payment: QRIS, VA (BCA/Mandiri/BRI/BNI/BSI/Permata), e-Wallet (GoPay, ShopeePay, Dana, OVO), CC.
- AC-06 Sub-total, ongkir, diskon (jika voucher), total auto-recalc.
- AC-07 CTA "Bayar Sekarang" disabled hingga semua field wajib + payment + shipping dipilih.
- AC-08 Saat klik Bayar: `POST /checkout/place-order` → return `snap_token` atau `redirect_url`.
- AC-09 Order awal ter-create dgn status `pending_payment`, `batas_bayar = now+1h`.

#### FR-11 Payment Gateway `P0`
- AC-01 Provider primary: Midtrans Snap. Fallback Xendit di Phase 1.5.
- AC-02 Snap embed (mobile-friendly) atau redirect.
- AC-03 Webhook `POST /webhook/midtrans` validasi signature SHA512(order_id+status_code+gross_amount+server_key).
- AC-04 Update status: `paid`, `expired`, `cancel`, `failure`, `refund`.
- AC-05 Idempotent: dedupe by `order_id + transaction_status` last-known.
- AC-06 Stok ter-decrement atomically saat status berubah ke `paid` (dgn DB lock).
- AC-07 Trigger notifikasi email + WA saat status berubah.
- AC-08 Retry policy: gateway timeout → exponential backoff 3×.

### 6.4 Order Lifecycle

#### FR-12 Order Status Machine `P0`

```
pending_payment ──(webhook paid)──► paid ──(admin)──► processing ──► packed
                                                            │
                                                            ▼
                                                          shipped ──(timer / manual)──► delivered ──► completed
   │                                                                                       │
   │ (timer 1h)                                                                            │
   ▼                                                                                       │
expired                                                                                   │
   │                                                                                       │
   │ (admin cancel / customer cancel before paid)                                          │
   ▼                                                                                       │
cancelled                                                                  return_requested ◄─ (window 7 hari)
                                                                                  │
                                                                                  ▼
                                                                              refunded
```

- AC-01 Auto-cancel: `pending_payment` > `batas_bayar` → status `expired`, release stock-hold (jika ada).
- AC-02 Customer can cancel hanya pada status `pending_payment` atau `paid` (sebelum `processing`).
- AC-03 Admin can cancel kapan saja sebelum `shipped`.
- AC-04 `delivered` → window 3 hari customer click "Konfirmasi Diterima" → `completed`. Tanpa konfirmasi dlm 7 hari → auto `completed`.
- AC-05 Tiap transition tercatat di `pesanan_log` (timestamp, actor, from→to).

#### FR-13 Order Confirmation Page `P0`
- AC-01 URL `/pesanan/{kode_pesanan}`.
- AC-02 Tampilkan kode, items, alamat, total, ETA, status timeline visual.
- AC-03 Jika `pending_payment` & `batas_bayar` belum lewat → tombol "Bayar Sekarang" re-snap.
- AC-04 Jika `shipped` → tampil AWB + link tracking kurir.
- AC-05 Jika `delivered` → tombol "Konfirmasi Diterima" + "Ajukan Komplain" (window 3 hari).

#### FR-14 Order Tracking Access `P0`
- AC-01 Guest akses via magic link (token signed) di email/WA, valid 30 hari.
- AC-02 Logged-in user lihat semua order milik `user_id` di `/account/orders`.
- AC-03 Public detail tetap require token / login.

### 6.5 Notification

#### FR-15 Email Transactional `P0`
- AC-01 Order Created (with payment instruction & link).
- AC-02 Payment Received (with invoice PDF attachment).
- AC-03 Order Shipped (AWB + carrier tracking link).
- AC-04 Order Delivered (rating prompt).
- AC-05 Payment Expired.
- AC-06 Refund Processed.

#### FR-16 WhatsApp Notification `P0`
- AC-01 Provider: Fonnte / Wablas (configurable).
- AC-02 Same triggers seperti email tapi kondisi: customer toggle prefer atau email tidak diisi.
- AC-03 Template Bahasa Indonesia, brand voice.

#### FR-17 Invoice PDF `P0`
- AC-01 Generate via Spatie Browsershot / DomPDF.
- AC-02 Attach ke email "Payment Received".
- AC-03 Download dari order detail page.
- AC-04 Berisi: header brand, kode pesanan, customer, items, harga, ongkir, diskon, total, metode pembayaran, tanggal.

### 6.6 Admin Dashboard `P0`

Stack: **Filament v3.x** (Laravel-native admin).

#### FR-18 Admin Auth `P0`
- AC-01 `/admin/login`, role-guarded route.
- AC-02 Hashing argon2id atau bcrypt cost ≥ 12.
- AC-03 Session timeout 2 jam.
- AC-04 2FA TOTP optional (Phase 2).

#### FR-19 Manage Produk `P0`
- AC-01 CRUD produk dengan field BRD lengkap (slug auto, kategori, gambar multi-upload, badge, urutan).
- AC-02 CRUD varian: ukuran, warna, kode_warna, sku, stok, penyesuaian_harga.
- AC-03 Upload gambar varian dgn flag `utama` & `urutan`.
- AC-04 Bulk action: aktif/non-aktif, set unggulan, hapus.

#### FR-20 Manage Order `P0`
- AC-01 List dgn filter status, tanggal, kurir, customer.
- AC-02 Detail + timeline + payment log + items + customer info.
- AC-03 Action: confirm payment manual (untuk transfer), update status (processing → packed → shipped + AWB → delivered), cancel, refund.
- AC-04 Print invoice & shipping label thermal-friendly (A6).

#### FR-21 Stock Management `P0`
- AC-01 List varian dgn low-stock alert (threshold default 5).
- AC-02 Bulk import/export stok CSV.
- AC-03 Audit log perubahan stok (siapa/kapan/dari/ke).

#### FR-22 Reports `P1`
- AC-01 Dashboard ringkasan: order count, revenue, AOV, top product, conversion estimasi.
- AC-02 Filter periode hari/minggu/bulan.
- AC-03 Export CSV.

### 6.7 Account `P1`

#### FR-23 Register `P1`
- AC-01 Email + password + nama.
- AC-02 Validasi email unik.
- AC-03 Password min 8, mix letters+number.
- AC-04 Verifikasi email opsional Phase 1.5.

#### FR-24 Login `P1`
- AC-01 Email + password, throttle 6 attempts/menit.
- AC-02 "Remember me" checkbox.
- AC-03 Lupa password (Phase 1.5).

#### FR-25 Order History `P1`
- AC-01 List order milik `user_id`.
- AC-02 Filter status & search kode.
- AC-03 Klik → detail standar.

#### FR-26 Address Book `P1`
- AC-01 CRUD alamat tersimpan (max 5).
- AC-02 Set default address.
- AC-03 Pre-fill di checkout.

### 6.8 Promo & Voucher `P1`

#### FR-27 Free Shipping Threshold `P1`
- AC-01 Rule global: ongkir = 0 jika `subtotal ≥ Rp 500.000`.
- AC-02 Banner promo di header & cart bila threshold tercapai.

#### FR-28 Voucher Code `P1`
- AC-01 Tipe: percentage / fixed amount / free shipping.
- AC-02 Constraints: min spend, max discount, valid period, usage limit (global + per user), kategori/produk whitelist.
- AC-03 Input kode di checkout, validasi server-side.
- AC-04 Stack rule: 1 voucher per order (default).

### 6.9 SEO & Sharing `P1`

#### FR-29 Meta & Schema `P1`
- AC-01 Dynamic `<title>` & `<meta description>` per page.
- AC-02 OpenGraph (og:title, og:description, og:image, og:type=product).
- AC-03 Twitter Card.
- AC-04 JSON-LD `Product` di PDP (name, image, price, availability, sku).
- AC-05 JSON-LD `BreadcrumbList`.
- AC-06 `sitemap.xml` auto-generated, `robots.txt`.

### 6.10 Analytics `P1`

#### FR-30 Tracking Pixel `P1`
- AC-01 GA4 + Meta Pixel + TikTok Pixel.
- AC-02 Standard e-commerce events: `view_item`, `add_to_cart`, `begin_checkout`, `add_payment_info`, `purchase`.
- AC-03 Server-side conversion API (Phase 2).
- AC-04 Consent banner cookie compliance.

### 6.11 Reviews `P1`

#### FR-31 Product Review `P1`
- AC-01 Submit hanya untuk customer dengan order `delivered/completed`.
- AC-02 Rating 1–5 + text + max 5 photo.
- AC-03 Moderasi admin sebelum tampil.
- AC-04 Tampil di PDP, pagination, filter rating.
- AC-05 JSON-LD AggregateRating.

---

## 7. NON-FUNCTIONAL REQUIREMENTS

| Kategori | Requirement |
|---|---|
| Performance | LCP < 2.5s mobile, INP < 200ms, CLS < 0.1 |
| Availability | Uptime ≥ 99.5%, RTO < 4h, RPO < 1h |
| Scalability | Handle 200 RPS peak (kampanye), auto-scale horizontal |
| Security | OWASP Top 10, CSRF aktif, XSS-safe templating, SQL injection prevention, rate-limit auth/payment, secret manager utk API key |
| Privacy | UU PDP compliance, consent log, data export & delete, retention 24 bln untuk transaksi |
| Compatibility | Modern Chromium/WebKit, iOS Safari ≥ 15, Android Chrome ≥ 100 |
| Accessibility | WCAG 2.2 AA: kontras 4.5:1, keyboard nav, alt text, ARIA labels icon-only, target 24×24px min |
| SEO | LCP/CWV passing, dynamic meta, structured data |
| Observability | Sentry error tracking, log aggregation, uptime monitoring (UptimeRobot/BetterStack), webhook log |
| Backup | Automated DB backup hourly, multi-region, restore drill kuartalan |
| Internationalization | ID-only di MVP, struktur i18n-ready |

---

## 8. ACCEPTANCE CRITERIA MASTER LIST

> Format: `Definition of Done (DoD)` per fitur sebelum dianggap "release-ready".

- [ ] Semua AC functional pass di staging.
- [ ] Unit + integration test ≥ 70% coverage di service layer (Cart, Checkout, Order, Payment, Stock).
- [ ] Manual QA pass utk happy path + 5 edge case core.
- [ ] Webhook payment teruji dgn replay & duplicate.
- [ ] Stock decrement teruji race-condition (concurrent checkout).
- [ ] Email transactional teruji terkirim ke ≥ 3 provider (Gmail, Yahoo, Outlook).
- [ ] WhatsApp notif teruji deliver < 30s.
- [ ] Lighthouse mobile ≥ 90 (Performance, Accessibility, SEO).
- [ ] Lulus pen-test self-check (XSS, CSRF, IDOR, SQLi).
- [ ] Backup & restore drill berhasil.
- [ ] Privacy policy & ToS published.
- [ ] PSE registration filed (jika omzet capai threshold).

---

## 9. RELEASE PLAN

### 9.1 Milestones

| Milestone | Window | Output |
|---|---|---|
| M0 — Foundation | Week 1 | Filament admin, model lengkap, seeder, pesanan_log, stock_hold |
| M1 — Stock & Order Lifecycle | Week 2–3 | Stock decrement+lock, status machine, auto-cancel, manual admin transitions |
| M2 — Payment | Week 3–4 | Midtrans Snap, webhook, idempotency, refund |
| M3 — Shipping | Week 4–5 | Biteship/RajaOngkir live rate, AWB simpan |
| M4 — Notification | Week 5 | Email transactional + WA via Fonnte + invoice PDF |
| M5 — SEO + Analytics | Week 6 | JSON-LD, sitemap, GA4 + Pixel + TikTok |
| M6 — Account + Wishlist Server | Week 7 | Order history, address book, wishlist persisted |
| M7 — Voucher + Free Ship | Week 7 | Free ship threshold, voucher engine basic |
| M8 — QA, Hardening, Soft Launch | Week 8 | Pen-test, load test, content polish |
| M9 — Launch | End Week 8 | Public launch + ad campaign |
| Phase 2 | Month 3–6 | Reviews, abandoned cart, restock notif, loyalty basic |

### 9.2 Dependencies

- Midtrans/Xendit account (KYC bisa makan 5–10 hari kerja).
- Biteship/RajaOngkir API key.
- Fonnte/Wablas account + WA business number.
- SMTP transactional (Postmark/Mailtrap-prod/SendGrid).
- Domain + SSL + Cloudflare/Bunny CDN utk image.
- Hosting prod (Forge/VPS/Hetzner/IDCloudHost) + DB managed.

### 9.3 Rollback Plan

- Feature flag per integrasi (payment/shipping provider) → bisa switch ke mode "manual confirmation" jika provider down.
- Database migration semua reversible.
- Tag release `vMAJOR.MINOR.PATCH`, blue-green atau atomic deploy.

---

## 10. METRICS & TRACKING PLAN

### 10.1 North-Star Metric
**GMV per Active Customer** (gross merchandise value / monthly active customer).

### 10.2 Funnel Metrics

| Stage | Event | Target |
|---|---|---|
| Discovery | session_start | baseline |
| Interest | view_item | ≥ 60% sessions |
| Consideration | add_to_cart | ≥ 12% |
| Intent | begin_checkout | ≥ 6% |
| Purchase | purchase | ≥ 1.8% |

### 10.3 Operational Metrics

- Order processing time (paid → shipped) median < 24h
- Email deliverability ≥ 98%
- WA delivery rate ≥ 95%
- Webhook success rate ≥ 99.5%
- Page errors < 0.5% session
- Mobile LCP p75 < 2.5s

### 10.4 Tracking Implementation

- GA4 dataLayer: push event di key actions (lihat FR-30).
- Meta Pixel + Conversion API (server-side fallback).
- TikTok Pixel + Events API.
- Internal `analytics_events` table (Phase 2) untuk audit.

---

## 11. OPEN QUESTIONS

| ID | Question | Owner | Decision Needed By |
|---|---|---|---|
| Q-01 | Provider payment primary: Midtrans atau Xendit? | Founder | Week 2 |
| Q-02 | Provider shipping: Biteship atau RajaOngkir Pro? | Ops | Week 2 |
| Q-03 | WA provider: Fonnte vs Wablas vs Whatsapp Cloud API official? | Ops | Week 3 |
| Q-04 | COD area & threshold? Aktifkan di MVP atau Phase 2? | Founder | Week 4 |
| Q-05 | Apakah email wajib di MVP (bukan opsional)? | Product | Week 1 |
| Q-06 | Stock-hold strategi: hold saat add-to-cart, begin-checkout, atau hanya saat snap_token issued? | Engineering | Week 2 |
| Q-07 | Kebijakan retur: refund cash atau store credit? | Founder | Week 4 |
| Q-08 | Apakah free-ship threshold 500K final atau A/B test 400K vs 500K vs 600K? | Marketing | Week 6 |
| Q-09 | Hosting: Forge+VPS, IDCloudHost, atau full managed (Cloudways)? | Engineering | Week 1 |
| Q-10 | CDN gambar: tetap CloudFront pihak ke-3 atau migrasi Bunny/Cloudflare R2? | Engineering | Week 5 |

---

## CHANGE LOG

| Versi | Tanggal | Catatan |
|---|---|---|
| 1.0 | 2026-05-24 | Initial draft after BRD audit & current code audit |
