# Auraquina — Docker Setup Guide

## Prasyarat
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (Windows/macOS/Linux)

---

## 1-Klik Setup

### Windows
Double-click **`setup-docker.bat`**

### Linux / macOS
```bash
chmod +x setup-docker.sh
./setup-docker.sh
```

Selesai! Buka browser:
- **Web**: http://localhost:8000
- **Admin**: http://localhost:8000/admin

---

## Manual Setup (jika script gagal)

```bash
cp .env.example .env
docker compose up -d --build
docker compose exec -u root app composer install
docker compose exec -u root app php artisan key:generate
docker compose exec -u root app chmod -R 777 storage bootstrap/cache
docker compose exec node npm run build
```

---

## Perintah Penting

| Perintah | Fungsi |
|---|---|
| `docker compose up -d` | Nyalakan semua container |
| `docker compose down` | Matikan semua container |
| `docker compose exec app php artisan migrate` | Jalankan migrasi |
| `docker compose exec app php artisan db:seed` | Seed database |
| `docker compose logs -f app` | Lihat log aplikasi |
| `docker compose exec node npm run build` | Build ulang aset frontend |

---

## Mengapa Docker?

Tidak perlu install PHP, MySQL, Node.js, Composer, atau Laragon secara lokal. Semua jalan di dalam container terisolasi dengan versi yang sama persis.
