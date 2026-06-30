# Auraquina — Docker Setup Guide

Dokumentasi ini menjelaskan cara menjalankan project **Auraquina** di lingkungan lokal menggunakan Docker.

## Prasyarat
Pastikan laptop kamu sudah terinstall **Docker Desktop**.
- [Download Docker Desktop](https://www.docker.com/products/docker-desktop/) (Windows/macOS/Linux)

---

## Langkah Setup & Instalasi (Pertama Kali)

Ikuti urutan langkah di bawah ini:

### 1. Salin Environment (.env)
Buka terminal/CMD di folder project, jalankan:
```bash
cp .env.example .env
```
*(Di Windows PowerShell, gunakan `Copy-Item .env.example .env`)*

### 2. Nyalakan Docker Services
Jalankan perintah berikut untuk mengunduh image dan menyalakan container:
```bash
docker compose up -d --build
```
> ⚠️ **Catatan**: Proses pertama kali akan memakan waktu beberapa menit karena harus mendownload image PHP, MySQL, Nginx, dan Node.js. 
> MySQL otomatis akan mengimport file database awal (`auraquina-db-export.sql`) saat container database dinyalakan.

### 3. Install Dependensi PHP (Composer) & Generate Key
Jalankan composer install dan generate application key di dalam container app:
```bash
docker compose exec app composer install
docker compose exec app php artisan key:generate
```

### 4. Buka Aplikasi di Browser
- **Aplikasi**: [http://localhost:8000](http://localhost:8000)
- **Admin Panel**: [http://localhost:8000/admin](http://localhost:8000/admin)

---

## Perintah Penting Sehari-hari

- **Mematikan Docker**:
  ```bash
  docker compose down
  ```
- **Menyalakan Docker Kembali** (Tanpa build ulang):
  ```bash
  docker compose up -d
  ```
- **Menjalankan Perintah Artisan**:
  Format: `docker compose exec app [perintah]`
  Contoh:
  ```bash
  docker compose exec app php artisan migrate
  docker compose exec app php artisan db:seed
  ```
- **Melihat Log Aplikasi**:
  ```bash
  docker compose logs -f app
  ```

---

## Mengapa Menggunakan Docker?
Dengan setup ini, kamu **tidak perlu** menginstall XAMPP, Laragon, PHP, Node.js, Composer, atau MySQL secara lokal di komputermu. Semua *environment* berjalan terisolasi di dalam container dengan versi yang sama persis seperti yang digunakan developer lain.
