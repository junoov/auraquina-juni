# AURAQUINA — Account, Order Center, and After-Sales Implementation Plan

| **Atribut** | **Detail** |
|---|---|
| **Nama Dokumen** | Account, Order Center, and After-Sales Implementation Plan |
| **Nama Project** | Auraquina E-Commerce Platform |
| **Versi Dokumen** | 1.0 |
| **Tanggal** | 06 Juni 2026 |
| **Status** | Draft for Review |
| **Author** | Product & Engineering Team |
| **Approver** | Founder / Business Owner |
| **Klasifikasi** | Internal - Confidential |
| **Referensi** | `docs/FEATURE-AUDIT.md`, `docs/BRD-PRD.md`, `docs/PRD.md`, `docs/SRS.md` |

> Dokumen ini adalah **implementation plan terfokus** untuk tiga prioritas teratas dari audit fitur Auraquina: **manajemen akun pelanggan**, **order history / order center**, dan **retur/refund/after-sales flow**. Dokumen ini bukan BRD baru, melainkan rencana kerja delivery yang lebih operasional dan siap dipakai untuk backlog sequencing.

---

## DAFTAR ISI

1. [Tujuan Dokumen](#1-tujuan-dokumen)
2. [Ruang Lingkup Plan](#2-ruang-lingkup-plan)
3. [Kondisi Awal Implementasi](#3-kondisi-awal-implementasi)
4. [Target Outcome](#4-target-outcome)
5. [Matriks Prioritas dan Ketergantungan](#5-matriks-prioritas-dan-ketergantungan)
6. [Rencana Implementasi per Workstream](#6-rencana-implementasi-per-workstream)
7. [Phasing dan Sequence Delivery](#7-phasing-dan-sequence-delivery)
8. [Deliverables per Fase](#8-deliverables-per-fase)
9. [Risiko dan Mitigasi](#9-risiko-dan-mitigasi)
10. [Definition of Done Plan-Level](#10-definition-of-done-plan-level)
11. [Open Questions](#11-open-questions)

---

## 1. TUJUAN DOKUMEN

Dokumen ini dibuat untuk:

1. Mengubah hasil audit prioritas menjadi **rencana delivery yang bisa dieksekusi**.
2. Menentukan **urutan implementasi aman** untuk workstream account, order center, dan after-sales.
3. Mengurangi overlap dan rework antar modul customer-facing.
4. Menjadi acuan sebelum penulisan task breakdown teknis di backlog engineering.

---

## 2. RUANG LINGKUP PLAN

### 2.1 In Scope

| Workstream | Cakupan |
|---|---|
| Account Management | Profile dasar, delivery info/address management, account settings yang benar-benar fungsional |
| Order Center | Daftar pesanan customer, status summary, akses ke detail order dan action relevan |
| After-Sales | Return request, refund request/issue reporting, status after-sales, basic admin handling flow |

### 2.2 Out of Scope

| Area | Catatan |
|---|---|
| Payment gateway integration | Tidak masuk plan ini |
| Shipping / ongkir gateway integration | Tidak masuk plan ini |
| Review/rating module | Prioritas berikutnya, bukan bagian plan ini |
| Wishlist persistence | Prioritas berikutnya, bukan bagian plan ini |
| Loyalty / membership | Bukan fokus current delivery |

### 2.3 Constraint Penting

1. Plan ini harus memanfaatkan fondasi yang **sudah ada**, bukan mendesain ulang total flow checkout/order.
2. Perubahan harus konsisten dengan struktur dokumen di `docs/` dan realitas implementasi saat ini.
3. Workstream 1, 2, dan 3 saling terhubung; sequencing harus mencegah duplikasi effort.

---

## 3. KONDISI AWAL IMPLEMENTASI

### 3.1 Ringkasan Status Saat Ini

| Area | Kondisi Saat Ini | Dampak |
|---|---|---|
| Account | Halaman account sudah ada, namun sebagian besar masih statis / placeholder | Customer belum punya self-service account yang usable |
| Order Center | Data order sudah tersedia di backend, tetapi belum surfaced sebagai pusat order di akun | Customer belum punya history pesanan yang rapi |
| After-Sales | State order sudah mengarah ke return/refund, tetapi belum ada flow customer-facing | Trust pasca-pembelian belum didukung produk |

### 3.2 Asumsi Dasar Plan

| Asumsi | Implikasi |
|---|---|
| Auth dasar sudah aktif | Feature account dapat dibangun di atas user login yang sudah ada |
| Order persistence sudah berjalan | Order center dapat memakai data aktual, bukan mock |
| Signed order detail sudah ada | Transition ke order center bisa dilakukan bertahap tanpa memutus flow existing |
| Payment/shipping gateway belum dibahas di plan ini | Flow after-sales difokuskan pada request/status internal, bukan settlement gateway |

---

## 4. TARGET OUTCOME

### 4.1 Outcome Bisnis

| Outcome | Nilai |
|---|---|
| Customer trust meningkat | Ada account area yang nyata dan jalur purna jual yang jelas |
| Beban CS turun | Customer bisa cek order dan ajukan issue sendiri |
| Retention naik | Account jadi punya alasan untuk dipakai kembali |

### 4.2 Outcome Produk

| Outcome | Deskripsi |
|---|---|
| Account usable | User bisa mengelola data dasar dan alamat |
| Order self-service | User bisa melihat, membuka, dan memantau pesanan dari akun |
| After-sales visible | User bisa memulai request pasca-pembelian tanpa bergantung penuh ke chat manual |

---

## 5. MATRIKS PRIORITAS DAN KETERGANTUNGAN

### 5.1 Prioritas Eksekusi

| Priority Order | Workstream | Reason |
|---|---|---|
| 1 | Account Management | Fondasi identitas customer dan tempat semua feature account bergantung |
| 2 | Order Center | Memanfaatkan account foundation dan data order existing |
| 3 | After-Sales | Bergantung pada order visibility dan status access yang jelas |

### 5.2 Dependency Matrix

| Workstream | Bergantung Pada | Kenapa |
|---|---|---|
| Account Management | Auth dasar, user model, UI account existing | Menjadi basis data customer-facing |
| Order Center | Account Management, order persistence existing | Order perlu surfaced di account yang benar |
| After-Sales | Order Center, status transition model | Request after-sales harus terkait order yang bisa diakses customer |

### 5.3 Rekomendasi Sequencing

| Sequence | Modul | Mode Delivery |
|---|---|---|
| A | Account foundation | Harus selesai dulu |
| B | Order center baseline | Mulai setelah A stabil |
| C | After-sales request baseline | Mulai setelah B usable |
| D | Hardening + QA cross-flow | Setelah A+B+C tersedia |

---

## 6. RENCANA IMPLEMENTASI PER WORKSTREAM

## 6.1 WORKSTREAM A — ACCOUNT MANAGEMENT

### A. Objective

Membuat area account yang benar-benar berfungsi untuk mengelola informasi customer dasar dan alamat.

### A. Scope

| Scope Item | Status Target |
|---|---|
| Profile info | Editable |
| Delivery info / address book | Create, update, set default |
| Account information section | Real settings page, bukan static shell |

### A. Deliverables

| Deliverable | Deskripsi |
|---|---|
| Account data model alignment | Struktur data user/address yang jelas |
| Functional account screens | UI account yang submit ke backend nyata |
| Validation and feedback states | Error/success state yang konsisten |

### A. Notes

1. Hindari scope creep ke social login atau loyalty.
2. Fokus pada data yang benar-benar dibutuhkan untuk order dan account continuity.

---

## 6.2 WORKSTREAM B — ORDER HISTORY / ORDER CENTER

### B. Objective

Menyediakan pusat pesanan yang bisa diakses customer dari account.

### B. Scope

| Scope Item | Status Target |
|---|---|
| Order list page | Visible |
| Status grouping / summary | Visible |
| Link ke detail pesanan | Functional |
| Customer action entry point | Contextual |

### B. Deliverables

| Deliverable | Deskripsi |
|---|---|
| Order center screen | Daftar pesanan dalam akun |
| Status taxonomy mapping | Pending, active, delivered, completed, cancelled, dll |
| Order action map | Aksi mana yang tersedia pada status tertentu |

### B. Notes

1. Gunakan data dan route order yang sudah ada semaksimal mungkin.
2. Order center harus menjadi jembatan ke after-sales, bukan layar terpisah yang buntu.

---

## 6.3 WORKSTREAM C — AFTER-SALES FLOW

### C. Objective

Memberi customer jalur resmi untuk issue pasca-pembelian seperti return/refund request.

### C. Scope

| Scope Item | Status Target |
|---|---|
| Return/refund request entry point | Available |
| Request form / issue capture | Available |
| Status visibility | Available |
| Admin handling baseline | Defined |

### C. Deliverables

| Deliverable | Deskripsi |
|---|---|
| After-sales request flow | Alur customer dari order ke request |
| Request state model | State minimum yang dibutuhkan |
| Admin/internal handling notes | Apa yang harus ditangani internal dan apa yang customer lihat |

### C. Notes

1. Karena payment/shipping gateway out of scope, request flow harus cukup generik untuk tetap berjalan tanpa integrasi gateway.
2. Fokus awal pada issue intake, visibility, dan state clarity.

---

## 7. PHASING DAN SEQUENCE DELIVERY

### 7.1 Fase Implementasi

| Fase | Fokus | Workstream | Output Utama |
|---|---|---|---|
| Phase 1 | Foundation | A | Account usable baseline |
| Phase 2 | Visibility | B | Order center baseline |
| Phase 3 | Post-purchase trust | C | After-sales baseline |
| Phase 4 | Integration hardening | A + B + C | Cross-flow consistency and QA |

### 7.2 Suggested Sprint Framing

| Sprint | Fokus | Estimasi Kasar |
|---|---|---|
| Sprint 1 | Account foundation | 1 sprint |
| Sprint 2 | Order center | 1 sprint |
| Sprint 3 | After-sales baseline | 1 sprint |
| Sprint 4 | Hardening, polish, QA | 1 sprint |

> Catatan: angka sprint di atas adalah framing awal untuk sequencing, bukan estimasi final tim.

---

## 8. DELIVERABLES PER FASE

| Phase | Deliverable Product | Deliverable Tech | Deliverable QA |
|---|---|---|---|
| 1 | Account screens yang usable | Endpoint/form handling, persistence, validation | Form and state coverage |
| 2 | Order center screen | Query/state mapping, route surfacing | Order visibility and navigation coverage |
| 3 | After-sales request UI | Request persistence, status model, admin handling baseline | Request flow and access coverage |
| 4 | End-to-end coherence | Cross-module cleanup and hardening | Regression checklist |

---

## 9. RISIKO DAN MITIGASI

| Risiko | Dampak | Mitigasi |
|---|---|---|
| Scope account melebar ke terlalu banyak profile features | Delivery melambat | Batasi ke data inti + address management |
| Order center terlalu cepat didesain ulang | Rework tinggi | Pakai route/data order existing sebagai baseline |
| After-sales ambigu antara return vs refund vs complaint | UX dan operasional kabur | Definisikan taxonomy request lebih dulu di fase planning teknis |
| Tidak ada test memadai | Risiko regresi naik | Tambahkan QA checklist dan test coverage per fase |
| Gateway belum terintegrasi | Beberapa case after-sales tidak final | Gunakan baseline internal workflow dulu, tandai dependency eksternal |

---

## 10. DEFINITION OF DONE PLAN-LEVEL

Plan ini dianggap siap diturunkan ke backlog implementasi jika:

1. Scope untuk workstream A, B, dan C sudah jelas dan tidak overlap.
2. Dependency order antar workstream sudah disepakati.
3. Deliverables per fase bisa diterjemahkan menjadi task engineering.
4. Risiko utama dan out-of-scope sudah terdokumentasi.
5. Stakeholder product/engineering sepakat bahwa plan ini cukup untuk masuk breakdown teknis.

---

## 11. OPEN QUESTIONS

| ID | Open Question | Dampak ke Plan |
|---|---|---|
| OQ-01 | Apakah address book akan single-address dulu atau multi-address sejak awal? | Menentukan scope Workstream A |
| OQ-02 | Apakah order center perlu filter status di fase awal atau cukup daftar + detail? | Menentukan scope Workstream B |
| OQ-03 | Apakah after-sales baseline mencakup refund dan return sekaligus, atau dimulai dari issue request generik? | Menentukan kompleksitas Workstream C |
| OQ-04 | Apakah admin handling after-sales akan masuk panel existing atau cukup documented internal workflow dulu? | Menentukan deliverable teknis dan operasional |

### 11.1 Rekomendasi Jawaban Awal

| Pertanyaan | Rekomendasi Awal |
|---|---|
| Address book | Mulai dari single default address + edit, lalu naik ke multi-address |
| Order center | Mulai dari daftar order + status + link detail |
| After-sales | Mulai dari request generik yang terhubung ke order |
| Admin handling | Baseline internal workflow dulu, lalu panelize jika scope sudah stabil |
