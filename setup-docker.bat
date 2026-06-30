@echo off
echo 🚀 Starting Auraquina Docker 1-Click Setup...

:: 1. Copy env if not exists
if not exist .env (
    echo 📋 Copying .env.example to .env...
    copy .env.example .env
)

:: 2. Clean up orphan containers and start Docker Compose
echo 🧹 Cleaning up old containers...
docker compose down --remove-orphans

echo 🐳 Starting Docker containers...
docker compose up -d --build

:: 3. Tunggu beberapa detik agar container hidup
echo ⏳ Menunggu container hidup...
timeout /t 5 /nobreak >nul

:: 4. Jalankan perintah secara eksplisit agar muncul di terminal
echo 📦 Menginstall dependencies (Composer)...
docker compose exec -u root app composer install --no-interaction

echo 🔑 Generate Application Key...
docker compose exec -u root app php artisan key:generate

echo 🔒 Memperbaiki hak akses file...
docker compose exec -u root app chmod -R 777 storage bootstrap/cache

:: 5. Build Frontend Assets
echo ⚡ Membangun frontend assets (Vite)...
docker compose exec node npm run build

echo.
echo =============================================
echo 🎉 Setup selesai! Aplikasi siap digunakan.
echo 🌐 Web: http://localhost:8000
echo 🔐 Admin: http://localhost:8000/admin
echo ---------------------------------------------
echo 💡 Perintah Sehari-hari:
echo    - Mematikan : docker compose stop
echo    - Menyalakan: docker compose start
echo =============================================
pause
