@echo off
echo 🚀 Starting Auraquina Docker 1-Click Setup...

:: 1. Copy env if not exists
if not exist .env (
    echo 📋 Copying .env.example to .env...
    copy .env.example .env
)

:: 2. Start Docker Compose (MySQL + App + Node)
echo 🐳 Starting Docker containers...
docker compose up -d --build

:: 3. Wait for MySQL to be ready
echo ⏳ Waiting for MySQL to be ready...
timeout /t 10 /nobreak >nul

:: 4. Install PHP Dependencies
echo 📦 Installing Composer dependencies...
docker compose exec -u root app composer install

:: 5. Generate Application Key
echo 🔑 Generating Laravel Application Key...
docker compose exec -u root app php artisan key:generate

:: 6. Fix Storage Permissions
echo 🔒 Fixing storage permissions...
docker compose exec -u root app chmod -R 777 storage bootstrap/cache

:: 7. Build Frontend Assets
echo ⚡ Building frontend assets (Vite)...
docker compose exec node npm run build

echo.
echo =============================================
echo 🎉 Setup selesai! Aplikasi siap digunakan.
echo 🌐 Web: http://localhost:8000
echo 🔐 Admin: http://localhost:8000/admin
echo =============================================
pause
