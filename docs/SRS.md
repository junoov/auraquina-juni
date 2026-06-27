# AURAQUINA — Software Requirements Specification (SRS)

| **Atribut** | **Detail** |
|---|---|
| **Nama Project** | Auraquina E-Commerce Platform |
| **Versi Dokumen** | 1.0 |
| **Tanggal** | 24 Mei 2026 |
| **Status** | Draft for Review |
| **Author** | Engineering Team |
| **Approver** | Tech Lead / CTO Advisor |
| **Klasifikasi** | Internal — Confidential |
| **Standar Acuan** | IEEE 830-1998 (adapted), ISO/IEC 25010 |
| **Referensi** | `docs/BRD-PRD.md`, `docs/PRD.md` |

> Dokumen ini menjabarkan **kebutuhan teknis-fungsional** sistem Auraquina secara presisi sehingga developer dapat mengimplementasikan tanpa ambiguitas. Untuk *what & why*, lihat `docs/PRD.md`. Untuk *business intent*, lihat `docs/BRD-PRD.md`.

---

## DAFTAR ISI

1. [Introduction](#1-introduction)
2. [Overall Description](#2-overall-description)
3. [System Architecture](#3-system-architecture)
4. [Data Model](#4-data-model)
5. [Functional Specification](#5-functional-specification)
6. [API Specification](#6-api-specification)
7. [Integration Specification](#7-integration-specification)
8. [State Machines](#8-state-machines)
9. [Concurrency & Stock Locking](#9-concurrency--stock-locking)
10. [Security Specification](#10-security-specification)
11. [Performance & Scalability](#11-performance--scalability)
12. [Logging, Monitoring, Observability](#12-logging-monitoring-observability)
13. [Testing Strategy](#13-testing-strategy)
14. [Deployment & DevOps](#14-deployment--devops)
15. [Configuration Management](#15-configuration-management)
16. [Error Handling Catalog](#16-error-handling-catalog)
17. [Background Jobs & Schedules](#17-background-jobs--schedules)
18. [Acceptance & Definition of Done](#18-acceptance--definition-of-done)
19. [Glossary](#19-glossary)

---

## 1. INTRODUCTION

### 1.1 Purpose
SRS ini mendefinisikan kebutuhan perangkat lunak Auraquina E-Commerce versi MVP + Phase 2, termasuk arsitektur, kontrak API, model data, integrasi pihak ketiga, kebutuhan non-fungsional, dan kriteria penerimaan teknis.

### 1.2 Scope
Sistem mencakup:
- Storefront (catalog, PDP, cart, checkout, order tracking)
- Admin dashboard (Filament) untuk produk, varian, order, stok, customer, voucher
- Payment gateway integration (Midtrans Snap primary)
- Shipping integration (Biteship primary)
- Notification service (email + WhatsApp + invoice PDF)
- Authentication (guest checkout + optional account)

Out of scope MVP: native app, multi-language, marketplace sync, multi-warehouse, recurring billing.

### 1.3 Definitions, Acronyms

Lihat §19 Glossary.

### 1.4 References
- BRD `docs/BRD-PRD.md`
- PRD `docs/PRD.md`
- Laravel 13 docs
- Midtrans Snap docs (https://docs.midtrans.com/reference/snap)
- Biteship API docs (https://biteship.com/id/docs)
- Filament v3 docs

### 1.5 Intended Audience
- Backend & frontend developer
- DevOps & SRE
- QA engineer
- Tech lead untuk review arsitektural

---

## 2. OVERALL DESCRIPTION

### 2.1 Product Perspective

Auraquina adalah aplikasi web monolitik berbasis Laravel 13 + Vite + Tailwind CSS 4. Frontend disajikan via Blade templates; modul admin via Filament. Sistem terhubung ke layanan eksternal: payment gateway, shipping API, WA gateway, SMTP, CDN.

```
┌────────────────────────────────────────────────────────────┐
│                          Browser / Mobile                    │
└────────────────────────────────────────────────────────────┘
                               │  HTTPS
                               ▼
┌────────────────────────────────────────────────────────────┐
│                  Cloudflare / CDN (image)                  │
└────────────────────────────────────────────────────────────┘
                               │
                               ▼
┌────────────────────────────────────────────────────────────┐
│                  Web Server (Nginx)                        │
│                  PHP-FPM 8.3                               │
│  ┌──────────────────────────────────────────────────────┐  │
│  │ Laravel 13 (Storefront + API + Admin/Filament)        │  │
│  │ Vite (build assets)                                  │  │
│  └──────────────────────────────────────────────────────┘  │
└────────────────────────────────────────────────────────────┘
   │           │              │              │           │
   ▼           ▼              ▼              ▼           ▼
┌──────┐  ┌────────┐  ┌─────────────┐  ┌─────────┐  ┌────────┐
│MySQL │  │ Redis  │  │  Midtrans   │  │Biteship │  │ Fonnte │
│  8   │  │(cache, │  │  (Snap)     │  │ Shipping│  │   WA   │
│      │  │queue,  │  │             │  │  rate)  │  │        │
│      │  │session)│  │             │  │         │  │        │
└──────┘  └────────┘  └─────────────┘  └─────────┘  └────────┘
```

### 2.2 User Classes

| Class | Authentication | Capabilities |
|---|---|---|
| Guest | None | Browse, cart, guest checkout, track order via magic link |
| Customer | Email + password | All guest + order history, address book, wishlist persisted |
| Admin | Email + password (+TOTP Phase 2) | Manage produk, varian, order, stok, customer, voucher, report |
| Superadmin | Same | Plus role/permission, settings |

### 2.3 Operating Environment

- Runtime: PHP 8.3, Composer 2.x, Node 20+, NPM 10+
- Web: Nginx 1.24+
- DB: MySQL 8.0 (production), SQLite (dev/test)
- Cache/Queue/Session: Redis 7
- OS prod: Ubuntu 22.04 LTS
- TLS via Let's Encrypt / Cloudflare

### 2.4 Design Constraints
- Mobile-first responsive (≥ 70% traffic mobile)
- WCAG 2.2 AA accessibility
- UU PDP compliance (PSE Privat registration if applicable)
- Bahasa Indonesia primary (i18n-ready)

---

## 3. SYSTEM ARCHITECTURE

### 3.1 Layered View

```
┌──────────────────────────────────────────────────────┐
│ Presentation: Blade Views + Vite Bundle              │
├──────────────────────────────────────────────────────┤
│ Controller / HTTP: Laravel Route → Controller        │
├──────────────────────────────────────────────────────┤
│ Application Service: Cart, Checkout, Order, Payment, │
│  Shipping, Stock, Notification, Voucher              │
├──────────────────────────────────────────────────────┤
│ Domain: Eloquent Models + Domain Events + Policies   │
├──────────────────────────────────────────────────────┤
│ Infrastructure: DB, Redis, Mail, WA gateway, Storage │
└──────────────────────────────────────────────────────┘
```

### 3.2 Directory Convention (Laravel)

```
app/
  Http/
    Controllers/        ← thin (parse req, call service, return view/json)
    Middleware/
    Requests/           ← FormRequest validation
  Services/
    Cart/CartService.php
    Checkout/CheckoutService.php
    Order/OrderService.php
    Payment/MidtransService.php
    Shipping/BiteshipService.php
    Stock/StockService.php
    Notification/{Email,Whatsapp,Invoice}Service.php
    Voucher/VoucherService.php
  Models/
  Filament/             ← admin resources
    Resources/{Produk,Pesanan,...}Resource.php
  Events/
  Listeners/
  Jobs/                 ← queued
  Mail/
  Policies/
config/
  midtrans.php
  biteship.php
  whatsapp.php
routes/
  web.php
  api.php
  webhook.php           ← payment/shipping callback
database/
  migrations/
  seeders/
tests/
  Feature/
  Unit/
```

### 3.3 Package Stack

| Concern | Package |
|---|---|
| Admin | `filament/filament:^3.2` |
| Auth | Laravel Breeze (sudah dipakai) |
| Payment | `midtrans/midtrans-php:^2.5` |
| Shipping | HTTP client → Biteship REST |
| WA | HTTP client → Fonnte / Wablas REST |
| PDF | `barryvdh/laravel-dompdf:^3.0` atau `spatie/laravel-pdf` |
| Image | `spatie/laravel-medialibrary:^11` (jika perlu media abstraction) |
| Search (Phase 2) | `laravel/scout` + Meilisearch |
| Queue UI | `laravel/horizon:^5` |
| Logging | `sentry/sentry-laravel:^4` |
| HTTP Client | Laravel Http Client (built-in) |
| Test | PHPUnit (sudah), `pestphp/pest:^2` opsional |

### 3.4 Communication Patterns

- **Sync**: Web request → Controller → Service → DB.
- **Async via queue (Redis)**: notification email/WA, invoice generation, image processing, webhook follow-up retries, abandoned-cart trigger.
- **Webhook inbound**: Midtrans, Biteship status update.
- **Outbound HTTP**: shipping rate query, payment create/refund, WA send.

---

## 4. DATA MODEL

### 4.1 ER Diagram (logical)

```
kategoris ──┐
            │
            ▼
         produks ─── gambar_produks
            │
            └── varian_produks ─── gambar_varian_produks

users ────► alamats (Phase 1.5)
   │
   ├──► pesanans ───► item_pesanans
   │       │             │
   │       │             └── (snapshot: produk_id, varian_id)
   │       │
   │       ├──► pesanan_logs (audit trail)
   │       ├──► payment_transactions
   │       └──► shipments

session_id ──► item_keranjangs (guest cart)

users ──► wishlists (Phase 2)
users ──► reviews (Phase 2)

vouchers ──► voucher_redeems
stock_holds (TTL)
```

### 4.2 Schema Tambahan (yang belum ada)

#### 4.2.1 `pesanan_logs`

| Field | Type | Note |
|---|---|---|
| id | bigint PK | |
| pesanan_id | FK pesanans | cascade |
| from_status | string | nullable (saat created) |
| to_status | string | |
| actor_type | enum('system','customer','admin','webhook') | |
| actor_id | bigint nullable | user id |
| meta | json nullable | payload referensi |
| created_at | timestamp | |

Index: `(pesanan_id, created_at)`.

#### 4.2.2 `payment_transactions`

| Field | Type | Note |
|---|---|---|
| id | bigint PK | |
| pesanan_id | FK pesanans | |
| provider | string | midtrans/xendit |
| order_id | string | reference yg dikirim ke gateway (= `kode_pesanan`) |
| transaction_id | string nullable | gateway transaction id |
| snap_token | string nullable | |
| payment_type | string nullable | qris/va/gopay/cc |
| status | string | pending/settlement/paid/expired/refund/failure |
| gross_amount | int | |
| signature_key | string nullable | |
| raw_payload | json | request/response audit |
| received_at | timestamp nullable | |
| created_at, updated_at | | |

Unique: `(provider, order_id, transaction_id)`.

#### 4.2.3 `shipments`

| Field | Type | Note |
|---|---|---|
| id | bigint PK | |
| pesanan_id | FK pesanans | unique |
| courier_code | string | jne/jnt/sicepat |
| service_code | string | reg/express |
| awb | string nullable | resi |
| ongkir | int | |
| etd_min, etd_max | int nullable | days |
| tracking_url | string nullable | |
| picked_up_at, delivered_at | timestamp nullable | |
| raw_response | json nullable | |
| timestamps | | |

#### 4.2.4 `stock_holds`

| Field | Type | Note |
|---|---|---|
| id | bigint PK | |
| varian_id | FK | |
| pesanan_id | FK nullable | |
| session_id | string | |
| jumlah | int | |
| expires_at | timestamp | TTL hold |
| released_at | timestamp nullable | |
| timestamps | | |

Index: `(varian_id, expires_at)`.

#### 4.2.5 `vouchers`

| Field | Type | Note |
|---|---|---|
| id | bigint PK | |
| kode | string unique | uppercase |
| tipe | enum('percent','fixed','free_shipping') | |
| nilai | int | percent (0-100) atau fixed amount |
| min_subtotal | int default 0 | |
| max_diskon | int nullable | cap diskon untuk percent |
| mulai, berakhir | timestamp | |
| limit_global | int nullable | |
| limit_per_user | int default 1 | |
| jumlah_dipakai | int default 0 | |
| aktif | boolean default true | |
| kategori_whitelist | json nullable | id list |
| produk_whitelist | json nullable | id list |
| timestamps | | |

#### 4.2.6 `voucher_redeems`

| Field | Type | Note |
|---|---|---|
| id | bigint PK | |
| voucher_id | FK | |
| pesanan_id | FK | |
| user_id | FK nullable | |
| diskon_terapan | int | |
| timestamps | | |

#### 4.2.7 `addresses` (Phase 1.5)

| Field | Type |
|---|---|
| id, user_id, label, nama_penerima, telepon, provinsi, kota, kecamatan, kode_pos, alamat_lengkap, is_default, timestamps |

#### 4.2.8 `wishlists` (Phase 2)

| Field | Type |
|---|---|
| id, user_id, produk_id, created_at |

Unique: `(user_id, produk_id)`.

#### 4.2.9 `reviews` (Phase 2)

| Field | Type |
|---|---|
| id, produk_id, user_id, pesanan_id, rating(1-5), text, photos(json), status(pending/approved/rejected), timestamps |

#### 4.2.10 Tambahan kolom existing

- `pesanans.email` (string nullable) — disarankan wajib di Phase 2 untuk notif
- `pesanans.provinsi`, `pesanans.kecamatan`, `pesanans.kode_pos` (string nullable) — kelengkapan alamat shipping API
- `pesanans.berat_total` (int) — gram, untuk shipping
- `pesanans.expired_at` (rename dari `batas_bayar` boleh, atau biarkan)
- `pesanans.dibayar_pada` (sudah ada)
- `pesanans.cancelled_at`, `pesanans.refunded_at` (timestamp nullable)
- `varian_produks.berat` (int) gram
- `produks.berat` (sudah ada di model fillable, pastikan migrasi punya)

### 4.3 Indexing

| Table | Index | Reason |
|---|---|---|
| produks | (`aktif`, `urutan`), (`unggulan`, `urutan`), unique(`slug`) | listing & lookup |
| varian_produks | (`produk_id`), unique(`sku`) | child fetch |
| item_keranjangs | (`session_id`), (`session_id`, `produk_id`, `varian_id`) | cart fetch & dedupe |
| pesanans | unique(`kode_pesanan`), (`status`, `created_at`), (`user_id`), (`session_id`) | admin filter |
| payment_transactions | unique(`provider`, `order_id`, `transaction_id`) | webhook dedupe |
| stock_holds | (`varian_id`, `expires_at`) | release scan |
| vouchers | unique(`kode`), (`mulai`, `berakhir`, `aktif`) | code lookup |

### 4.4 Data Retention

- `item_keranjangs`: TTL 30 hari sejak `updated_at`, cleanup harian.
- `stock_holds`: cleanup hourly.
- `pesanans`: simpan 5 tahun (kewajiban pajak/akuntansi).
- `pesanan_logs`: simpan permanen untuk audit.
- `payment_transactions.raw_payload`: simpan 24 bulan, lalu mask sensitive fields.

---

## 5. FUNCTIONAL SPECIFICATION

### 5.1 Service Boundaries

#### 5.1.1 `CartService`
- `addItem(sessionId, produkId, varianId, jumlah)` → idempotent merge.
- `updateItem(sessionId, itemId, jumlah)` → revalidate stock.
- `removeItem(sessionId, itemId)`.
- `summary(sessionId)` → items, subtotal, count.

#### 5.1.2 `CheckoutService`
- `createPayload(mode, sessionId, params)` → simpan `checkout_payload` di session.
- `placeOrder(sessionId, requestData)` → DB transaction:
  1. Validasi payload & item existence.
  2. Resolve final price + ongkir + diskon.
  3. Acquire stock-hold (TTL 30 menit) per varian.
  4. Create `pesanans` + `item_pesanans` + `pesanan_logs` (status `pending_payment`).
  5. Create `payment_transactions` (pending) + call gateway → snap_token.
  6. Bersihkan cart (mode=cart).
  7. Return `{order, snap_token, redirect_url}`.

#### 5.1.3 `OrderService`
- `markPaid(orderId, paymentMeta)` → transaction:
  1. Cek `from_status=pending_payment`. Idempotent: jika sudah `paid` skip.
  2. Decrement `varian_produks.stok` SELECT FOR UPDATE.
  3. Convert stock_holds ke "consumed" / hapus.
  4. Update `pesanans.status=paid, dibayar_pada=now()`.
  5. Append `pesanan_logs`.
  6. Dispatch event `OrderPaid` → trigger notif + invoice + shipping label prep.
- `cancel(orderId, actor, reason)` → release stock-hold + log + notif.
- `expire(orderId)` → on auto-cancel timer.
- `transition(orderId, toStatus, actor, meta)` → guarded transition (lihat §8).

#### 5.1.4 `StockService`
- `reserve(varianId, jumlah, key, ttlMin=30)`.
- `release(holdId|key)`.
- `confirm(varianId, jumlah)` (decrement permanen + delete hold).
- `availableForCheckout(varianId)` = `varian.stok - SUM(active stock_holds.jumlah WHERE expires_at>now)`.

#### 5.1.5 `PaymentService` (interface) → `MidtransService` (impl)
- `createTransaction(order)` → return `snap_token`, `redirect_url`.
- `verifySignature(payload)` → bool.
- `mapStatus(transaction_status, fraud_status)` → internal status.
- `refund(orderId, amount, reason)`.

#### 5.1.6 `ShippingService` → `BiteshipService`
- `rates(originAreaId, destinationAreaId, items[], couriers[])` → list option.
- `searchAreas(keyword)` → autocomplete kota/kec.
- `createShipment(order)` → AWB.
- `trackShipment(awb, courier)` → status.

#### 5.1.7 `NotificationService`
- `dispatchEvent(event, order)` → fan out ke email + WA channel sesuai preferensi.
- Channel: `EmailChannel`, `WhatsappChannel`.
- Template: render Blade mail + plain WA template.

#### 5.1.8 `InvoiceService`
- `generate(order)` → simpan PDF di `storage/app/invoices/{kode}.pdf`, optionally push ke S3-compatible.

#### 5.1.9 `VoucherService`
- `validate(code, context)` → return `{valid, diskon, message}`.
- `apply(code, order)` → update order + record `voucher_redeems`.

### 5.2 Validation Rules (master)

| Field | Rule |
|---|---|
| `nama_penerima` | required, string, max 255 |
| `telepon` | required, regex `^(\+62|0)8\d{8,12}$` |
| `email` | nullable di MVP, required Phase 2; `email:rfc,dns` |
| `provinsi`, `kota`, `kecamatan` | required (Phase shipping integration) |
| `kode_pos` | required, regex `^\d{5}$` |
| `alamat_lengkap` | required, max 1000 |
| `metode_pengiriman` | required, harus dari hasil rates |
| `metode_pembayaran` | required, harus dari config supported |
| `jumlah` (cart/checkout) | required int 1–99 |
| `voucher_code` | nullable string max 32, uppercase server-side |

---

## 6. API SPECIFICATION

> Semua endpoint web dilindungi CSRF kecuali `/webhook/*`. Response default JSON kecuali noted.

### 6.1 Public Endpoints

| Method | URL | Body | Response | Notes |
|---|---|---|---|---|
| GET | `/` | — | HTML | Homepage |
| GET | `/shop` | query: search, category | HTML | List |
| GET | `/shop/{slug}` | — | HTML | PDP |
| GET | `/api/search?q=` | — | `{items: [{id,nama,slug,url,harga,kategori,gambar}]}` | min 2 chars |
| GET | `/keranjang` | — | HTML / JSON (Accept) | |
| GET | `/keranjang/jumlah` | — | `{total_item}` | badge |
| POST | `/keranjang/tambah` | `{produk_id, varian_id?, jumlah?}` | `{success, pesan, total_item}` | |
| PUT | `/keranjang/{id}` | `{jumlah}` | `{success}` | session-scoped |
| DELETE | `/keranjang/{id}` | — | `{success, total_item}` | |
| POST | `/checkout/buy-now` | `{produk_id, varian_id?, jumlah}` | `{success, redirect}` | |
| POST | `/checkout/from-cart` | `{item_ids:[]}` | `{success, redirect}` | |
| GET | `/checkout` | — | HTML | |
| POST | `/checkout/rates` | `{destination_area_id, items[]}` | `{rates:[...]}` | live shipping |
| POST | `/checkout/voucher` | `{code}` | `{valid, diskon, message}` | |
| POST | `/checkout/place-order` | full address + shipping + payment | `{success, snap_token, redirect}` | begin payment |
| GET | `/pesanan/{kode}` | (query `t=` token utk guest) | HTML | order detail |
| POST | `/pesanan/{kode}/cancel` | — | `{success}` | only allowed states |
| POST | `/pesanan/{kode}/confirm-received` | — | `{success}` | window 3 hari |

### 6.2 Webhook Endpoints

| Method | URL | Body | Response |
|---|---|---|---|
| POST | `/webhook/midtrans` | Midtrans notification JSON | 200 OK |
| POST | `/webhook/biteship` | Biteship status | 200 OK |

### 6.3 Auth Endpoints

| Method | URL | Body | Response |
|---|---|---|---|
| GET | `/login` | — | HTML |
| POST | `/login` | `{email, password, remember?}` | redirect/JSON |
| GET | `/register` | — | HTML |
| POST | `/register` | `{email, password, nama}` | redirect/JSON |
| POST | `/logout` | — | redirect |
| POST | `/forgot-password` | `{email}` | (Phase 1.5) |

### 6.4 Account Endpoints (auth required)

| Method | URL |
|---|---|
| GET | `/account` |
| GET | `/account/orders` |
| GET | `/account/orders/{kode}` |
| GET | `/account/addresses` |
| POST | `/account/addresses` |
| PUT | `/account/addresses/{id}` |
| DELETE | `/account/addresses/{id}` |
| GET | `/account/wishlist` |
| POST | `/account/wishlist/{produkId}` |
| DELETE | `/account/wishlist/{produkId}` |

### 6.5 Admin Endpoints (Filament-managed)

`/admin/...` — semua resource Filament untuk produk, varian, kategori, gambar, pesanan, voucher, customer, settings, reports.

### 6.6 Standard Error Format

```json
{
  "error": {
    "code": "STOCK_INSUFFICIENT",
    "message": "Stok tidak mencukupi",
    "context": { "varian_id": 123, "tersedia": 1, "diminta": 3 }
  }
}
```

HTTP status: `400` invalid input, `401` unauth, `403` forbidden, `404` not found, `409` conflict (state), `422` validation, `429` rate limit, `500` server.

---

## 7. INTEGRATION SPECIFICATION

### 7.1 Midtrans (Snap)

#### 7.1.1 Config
```
MIDTRANS_MERCHANT_ID=
MIDTRANS_CLIENT_KEY=
MIDTRANS_SERVER_KEY=
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_NOTIFICATION_URL=https://auraquina.id/webhook/midtrans
```

#### 7.1.2 Create Snap Transaction
- Endpoint: `POST https://app.{sandbox.}midtrans.com/snap/v1/transactions`.
- Request payload:
```json
{
  "transaction_details": {
    "order_id": "{kode_pesanan}",
    "gross_amount": 489000
  },
  "customer_details": { "first_name": "...", "phone": "...", "email": "..." },
  "item_details": [{"id":"prod-1","price":489000,"quantity":1,"name":"..."}],
  "enabled_payments": ["qris","gopay","shopeepay","other_va","credit_card"],
  "expiry": {"start_time":"...","unit":"minutes","duration":60},
  "callbacks": {"finish":"https://auraquina.id/pesanan/{kode}"}
}
```
- Response: `{token, redirect_url}` → simpan `snap_token` di `payment_transactions`.

#### 7.1.3 Webhook Signature
Signature = `sha512(order_id + status_code + gross_amount + server_key)` → bandingkan dengan `signature_key` dalam payload.

Algoritma `mapStatus`:
| transaction_status | fraud_status | Internal |
|---|---|---|
| capture | accept | `paid` |
| settlement | * | `paid` |
| pending | * | tetap `pending_payment` |
| deny | * | `failure` |
| cancel | * | `cancelled` |
| expire | * | `expired` |
| refund | * | `refunded` |

Idempotency: cek `payment_transactions` dgn `(order_id, transaction_id, transaction_status)` last seen → skip jika identik.

#### 7.1.4 Refund
`POST {server}/v2/{order_id}/refund` dengan body `{refund_key, amount, reason}`.

### 7.2 Biteship

#### 7.2.1 Config
```
BITESHIP_API_KEY=
BITESHIP_BASE=https://api.biteship.com/v1
SHIPPING_ORIGIN_AREA_ID=IDNP6IDNC149IDND2189IDZ65141
SHIPPING_ORIGIN_LAT=
SHIPPING_ORIGIN_LNG=
```

#### 7.2.2 Endpoints
- `GET /maps/areas?countries=ID&input=...` — autocomplete.
- `POST /rates/couriers` — request rate (origin_area_id, destination_area_id, couriers, items[]).
- `POST /orders` — create shipment.
- `GET /trackings/{tracking_id}` — status.
- Webhook: optional event subscribe ke `/webhook/biteship`.

#### 7.2.3 Mapping Item
```json
{
  "name": "{produk.nama} - {varian.label}",
  "description": "...",
  "value": 489000,
  "weight": 350,
  "quantity": 1
}
```

### 7.3 Fonnte (WhatsApp)

```
WA_PROVIDER=fonnte
WA_TOKEN=
WA_DEVICE=...
```
- `POST https://api.fonnte.com/send` body: `target`, `message`, `delay`.
- Rate limit per device; gunakan queue.

### 7.4 SMTP

Provider rekomendasi: Postmark / SendGrid. Wajib SPF, DKIM, DMARC pada domain `mail.auraquina.id`.

### 7.5 CDN Image

Path prefix dari CloudFront existing dipakai untuk produk legacy. Asset internal (logo, og:image) dibungkus Cloudflare.

### 7.6 Sentry

`SENTRY_LARAVEL_DSN` di `.env`. Sample rate 0.2 di prod.

---

## 8. STATE MACHINES

### 8.1 Order Status

```
[NEW]
  └─► pending_payment ──(webhook paid)──► paid ──(admin processing)──► processing
        │                                  │
        │ (timer expired_at)                └──(admin packed)──► packed ──(admin shipped+AWB)──► shipped
        ▼                                                                               │
      expired                                                                           ├─(carrier delivered)──► delivered
        │                                                                               │
        │ (admin/customer cancel)                                                       │
        ▼                                                                               │
      cancelled                                                                         ├─(customer confirm or 7d auto)──► completed
                                                                                         │
                                                                                         └─(customer return req <3d)──► return_requested ──► refunded
```

Transitions allowed table:

| from \ to | pending_payment | paid | processing | packed | shipped | delivered | completed | cancelled | expired | return_requested | refunded |
|---|---|---|---|---|---|---|---|---|---|---|---|
| pending_payment | — | webhook | — | — | — | — | — | customer/admin | timer | — | — |
| paid | — | — | admin | — | — | — | — | admin | — | — | admin |
| processing | — | — | — | admin | — | — | — | admin | — | — | admin |
| packed | — | — | — | — | admin+AWB | — | — | admin | — | — | admin |
| shipped | — | — | — | — | — | carrier/admin | — | — | — | — | admin |
| delivered | — | — | — | — | — | — | customer/auto | — | — | customer<3d | — |
| completed | — | — | — | — | — | — | — | — | — | customer<3d | — |
| return_requested | — | — | — | — | — | — | — | — | — | — | admin |

### 8.2 Payment Transaction Status

```
pending ──► settlement(paid)
        ├─► expire
        ├─► cancel
        ├─► deny
        └─► refund
```

### 8.3 Cart Item Lifecycle

`created → updated* → checked_out|removed|expired(30d)`.

---

## 9. CONCURRENCY & STOCK LOCKING

### 9.1 Strategi
**Stock-hold + transactional decrement.**

- Saat `placeOrder`: per varian, eksekusi:
  ```sql
  BEGIN;
  SELECT stok FROM varian_produks WHERE id=? FOR UPDATE;
  -- application: tersedia = stok - active_holds_sum
  IF tersedia >= jumlah THEN
    INSERT INTO stock_holds (varian_id, jumlah, expires_at=now()+30min, ...);
  ELSE
    ABORT(409 STOCK_INSUFFICIENT);
  END;
  COMMIT;
  ```
- Saat `markPaid` (webhook):
  ```sql
  BEGIN;
  SELECT stok FROM varian_produks WHERE id=? FOR UPDATE;
  IF stok >= jumlah THEN
    UPDATE varian_produks SET stok=stok-jumlah WHERE id=?;
    DELETE FROM stock_holds WHERE id=?;
  ELSE
    ROLLBACK; mark order failure_oversold + alert admin;
  END;
  COMMIT;
  ```
- Cron tiap 1 menit:
  ```sql
  DELETE FROM stock_holds WHERE expires_at < now();
  ```

### 9.2 Idempotency
- Webhook handler: menggunakan `payment_transactions` table. Insert baru untuk tiap event distinctby `(provider, order_id, transaction_id, transaction_status)`. Status update order hanya jika belum dalam state final.

### 9.3 Rate Limiting
- Auth login: throttle 6/menit (existing).
- Webhook: tidak throttle, namun verify signature ketat.
- API search: 30/menit per IP.
- Voucher validate: 10/menit per session.
- Cart actions: 60/menit per session.

---

## 10. SECURITY SPECIFICATION

### 10.1 Authentication & Session
- Password: argon2id (preferred) atau bcrypt cost ≥ 12.
- Session driver Redis dgn TTL 7 hari (customer), 2 jam (admin).
- Cookie flags: `Secure`, `HttpOnly`, `SameSite=Lax`.
- CSRF token wajib untuk semua POST/PUT/DELETE web.
- Magic-link order tracking: signed URL Laravel, expiry 30 hari.

### 10.2 Authorization
- Policies untuk: `Pesanan::view` (own user / token), admin role.
- Admin role-based via Filament + `spatie/laravel-permission` (Phase 2).

### 10.3 Input Validation
- Semua FormRequest define rules.
- Output escaping default Blade `{{ }}`.
- File upload: type whitelist (png/jpg/webp), size ≤ 5MB, store di `storage/app/public/produk/{yyyy}/{mm}` lalu dipindah CDN.

### 10.4 OWASP Mitigation
| Threat | Mitigation |
|---|---|
| XSS | Blade escape + CSP header + `x-content-type-options: nosniff` |
| CSRF | Laravel CSRF middleware |
| SQL Injection | Eloquent / parameter binding only |
| Mass Assignment | `$fillable` whitelist tiap model |
| IDOR | Policy + ownership check (session/user) |
| SSRF | Outbound HTTP via internal client, no user-supplied URL |
| Open Redirect | Whitelist redirect (`callbacks.finish`) |
| Brute Force | Throttle, captcha (Phase 1.5) |
| Sensitive Data Exposure | TLS only, no logging full card / OTP |
| Webhook Spoofing | Signature validation + IP allowlist (jika provider sediakan) |

### 10.5 Privacy / UU PDP
- Privacy policy + ToS publish di `/privacy`, `/terms`.
- Cookie banner consent (analytics & marketing).
- Data export request (Phase 1.5).
- Data deletion request (Phase 2) — soft-anonymize `pesanans` (drop nama/telepon/alamat, keep aggregate).

### 10.6 Secret Management
- `.env` tidak commit. Gunakan secret manager (Forge env, Hashicorp Vault, atau encrypted SOPS).
- API keys rotate kuartalan.

### 10.7 Compliance Checklist (Pre-Launch)
- [ ] PSE Privat (jika omset / user threshold tercapai)
- [ ] HTTPS dengan HSTS preload
- [ ] DKIM/SPF/DMARC
- [ ] Backup encrypted
- [ ] Pen-test internal pass

---

## 11. PERFORMANCE & SCALABILITY

### 11.1 Performance Targets
- TTFB < 300ms (cached pages)
- LCP p75 mobile < 2.5s
- INP p75 < 200ms
- CLS < 0.1

### 11.2 Optimasi
- Page cache pada GET `/`, `/shop`, `/shop/{slug}` via Cloudflare edge atau Laravel response cache (TTL 5–10 menit, purge on admin update via tag).
- DB query: eager-load relasi (`with(['gambarUtama','varians'])`), index proper.
- Image: WebP + responsive `srcset` + lazy loading offscreen.
- Asset: Vite chunk + preload critical CSS.
- Queue: notif & PDF di Redis queue (worker Horizon).

### 11.3 Scalability
- Web: 2 instance behind LB, sticky session by Redis.
- DB: master-replica (read-only replica untuk listing), connection pool.
- Queue: 2 worker container scale by length.
- Cache: Redis cluster (Phase scale).

### 11.4 Capacity Plan (Year 1)
- Peak target: 200 RPS.
- Order/hari: ≤ 500.
- Image storage: 50 GB tahun pertama, prune resolusi besar.

---

## 12. LOGGING, MONITORING, OBSERVABILITY

### 12.1 Logging
- Laravel log channel `daily` + `sentry`.
- Format: JSON structured (timestamp, level, message, context, request_id).
- Sensitive masking: never log `password`, `card_number`, `cvv`, `otp`.

### 12.2 Metrics
- Application: requests/sec, error rate, queue length, job duration.
- Business: order count, GMV, conversion (server-side calc per hari).
- Infra: CPU, RAM, DB IOPS, Redis hit rate.

### 12.3 Alerts
| Alert | Trigger | Channel |
|---|---|---|
| 5xx > 1% selama 5m | Prometheus | Slack + WA |
| Queue length > 1000 | Horizon | Slack |
| Webhook fail > 5 dlm 10m | App log | WA |
| Disk > 85% | Infra | Slack |
| Order anomaly (spike/drop > 50%) | Metrics | Email founder |

### 12.4 Tracing
- Sentry performance trace di endpoint kritis: checkout, webhook, place-order, search.

---

## 13. TESTING STRATEGY

### 13.1 Test Types
| Layer | Tool | Target Coverage |
|---|---|---|
| Unit | PHPUnit/Pest | service & utils ≥ 80% |
| Feature | Laravel HTTP test | controller + policy |
| Browser | Pest Browser / Playwright (existing playwright deps di node_modules) | smoke checkout, PDP swipe |
| Contract | Pact / Postman against gateway sandbox | Midtrans, Biteship |
| Load | k6 | 200 RPS scenario |
| Security | OWASP ZAP baseline scan | weekly CI |

### 13.2 Skenario Wajib
1. Add to cart concurrent (>50 paralel) — no oversell.
2. Buy now flow happy path.
3. Cart checkout multi-item happy path.
4. Webhook duplicate received — idempotent.
5. Webhook out-of-order (paid before pending).
6. Auto-expire saat batas_bayar lewat.
7. Refund partial.
8. Voucher edge: minimum subtotal not met.
9. Free-ship threshold tepat di Rp 500.000.
10. Search XSS payload.
11. Mobile gallery swipe to next color (PDP).

### 13.3 CI Pipeline
```
push/PR → lint (php-cs-fixer, prettier) → unit + feature tests → build vite → ZAP baseline → if main: deploy to staging
manual approve → deploy to prod (atomic) → smoke check → notify Slack
```

---

## 14. DEPLOYMENT & DEVOPS

### 14.1 Environment

| Env | Purpose |
|---|---|
| local | dev (SQLite, mailpit, Midtrans sandbox) |
| staging | UAT (MySQL, Midtrans sandbox, Biteship sandbox) |
| production | Live |

### 14.2 Deploy Strategy
- Forge atau GitHub Actions → SSH deploy.
- Atomic releases (`/var/www/auraquina/releases/{ts}` simlink `current`).
- Steps: `composer install --no-dev`, `npm ci && npm run build`, `php artisan migrate --force`, `php artisan optimize`, restart PHP-FPM, restart queue worker.
- Zero-downtime: simlink swap + warm cache.

### 14.3 Backup
- DB dump terjadwal tiap 1 jam ke S3 (encrypted, retention 30 hari + monthly snapshot 12 bln).
- Storage backup harian.
- Restore drill kuartalan.

### 14.4 Rollback
- Revert simlink ke release sebelumnya.
- DB migration must be reversible (atau ada compensating migration).

---

## 15. CONFIGURATION MANAGEMENT

### 15.1 ENV Required

```
APP_NAME=Auraquina
APP_ENV=production
APP_KEY=
APP_URL=https://auraquina.id
APP_DEBUG=false

DB_CONNECTION=mysql
DB_HOST=
DB_PORT=3306
DB_DATABASE=auraquina
DB_USERNAME=
DB_PASSWORD=

REDIS_HOST=
REDIS_PASSWORD=
REDIS_PORT=6379
REDIS_DB=0
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
CACHE_STORE=redis

MAIL_MAILER=postmark
POSTMARK_TOKEN=
MAIL_FROM_ADDRESS=hello@auraquina.id
MAIL_FROM_NAME="Auraquina"

MIDTRANS_MERCHANT_ID=
MIDTRANS_CLIENT_KEY=
MIDTRANS_SERVER_KEY=
MIDTRANS_IS_PRODUCTION=true
MIDTRANS_NOTIFICATION_URL=https://auraquina.id/webhook/midtrans

BITESHIP_API_KEY=
BITESHIP_BASE=https://api.biteship.com/v1
SHIPPING_ORIGIN_AREA_ID=

WA_PROVIDER=fonnte
WA_TOKEN=
WA_FROM=628113662636

SENTRY_LARAVEL_DSN=
SENTRY_TRACES_SAMPLE_RATE=0.2

ANALYTICS_GA4_ID=
ANALYTICS_META_PIXEL=
ANALYTICS_TIKTOK_PIXEL=

FREE_SHIPPING_THRESHOLD=500000
ORDER_PAYMENT_DEADLINE_MINUTES=60
DELIVERED_AUTO_COMPLETE_DAYS=7
```

### 15.2 Feature Flags (config/features.php)

```php
return [
  'wa_notification' => env('FEATURE_WA', true),
  'voucher' => env('FEATURE_VOUCHER', false),
  'reviews' => env('FEATURE_REVIEWS', false),
  'cod_payment' => env('FEATURE_COD', false),
  'wishlist_server' => env('FEATURE_WISHLIST_SERVER', false),
];
```

---

## 16. ERROR HANDLING CATALOG

| Code | HTTP | Pesan ID | Recovery UI |
|---|---|---|---|
| `STOCK_INSUFFICIENT` | 422 | "Stok tidak mencukupi" | banner + back to PDP |
| `VARIAN_INVALID` | 422 | "Varian tidak tersedia" | refresh PDP |
| `VOUCHER_INVALID` | 422 | "Kode tidak berlaku" | input kembali |
| `VOUCHER_EXPIRED` | 422 | "Voucher kadaluwarsa" | input kembali |
| `VOUCHER_MIN_SUBTOTAL` | 422 | "Minimum pembelian Rp X" | tambah item |
| `ORDER_EXPIRED` | 410 | "Batas waktu pembayaran terlewati" | order ulang |
| `ORDER_STATE_INVALID` | 409 | "Aksi tidak diizinkan untuk status ini" | refresh page |
| `PAYMENT_GATEWAY_DOWN` | 503 | "Gateway pembayaran sedang gangguan" | retry / pilih metode lain |
| `SHIPPING_RATE_NOT_FOUND` | 422 | "Tidak ada layanan pengiriman ke alamat ini" | edit alamat |
| `WEBHOOK_SIGNATURE_INVALID` | 401 | (silent) | log + alert admin |
| `RATE_LIMITED` | 429 | "Terlalu banyak percobaan, coba lagi 1 menit" | wait |

---

## 17. BACKGROUND JOBS & SCHEDULES

### 17.1 Queued Jobs

| Job | Trigger | Queue | Retry |
|---|---|---|---|
| `SendOrderEmail` | event `OrderCreated` / `OrderPaid` etc | `notification` | 3x backoff |
| `SendOrderWhatsapp` | same | `notification-wa` | 3x backoff |
| `GenerateInvoicePdf` | event `OrderPaid` | `pdf` | 3x |
| `RequestShipmentLabel` | event `OrderPacked` | `shipping` | 3x |
| `RetryFailedWebhook` | scheduled | `webhook-retry` | manual |

### 17.2 Schedules (`app/Console/Kernel.php`)

| Cron | Action |
|---|---|
| `* * * * *` | Expire pending orders past `batas_bayar` |
| `* * * * *` | Release expired stock_holds |
| `0 * * * *` | Cleanup carts > 30 days |
| `*/30 * * * *` | Re-sync shipping status untuk shipped orders |
| `0 3 * * *` | DB nightly maintenance |
| `0 9 * * *` | Daily report email ke ops |
| `0 10 * * *` | Auto-complete delivered > 7 days |
| `*/15 * * * *` | Cancel `delivered` reminder if no action |

---

## 18. ACCEPTANCE & DEFINITION OF DONE

### 18.1 Per Feature
- [ ] Code review approved by ≥1 reviewer.
- [ ] Tests added (unit + feature).
- [ ] Lint pass.
- [ ] Manual QA pass at staging.
- [ ] Documentation update (README/CLAUDE.md jika relevan).

### 18.2 Release Gate
- [ ] All P0 FR pass acceptance.
- [ ] Load test 200 RPS pass.
- [ ] Security scan zero high.
- [ ] Backup verified.
- [ ] Rollback plan tested.
- [ ] Monitoring & alerts active.
- [ ] Privacy/ToS published.
- [ ] Stakeholder sign-off.

---

## 19. GLOSSARY

| Istilah | Definisi |
|---|---|
| AOV | Average Order Value — rata-rata nilai per transaksi |
| AWB | Airway Bill — nomor resi pengiriman |
| BRD | Business Requirements Document |
| CSF | Critical Success Factor |
| CSRF | Cross-Site Request Forgery |
| CWV | Core Web Vitals |
| DPA | Data Processing Agreement |
| GMV | Gross Merchandise Value |
| IDOR | Insecure Direct Object Reference |
| INP | Interaction to Next Paint |
| JTBD | Jobs To Be Done |
| KPI | Key Performance Indicator |
| LCP | Largest Contentful Paint |
| MoSCoW | Must / Should / Could / Won't prioritization |
| NPS | Net Promoter Score |
| OTP | One-Time Password |
| OWASP | Open Web Application Security Project |
| P0 | Priority 0 — wajib MVP |
| P1 | Priority 1 — Phase 2 |
| P2 | Priority 2 — Nice to have |
| PDP | Product Detail Page |
| PRD | Product Requirements Document |
| PSE | Penyelenggara Sistem Elektronik |
| RACI | Responsible/Accountable/Consulted/Informed |
| RPO | Recovery Point Objective |
| RTO | Recovery Time Objective |
| SLA | Service Level Agreement |
| SOP | Standard Operating Procedure |
| SRS | Software Requirements Specification |
| TTFB | Time to First Byte |
| UU PDP | Undang-Undang Pelindungan Data Pribadi |
| UX | User Experience |
| VA | Virtual Account |
| WCAG | Web Content Accessibility Guidelines |
| XSS | Cross-Site Scripting |

---

## CHANGE LOG

| Versi | Tanggal | Catatan |
|---|---|---|
| 1.0 | 2026-05-24 | Initial SRS, sinkron dengan PRD 1.0 dan BRD 2.0 |
