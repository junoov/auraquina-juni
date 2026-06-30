@echo off
echo 🚀 Starting Auraquina Docker 1-Click Setup...

:: 1. Copy env if not exists
if not exist .env (
    echo 📋 Copying .env.example to .env...
    copy .env.example .env
)

:: 2. Clean up orphan containers and start Docker Compose
echo 🧹 Cleaning up old containers...
docker compose down --remove-orphans 2>nul

echo 🐳 Starting Docker containers...
docker compose up -d --build

:: 3. Wait for app container to be fully running
echo ⏳ Menunggu container siap...
timeout /t 30 /nobreak >nul

:: 4. Generate Application Key (if not exists)
echo 🔑 Checking Application Key...
docker compose exec -u root app php artisan key:generate 2>nul

:: 5. Fix Storage Permissions
echo 🔒 Fixing storage permissions...
docker compose exec -u root app chmod -R 777 storage bootstrap/cache 2>nul

:: 6. Build Frontend Assets
echo ⚡ Building frontend assets (Vite)...
docker compose exec node npm run build 2>nul

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
