# Setup Auraquina di Komputer Lokal

Panduan buat tim yang baru clone repo ini. Ikutin step-nya urut ya.

---

## Yang Harus Udah Ada di Komputer

- **PHP 8.3+** — cek dengan `php -v`
- **Composer** — cek dengan `composer -V`
- **Node.js 18+** — cek dengan `node -v`
- **Git** — cek dengan `git --version`
- **MySQL 8+** — cek dengan `mysql --version`

Kalau belum install, cari di:
- PHP: https://windows.php.net/download/ (pakai VS16 x64 Thread Safe)
- Composer: https://getcomposer.org/download/
- Node.js: https://nodejs.org/ (LTS aja)
- MySQL: https://dev.mysql.com/downloads/installer/ (pakai yang Community)

---

## Step 1: Clone & Masuk ke Folder

```bash
git clone https://github.com/NAMA-REPO/auraquina.git
cd auraquina
```

---

## Step 2: Install Semua Dependency

```bash
composer install
npm install
```

Kalau `composer install` error soal PHP version, berarti PHP lo masih di bawah 8.3. Update dulu.

---

## Step 3: Setup Environment

```bash
copy .env.example .env
php artisan key:generate
```

Ini bikin file `.env` dari template. **Jangan di-commit** — udah di-ignore.

---

## Step 4: Setup Database

Project ini pakai **MySQL**. Pastikan MySQL udah jalan di komputer lo.

### 4a. Buat Database

Masuk ke MySQL:
```bash
mysql -u root -p
```

Terus jalankan:
```sql
CREATE DATABASE auraquina;
EXIT;
```

### 4b. Edit .env (Kalau Perlu)

Buka file `.env`, sesuaikan bagian database:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=auraquina
DB_USERNAME=root
DB_PASSWORD=
```

Kalau MySQL lo pakai password, isi `DB_PASSWORD=password_lo`.

### 4c. Jalankan Migrasi

```bash
php artisan migrate
```

Kalau mau data contoh (produk dummy, dll):
```bash
php artisan db:seed
```

---

## Step 5: Link Storage (Biar Foto Produk Muncul)

```bash
php artisan storage:link
```

Ini bikin symlink dari `public/storage` ke `storage/app/public`. Tanpa ini, foto produk ga akan muncul.

---

## Step 6: Download Foto Produk

Foto produk ga masuk git (kegedean, 1.8 GB). Download dari **Google Drive / cloud** yang udah dibagi tim, terus taruh di:

```
storage/app/public/produk/
```

Struktur folder-nya harus gini:
```
produk/
├── aura-set/
│   ├── image-01.jpg
│   ├── image-02.jpg
│   └── ...
├── bora-dress/
│   ├── image-01.jpg
│   └── ...
└── ... (folder produk lainnya)
```

Ada 5 foto sample di `docs/sample-produk/` buat referensi.

---

## Step 7: Build Asset & Jalankan

```bash
composer run dev
```

Ini jalankan **4 hal sekaligus**:
- Laravel server (http://localhost:8000)
- Queue worker
- Log viewer
- Vite (hot reload untuk CSS/JS)

Buka browser, akses **http://localhost:8000**

---

## Kalau Error

### "Could not find driver"
```bash
# Di Windows, uncomment extension di php.ini:
extension=pdo_mysql
extension=mysqli
```

### "Access denied for user"
```bash
# Cek username/password MySQL di .env
# Default: root tanpa password
# Kalau pakai password, isi DB_PASSWORD di .env
```

### "Unknown database 'auraquina'"
```bash
# Masuk MySQL dulu, terus buat database:
mysql -u root -p
CREATE DATABASE auraquina;
EXIT;
```

### "No application encryption key has been specified"
```bash
php artisan key:generate
```

### Foto produk ga muncul
```bash
php artisan storage:link
```
Pastikan foto udah ada di `storage/app/public/produk/`.

### CSS/JS ga update
```bash
npm run build
```
Atau pakai `composer run dev` biar auto-reload.

---

## Command Penting

| Command | Buat Apa |
|---|---|
| `composer run dev` | Jalankan server + vite + queue |
| `npm run build` | Build CSS/JS untuk production |
| `php artisan migrate` | Jalankan migrasi database |
| `php artisan db:seed` | Insert data dummy |
| `php artisan storage:link` | Link folder storage |
| `php artisan test` | Jalankan test |

---

## Struktur Folder (Yang Penting)

```
auraquina/
├── app/              ← PHP code (Model, Controller, dll)
├── config/           ← Konfigurasi Laravel
├── database/         ← Migrasi & seeder
├── docs/             ← Dokumentasi & foto sample
├── public/           ← Web root (CSS, JS, gambar)
├── resources/        ← Blade template & CSS/JS source
├── routes/           ← Definisi route
├── storage/          ← Log, cache, foto produk
├── .env              ← Environment (JANGAN DI-COMMIT)
└── composer.json     ← Dependency PHP
```

---

## Ada Pertanyaan?

Hubungi tim yang udah duluan setup. Atau cek dokumentasi Laravel: https://laravel.com/docs
