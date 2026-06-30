@echo off
echo 🚀 Starting Auraquina Docker 1-Click Setup...

:: 1. Copy env if not exists
if not exist .env (
    echo 📋 Copying .env.example to .env...
    copy .env.example .env
)

:: 2. Start Docker Compose
echo 🐳 Starting Docker containers...
docker compose up -d --build

:: 3. Install PHP Dependencies
echo 📦 Installing Composer dependencies...
docker compose exec -u root app composer install

:: 4. Generate Application Key
echo 🔑 Generating Laravel Application Key...
docker compose exec -u root app php artisan key:generate

:: 5. Install NPM packages & Build Frontend Assets
echo 📦 Installing NPM packages...
docker compose run --rm node npm install
echo ⚡ Building frontend assets (Vite)...
docker compose run --rm node npm run build

:: 6. Start Node Container
echo 🟢 Starting Vite development server...
docker compose up -d node

echo =============================================
echo 🎉 Setup selesai! Aplikasi siap digunakan.
echo 🌐 Web: http://localhost:8000
echo 🔐 Admin: http://localhost:8000/admin
echo =============================================
pause
