# AURAQUINA — Feature Implementation Audit & Prioritization Matrix

| **Atribut** | **Detail** |
|---|---|
| **Nama Dokumen** | Feature Implementation Audit & Prioritization Matrix |
| **Nama Project** | Auraquina E-Commerce Platform |
| **Versi Dokumen** | 1.0 |
| **Tanggal** | 06 Juni 2026 |
| **Status** | Draft for Review |
| **Author** | Product & Engineering Team |
| **Approver** | Founder / Business Owner |
| **Klasifikasi** | Internal - Confidential |
| **Referensi** | `docs/BRD-PRD.md`, `docs/PRD.md`, `docs/SRS.md` |

> Dokumen ini merangkum **audit implementasi fitur saat ini**, **gap produk yang belum terealisasi atau masih parsial**, dan **urutan prioritas eksekusi** yang lebih operasional daripada BRD/PRD. Fokus dokumen ini adalah membantu keputusan delivery, sequencing, dan alignment lintas product-engineering.

---

## DAFTAR ISI

1. [Tujuan Dokumen](#1-tujuan-dokumen)
2. [Ruang Lingkup Audit](#2-ruang-lingkup-audit)
3. [Ringkasan Kondisi Saat Ini](#3-ringkasan-kondisi-saat-ini)
4. [Matriks Prioritas Fitur](#4-matriks-prioritas-fitur)
5. [Detail Rekomendasi per Fitur](#5-detail-rekomendasi-per-fitur)
6. [Urutan Eksekusi yang Disarankan](#6-urutan-eksekusi-yang-disarankan)
7. [Keterkaitan dengan Dokumen Lain](#7-keterkaitan-dengan-dokumen-lain)
8. [Catatan Audit](#8-catatan-audit)

---

## 1. TUJUAN DOKUMEN

Dokumen ini dibuat untuk:

1. Memisahkan **fitur yang benar-benar sudah fungsional** dari fitur yang baru berupa UI, placeholder, atau implementasi parsial.
2. Menyusun **prioritas eksekusi** berdasarkan kebutuhan bisnis, impact ke customer journey, dan kesiapan fondasi teknis.
3. Menjadi artefak kerja product-engineering yang bisa dipakai bersama dengan BRD, PRD, dan SRS.
4. Memberi panduan sequencing implementasi tanpa mencampur semua kebutuhan ke satu dokumen strategis besar.

---

## 2. RUANG LINGKUP AUDIT

### 2.1 Yang Diaudit

- Route storefront dan account
- Controller customer-facing
- Blade views utama
- Frontend behavior di `resources/js/app.js`
- Model dan flow order/voucher yang relevan
- Coverage test yang tersedia

### 2.2 Yang Tidak Masuk Prioritas Dokumen Ini

Sesuai arahan audit ini, item berikut **sengaja dikeluarkan dari ranking utama**:

- Payment gateway integration
- Shipping / ongkir gateway integration

Keduanya tetap boleh dibahas di BRD/PRD/SRS, tetapi **tidak diprioritaskan di matriks dokumen ini**.

---

## 3. RINGKASAN KONDISI SAAT INI

### 3.1 Fitur yang Sudah Ada

| Area | Status | Catatan Singkat |
|---|---|---|
| Homepage / brand storefront | Sudah ada | Hero, featured products, lookbook, CTA, responsive layout |
| Shop / katalog | Sudah ada | Search overlay, filter kategori/ukuran/warna/harga, sort |
| Product detail | Sudah ada | Gallery, pemilihan ukuran/warna, qty, add to cart, buy now |
| Cart | Sudah ada | Session-based cart, update qty, hapus item, checkout selected |
| Checkout dasar | Sudah ada | Address form, voucher apply, place order, ringkasan pesanan |
| Order creation | Sudah ada | Order dan item order sudah persist ke DB |
| Voucher apply | Sudah ada | Voucher bisa diaplikasikan di checkout |
| Auth dasar | Sudah ada | Login, register, logout |
| Order detail page | Sudah ada | Signed access, cancel, confirm received, countdown |

### 3.2 Temuan Utama

| Temuan | Implikasi |
|---|---|
| Beberapa area account masih berupa tampilan statis | Customer belum punya self-service account yang layak |
| Order engine sudah lumayan, tetapi order center di account belum surfaced | User harus mengandalkan link order detail, bukan dashboard akun |
| Review produk belum menjadi modul nyata | Trust dan conversion support masih lemah |
| Wishlist masih browser-local | Tidak persisten lintas device / login |
| Banyak CTA informasional masih placeholder | Trust content dan customer guidance belum lengkap |
| Test coverage customer flow masih tipis | Risiko regresi masih tinggi saat fitur berkembang |

---

## 4. MATRIKS PRIORITAS FITUR

### 4.1 Definisi Skala

| Skala | Arti |
|---|---|
| **Impact** | Dampak ke conversion, retention, trust, atau operasional |
| **Effort** | Estimasi kasar implementasi: S / M / L |
| **Priority** | P1 = paling dibutuhkan, lalu P2, P3 |

### 4.2 Ranking Utama

| Rank | Fitur | Kondisi Sekarang | Customer Impact | Business / Ops Impact | Effort | Priority | Alasan Prioritas |
|---|---|---|---|---|---|---|---|
| 1 | Manajemen akun pelanggan | Area account masih dominan statis; profile, delivery info, dan account information belum benar-benar editable | Tinggi | Tinggi | M | P1 | Ini fondasi self-service customer dan identitas akun |
| 2 | Order history / order center | Data order sudah disiapkan di backend, tetapi belum dirender sebagai pusat pesanan di akun | Tinggi | Tinggi | M | P1 | Customer perlu melihat histori dan status order dari dashboard akun |
| 3 | Retur / refund / after-sales flow | State machine order sudah mengarah ke return/refund, tetapi belum ada flow customer-facing | Tinggi | Tinggi | M | P1 | Sangat penting untuk trust dan efisiensi CS |
| 4 | Review / ulasan produk | UI menampilkan rating/ulasan statis, tetapi belum ada modul review yang nyata | Tinggi | Tinggi | M | P1 | Social proof langsung memengaruhi conversion |
| 5 | Recovery akun | Login/register ada, tetapi forgot/reset password belum terlihat sebagai flow lengkap | Sedang | Tinggi | M | P1 | Akan cepat dibutuhkan begitu user base tumbuh |
| 6 | Wishlist persisten lintas login/device | Saat ini wishlist berbasis `localStorage` | Sedang | Sedang | M | P2 | Penting untuk retention dan continuity customer |
| 7 | Pagination katalog yang proper | Shop masih mengambil produk penuh tanpa pagination backend yang jelas | Sedang | Sedang | M | P2 | Penting untuk scale katalog dan performa listing |
| 8 | Admin management voucher/promo | Voucher apply ada, tetapi pengelolaan promo belum surfaced sebagai workflow admin yang jelas | Rendah bagi customer langsung | Tinggi | M | P2 | Dibutuhkan untuk operasi campaign yang rapi |
| 9 | Content / trust pages yang nyata | Size guide, terms, policy, FAQ, about, dan beberapa CTA masih placeholder | Sedang | Sedang | S-M | P2 | Mengurangi friction, komplain, dan keraguan pembeli |

### 4.3 Fitur yang Tidak Masuk Ranking Utama

| Fitur | Status dalam Dokumen Ini | Catatan |
|---|---|---|
| Payment gateway integration | Dikeluarkan dari prioritas | Sesuai arahan audit |
| Shipping / ongkir gateway | Dikeluarkan dari prioritas | Sesuai arahan audit |

---

## 5. DETAIL REKOMENDASI PER FITUR

### 5.1 Manajemen Akun Pelanggan

**Masalah saat ini**
- Delivery info masih bersifat statis / placeholder.
- Edit profile belum menjadi flow update data yang nyata.
- Account information masih lebih dekat ke static page daripada settings center.

**Outcome yang dibutuhkan**
- Customer bisa mengubah profil dasar.
- Customer bisa menyimpan dan mengelola alamat.
- Customer punya account area yang benar-benar berguna, bukan hanya tampilan.

**Kenapa didahulukan**
- Menjadi fondasi untuk order history, wishlist persisten, dan fitur account lain.

### 5.2 Order History / Order Center

**Masalah saat ini**
- Backend sudah menyiapkan data order, tetapi belum surfaced rapi di dashboard akun.
- Route account orders belum membentuk pengalaman pusat pesanan yang utuh.

**Outcome yang dibutuhkan**
- Halaman daftar order.
- Filter status order.
- Shortcut ke detail pesanan dan aksi yang relevan.

**Kenapa didahulukan**
- Mengurangi ketergantungan customer ke CS dan meningkatkan rasa kontrol.

### 5.3 Retur / Refund / After-Sales Flow

**Masalah saat ini**
- Domain status order sudah mengarah ke return/refund, tetapi belum ada experience customer untuk memakainya.

**Outcome yang dibutuhkan**
- Customer bisa mengajukan retur / komplain pasca-pembelian.
- Admin punya status yang jelas untuk menindaklanjuti.

**Kenapa didahulukan**
- Setelah order aktif, trust pasca-pembelian jadi pembeda kualitas brand.

### 5.4 Review / Ulasan Produk

**Masalah saat ini**
- Ada indikasi rating di UI, tetapi belum ada modul review terstruktur.

**Outcome yang dibutuhkan**
- Customer bisa memberi rating dan ulasan.
- Produk bisa menampilkan social proof nyata.

**Kenapa didahulukan**
- Sangat berpengaruh ke conversion pada fashion e-commerce.

### 5.5 Recovery Akun

**Masalah saat ini**
- Flow login/register ada, tetapi recovery akun belum jelas surfaced.

**Outcome yang dibutuhkan**
- Forgot password.
- Reset password.
- Feedback yang jelas saat akun terkunci / lupa kredensial.

**Kenapa didahulukan**
- Kebutuhan standar untuk produk yang mulai dipakai user riil.

### 5.6 Wishlist Persisten

**Masalah saat ini**
- Wishlist hanya tersimpan di browser lokal.

**Outcome yang dibutuhkan**
- Wishlist tersimpan per user.
- Wishlist tetap ada saat login dari device lain.

**Kenapa belum P1**
- Penting, tapi masih bisa berjalan secara minimum untuk tahap awal karena already usable secara lokal.

### 5.7 Pagination Katalog

**Masalah saat ini**
- Listing produk masih condong ke render penuh, belum siap scale untuk katalog yang membesar.

**Outcome yang dibutuhkan**
- Pagination backend atau strategi incremental loading yang konsisten.
- Listing tetap cepat saat SKU bertambah.

**Kenapa belum P1**
- Menjadi kritis saat jumlah produk tumbuh, bukan blocker paling dekat untuk usability inti.

### 5.8 Admin Voucher / Promo Management

**Masalah saat ini**
- Voucher sudah bisa dipakai customer, tetapi pengelolaan internal belum tampak matang.

**Outcome yang dibutuhkan**
- Create/edit/disable voucher.
- Tracking penggunaan voucher.

**Kenapa belum P1**
- Dampak customer tidak secepat area account dan after-sales.

### 5.9 Content / Trust Pages

**Masalah saat ini**
- Beberapa halaman atau CTA trust masih placeholder.

**Outcome yang dibutuhkan**
- Terms & conditions.
- Return policy.
- Shipping policy.
- Size guide.
- FAQ.
- About / brand story yang nyata.

**Kenapa penting**
- Menurunkan keraguan pembeli, mengurangi pertanyaan berulang, dan memperkuat brand trust.

---

## 6. URUTAN EKSEKUSI YANG DISARANKAN

### 6.1 Sequence Delivery

| Fase | Fokus | Isi Utama |
|---|---|---|
| Phase A | Account foundation | Manajemen akun pelanggan + recovery akun |
| Phase B | Order self-service | Order center + after-sales flow |
| Phase C | Conversion trust | Review produk + trust pages |
| Phase D | Retention & operations | Wishlist persisten + admin voucher management |
| Phase E | Catalog scale | Pagination / listing scalability |

### 6.2 Quick Wins

| Quick Win | Nilai |
|---|---|
| Trust pages nyata | Cepat dikerjakan, langsung menaikkan trust |
| Surface order center | Memanfaatkan backend yang sudah ada |
| Recovery akun | Fitur standar dengan impact tinggi |

---

## 7. KETERKAITAN DENGAN DOKUMEN LAIN

| Dokumen | Peran | Hubungan dengan Dokumen Ini |
|---|---|---|
| `docs/BRD-PRD.md` | Arah bisnis dan kebutuhan makro | Dokumen ini memecah kebutuhan menjadi urutan kerja yang lebih operasional |
| `docs/PRD.md` | Product requirement dan scope | Dokumen ini memperjelas mana yang masih gap di implementasi aktual |
| `docs/SRS.md` | Spesifikasi teknis | Dokumen ini membantu menentukan modul teknis mana yang perlu dinaikkan prioritasnya |

### 7.1 Rekomendasi Sinkronisasi Dokumen

Setelah dokumen ini direview, disarankan:

1. Menyamakan section audit/gap di `PRD.md` dengan kondisi implementasi terkini.
2. Menambahkan referensi silang dari PRD atau BRD ke dokumen audit ini.
3. Menggunakan ranking di dokumen ini sebagai dasar backlog atau milestone engineering.

---

## 8. CATATAN AUDIT

### 8.1 Metode Audit

Audit ini disusun dari inspeksi read-only terhadap:

- `routes/web.php`
- controller customer-facing
- views utama di `resources/views/`
- frontend behavior di `resources/js/app.js`
- model order dan voucher
- test feature yang tersedia

### 8.2 Batasan Audit

- Audit ini tidak menilai kualitas desain visual secara mendalam.
- Audit ini tidak memasukkan prioritas payment gateway dan shipping gateway ke ranking utama.
- Audit ini fokus pada fitur yang **belum terealisasi penuh**, bukan seluruh roadmap produk.

### 8.3 Status Dokumen

Dokumen ini sebaiknya diperlakukan sebagai:

- artefak audit implementasi,
- acuan prioritas backlog,
- dokumen pendamping BRD/PRD/SRS,

bukan pengganti dokumen requirement strategis utama.
