# AURAQUINA — Admin Dashboard Feature Roadmap & Execution Plan

| **Atribut** | **Detail** |
|---|---|
| **Nama Dokumen** | Admin Dashboard Feature Roadmap & Execution Plan |
| **Nama Project** | Auraquina E-Commerce Platform |
| **Versi Dokumen** | 1.0 |
| **Tanggal** | 09 Juni 2026 |
| **Status** | Draft for Review |
| **Author** | Product & Engineering Team |
| **Approver** | Founder / Business Owner |
| **Klasifikasi** | Internal - Confidential |
| **Referensi** | `docs/BRD-PRD.md`, `docs/FEATURE-AUDIT.md`, `docs/SRS.md` |

> Dokumen ini merangkum **planning implementasi dashboard admin Auraquina**, termasuk **fitur yang direkomendasikan**, **fitur yang dibutuhkan tetapi belum ada**, **urutan pengerjaan**, dan **fondasi teknis yang harus disiapkan** agar panel admin berkembang dari dashboard operasional dasar menjadi pusat kontrol bisnis yang layak pakai harian.

---

## DAFTAR ISI

1. [Tujuan Dokumen](#1-tujuan-dokumen)
2. [Kondisi Admin Saat Ini](#2-kondisi-admin-saat-ini)
3. [Sasaran Admin Dashboard](#3-sasaran-admin-dashboard)
4. [Prinsip Prioritisasi](#4-prinsip-prioritisasi)
5. [Daftar Fitur yang Direkomendasikan](#5-daftar-fitur-yang-direkomendasikan)
6. [Fitur yang Dibutuhkan tetapi Belum Ada](#6-fitur-yang-dibutuhkan-tetapi-belum-ada)
7. [Roadmap Implementasi Bertahap](#7-roadmap-implementasi-bertahap)
8. [Functional Requirements per Modul](#8-functional-requirements-per-modul)
9. [Fondasi Data & Teknis yang Harus Disiapkan](#9-fondasi-data--teknis-yang-harus-disiapkan)
10. [Risiko dan Dependency](#10-risiko-dan-dependency)
11. [Rekomendasi Eksekusi](#11-rekomendasi-eksekusi)
12. [Definition of Done per Phase](#12-definition-of-done-per-phase)

---

## 1. TUJUAN DOKUMEN

Dokumen ini dibuat untuk:

1. Menyusun roadmap implementasi admin panel secara realistis dan bertahap.
2. Memisahkan fitur yang **bagus untuk dimiliki** dari fitur yang **benar-benar dibutuhkan operasional**.
3. Menjadi panduan delivery untuk pengembangan modul admin berikutnya.
4. Membantu owner menentukan urutan kerja dengan impact bisnis paling tinggi.

---

## 2. KONDISI ADMIN SAAT INI

### 2.1 Modul yang Sudah Ada

| Modul | Status | Catatan |
|---|---|---|
| Dashboard stats dasar | Sudah ada | Menampilkan ringkasan order, pendapatan harian, stok rendah, produk aktif |
| Kategori | Sudah ada | CRUD melalui Filament |
| Produk | Sudah ada | CRUD dasar dan relasi gambar/varian |
| Stok | Sudah ada | Halaman penyesuaian stok |
| Pesanan | Sudah ada | Listing dan update order dasar |
| User | Sudah ada | Resource tersedia, akses role-dependent |
| Role | Sudah ada | Resource tersedia, akses role-dependent |

### 2.2 Temuan Utama

| Temuan | Implikasi |
|---|---|
| Dashboard masih fokus statistik ringkas | Belum menjadi command center operasional |
| Belum ada modul customer | Admin tidak punya visibilitas customer lifecycle |
| Voucher/promo belum surfaced sebagai workflow admin | Campaign marketing tidak terkelola dari panel |
| Belum ada reporting yang proper | Sulit ambil keputusan berbasis data |
| Belum ada after-sales center | Refund, retur, komplain berisiko jadi proses manual |
| Belum ada audit trail admin yang kuat | Sulit lacak siapa mengubah stok, order, harga |
| Belum ada notifikasi kerja | Admin harus aktif memantau manual |

---

## 3. SASARAN ADMIN DASHBOARD

Admin dashboard Auraquina seharusnya menjadi:

1. **Pusat monitoring bisnis harian**
2. **Pusat operasi pesanan dan stok**
3. **Pusat kontrol promo, customer, dan after-sales**
4. **Pusat pelaporan owner**

Dengan kata lain, dashboard tidak cukup hanya menampilkan angka. Dashboard harus membantu admin **tahu apa yang perlu dikerjakan sekarang**, **apa yang bermasalah**, dan **apa yang paling berdampak ke revenue**.

---

## 4. PRINSIP PRIORITISASI

Urutan prioritas di dokumen ini memakai prinsip berikut:

1. **Impact ke operasional harian** lebih penting dari kosmetik UI.
2. **Fitur yang mengurangi kerja manual** didahulukan.
3. **Fitur yang membuka visibilitas revenue, order, dan customer** didahulukan.
4. **Fitur analitik lanjut** dikerjakan setelah data dasarnya rapi.
5. **Integrasi eksternal** dilakukan setelah workflow internal jelas.

---

## 5. DAFTAR FITUR YANG DIREKOMENDASIKAN

### 5.1 Dashboard Widgets / Insight

| Fitur | Priority | Alasan |
|---|---|---|
| Omzet hari ini / minggu ini / bulan ini | P1 | Owner perlu lihat revenue cepat |
| Jumlah order per status | P1 | Admin perlu tahu antrean kerja |
| Pending payment | P1 | Menentukan follow-up pembayaran |
| Perlu diproses / dikirim | P1 | Inti workflow fulfillment |
| Stok rendah / stok habis | P1 | Mencegah oversell dan lost sales |
| Produk terlaris 7/30 hari | P2 | Membantu restock dan promosi |
| Kategori terlaris | P2 | Membantu strategi merchandising |
| Customer baru vs repeat customer | P2 | Indikator akuisisi vs retensi |
| Order melewati SLA | P2 | Bantu kontrol kualitas operasional |
| Grafik tren penjualan 7/30 hari | P2 | Baca momentum bisnis |
| Quick actions | P1 | Mempercepat kerja admin harian |

### 5.2 Quick Actions yang Disarankan

- Tambah produk
- Buka pesanan pending payment
- Buka pesanan perlu diproses
- Buka stok rendah
- Buat voucher
- Lihat customer baru

---

## 6. FITUR YANG DIBUTUHKAN TETAPI BELUM ADA

### 6.1 Prioritas P1

| Modul | Kenapa Dibutuhkan |
|---|---|
| Customer Management | Untuk lihat customer, histori belanja, total spend, repeat order |
| Voucher / Promo Management | Untuk workflow campaign internal yang sekarang belum surfaced |
| Reporting Penjualan | Untuk owner dan admin membaca performa bisnis |
| After-Sales Management | Untuk retur, refund, komplain, dan penanganan pasca pembelian |
| Admin Notifications / Alert Center | Untuk mengurangi monitoring manual |

### 6.2 Prioritas P2

| Modul | Kenapa Dibutuhkan |
|---|---|
| Audit Log | Untuk kontrol perubahan harga, stok, pesanan |
| Store Settings | Untuk rekening, kontak, kebijakan, pengaturan operasional |
| Content Management sederhana | Untuk banner, featured section, campaign blocks |
| Export CSV/Excel | Untuk kerja keuangan, marketing, operasional |

### 6.3 Prioritas P3

| Modul | Kenapa Dibutuhkan |
|---|---|
| Advanced analytics | Segmentasi customer, cohort, conversion deeper |
| Forecast stok / reorder suggestion | Berguna setelah data order lebih kaya |
| CRM ringan / broadcast list | Relevan setelah customer data dan consent rapi |

---

## 7. ROADMAP IMPLEMENTASI BERTAHAP

## Phase 1 — Operasional Inti Admin

**Tujuan:** menjadikan admin panel berguna untuk pekerjaan harian.

### Scope

1. Dashboard widgets yang actionable
2. Customer Management dasar
3. Voucher / Promo Management
4. Filter dan status order yang lebih lengkap
5. Export dasar untuk order dan customer

### Outcome

- Admin bisa memonitor order dan stok dari satu tempat
- Admin bisa menjalankan promo tanpa utak-atik database
- Owner bisa melihat performa bisnis dasar

### Deliverables

- Widget omzet, order status, stok kritis, top products
- Resource `Customer`
- Resource `Voucher`
- Filter order by status, tanggal, payment state
- Export CSV sederhana

---

## Phase 2 — Kontrol Bisnis & After-Sales

**Tujuan:** mengurangi proses manual setelah order terjadi.

### Scope

1. After-sales center
2. Refund / retur / komplain workflow
3. Audit log admin activity
4. Admin notifications / alert center
5. Reporting yang lebih lengkap

### Outcome

- Tim bisa menangani masalah order dengan status yang jelas
- Owner bisa mengaudit perubahan penting
- Admin mendapat daftar kerja prioritas tanpa cek manual terus-menerus

### Deliverables

- Resource `AfterSalesCase`
- Timeline penanganan kasus
- Log perubahan stok, order, voucher, produk
- Widget dan daftar notifikasi penting
- Laporan penjualan & laporan produk terlaris

---

## Phase 3 — Optimasi & Growth Tools

**Tujuan:** menjadikan admin panel alat growth, bukan hanya alat operasional.

### Scope

1. Customer segmentation ringan
2. Campaign insight
3. Product performance insight
4. Reorder / low stock suggestion
5. Content blocks / merchandising tools

### Outcome

- Owner bisa pakai panel untuk keputusan growth
- Marketing punya alat bantu campaign yang lebih rapih
- Tim bisa merespons tren penjualan lebih cepat

---

## 8. FUNCTIONAL REQUIREMENTS PER MODUL

## 8.1 Dashboard

### FR-DASH-01 Ringkasan Revenue
- Sistem menampilkan omzet hari ini, minggu ini, dan bulan ini.
- Sistem menampilkan total order dan AOV pada periode aktif.

### FR-DASH-02 Order Funnel
- Sistem menampilkan jumlah order per status.
- Admin dapat klik setiap status untuk menuju listing terfilter.

### FR-DASH-03 Stock Attention
- Sistem menampilkan stok rendah dan stok habis.
- Admin dapat langsung menuju halaman stok terfilter.

### FR-DASH-04 Product Insights
- Sistem menampilkan top 5 produk terlaris periode 7/30 hari.
- Sistem menampilkan kategori terlaris.

### FR-DASH-05 Quick Actions
- Sistem menyediakan tombol aksi cepat untuk workflow admin yang paling sering.

## 8.2 Customer Management

### FR-CUST-01 Customer List
- Admin dapat melihat daftar customer.
- Admin dapat mencari customer berdasarkan nama, email, phone.

### FR-CUST-02 Customer Detail
- Admin dapat melihat histori order customer.
- Admin dapat melihat total spend, total order, last order date.

### FR-CUST-03 Customer Segment Flags
- Sistem menandai customer baru, repeat customer, dan high-value customer.

## 8.3 Voucher / Promo Management

### FR-VCH-01 Voucher CRUD
- Admin dapat membuat, mengubah, menonaktifkan voucher.

### FR-VCH-02 Voucher Rules
- Voucher mendukung minimum belanja, masa aktif, kuota, dan tipe diskon.

### FR-VCH-03 Voucher Usage Insight
- Admin dapat melihat jumlah pemakaian dan dampak diskon.

## 8.4 Reporting

### FR-RPT-01 Sales Report
- Sistem menyediakan laporan order dan omzet berdasarkan rentang tanggal.

### FR-RPT-02 Product Report
- Sistem menyediakan laporan produk terlaris dan produk yang tidak bergerak.

### FR-RPT-03 Export
- Admin dapat export data ke CSV/Excel.

## 8.5 After-Sales Management

### FR-AFS-01 Case Creation
- Admin dapat melihat pengajuan after-sales dari customer.

### FR-AFS-02 Case Workflow
- Setiap kasus memiliki status, catatan internal, dan riwayat tindakan.

### FR-AFS-03 Refund / Return Decision
- Admin dapat menandai approve / reject / waiting action.

## 8.6 Notifications & Alerts

### FR-ALR-01 Alert Feed
- Sistem menampilkan alert untuk pending payment, stok kritis, order terlambat, dan kasus after-sales baru.

### FR-ALR-02 Alert Navigation
- Setiap alert dapat membuka halaman terkait.

## 8.7 Audit Log

### FR-AUD-01 Activity Tracking
- Sistem merekam perubahan penting pada order, stok, produk, voucher.

### FR-AUD-02 Actor Visibility
- Log menampilkan siapa yang melakukan aksi dan kapan dilakukan.

---

## 9. FONDASI DATA & TEKNIS YANG HARUS DISIAPKAN

Sebelum semua fitur di atas dikerjakan, fondasi berikut perlu dipastikan:

### 9.1 Data Model
- Tabel customer insight atau derived query untuk total spend dan last order
- Tabel voucher usage yang konsisten
- Tabel / model after-sales case
- Tabel alert / notification internal bila ingin persist

### 9.2 Domain Status
- Standardisasi status order
- Standardisasi status pembayaran
- Standardisasi status after-sales

### 9.3 Permission Matrix
- Permission per modul admin harus jelas
- `owner`, `admin`, `operator_pesanan`, `operator_produk`, `viewer` perlu matriks akses yang tegas

### 9.4 Query & Performance
- Widget dashboard harus memakai agregasi yang efisien
- Hindari query berat berulang di setiap page load
- Siapkan caching untuk statistik periodik bila perlu

### 9.5 UX Admin
- Semua widget penting harus clickable ke halaman detail
- Dashboard harus menampilkan “apa yang harus dikerjakan sekarang”, bukan hanya angka mati

---

## 10. RISIKO DAN DEPENDENCY

| Risiko | Dampak | Mitigasi |
|---|---|---|
| Status order belum seragam | Reporting dan widget jadi misleading | Rapikan enum/status terlebih dahulu |
| Data histori belum lengkap | Insight customer dan report kurang akurat | Definisikan sumber data utama per metrik |
| Query dashboard terlalu berat | Panel admin lambat | Tambah caching dan agregasi periodik |
| Permission tidak lengkap | Menu hilang / akses rancu | Seed dan audit permission matrix |
| Workflow after-sales belum didefinisikan bisnis | Implementasi berisiko salah arah | Finalisasi SOP bisnis dulu |

---

## 11. REKOMENDASI EKSEKUSI

### 11.1 Urutan Kerja yang Disarankan

1. Rapikan metrik dashboard inti
2. Bangun Customer Management
3. Bangun Voucher / Promo Management
4. Tambah reporting + export
5. Bangun after-sales center
6. Tambah audit log dan alert center
7. Tambah analytics lanjutan

### 11.2 Kenapa Urutan Ini

- Dashboard tanpa data customer dan promo akan cepat mentok nilainya.
- After-sales sebaiknya masuk setelah order workflow dan reporting lebih matang.
- Audit log dan alert lebih efektif setelah modul inti stabil.

### 11.3 Rekomendasi Produk

- **Jangan mulai dari grafik yang cantik dulu.** Mulai dari widget yang bisa diklik dan langsung mengarahkan kerja admin.
- **Customer module adalah gap terbesar setelah order/stok.** Ini paling bernilai setelah modul sekarang.
- **Voucher/promo harus segera surfaced.** Karena backend e-commerce sudah punya kebutuhan campaign.
- **After-sales jangan ditunda terlalu lama.** Begitu order naik, komplain dan retur akan muncul.
- **Audit log wajib ada sebelum tim admin membesar.** Kalau tidak, perubahan data akan sulit ditelusuri.

---

## 12. DEFINITION OF DONE PER PHASE

## Phase 1 dianggap selesai jika:

- Dashboard menampilkan metrik bisnis inti yang benar
- Ada quick actions yang bisa dipakai harian
- Customer resource aktif
- Voucher resource aktif
- Order list punya filter operasional yang layak
- Export dasar berjalan

## Phase 2 dianggap selesai jika:

- After-sales workflow dapat digunakan end-to-end
- Audit log dapat dibaca admin/owner
- Alert penting muncul di panel
- Reporting owner bisa dipakai untuk review mingguan/bulanan

## Phase 3 dianggap selesai jika:

- Ada insight growth yang benar-benar dipakai untuk keputusan campaign / restock
- Ada analitik produk dan customer yang stabil
- Admin panel berfungsi bukan hanya sebagai CRUD backend, tetapi sebagai business control center

---

## PENUTUP

Jika hanya memilih **3 pekerjaan terpenting berikutnya**, urutan paling masuk akal untuk Auraquina adalah:

1. **Upgrade dashboard menjadi actionable operations dashboard**
2. **Bangun Customer Management**
3. **Bangun Voucher / Promo Management**

Jika ketiganya selesai, admin panel akan naik level dari sekadar panel CRUD menjadi alat operasional bisnis yang benar-benar dipakai harian.
