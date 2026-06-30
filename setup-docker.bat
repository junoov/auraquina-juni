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

:: 3. Wait for services to be ready
echo ⏳ Waiting for services to be ready...
timeout /t 20 /nobreak >nul

:: 4. Build Frontend Assets
echo ⚡ Building frontend assets (Vite)...
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
