#!/bin/bash

# Exit on error
set -e

echo "🚀 Starting Auraquina Docker 1-Click Setup..."

# 1. Copy env if not exists
if [ ! -f .env ]; then
    echo "📋 Copying .env.example to .env..."
    cp .env.example .env
fi

# 2. Check if Docker is running
if ! sudo systemctl is-active --quiet docker; then
    echo "⚡ Starting Docker Service..."
    sudo systemctl start docker
fi

# 3. Start Docker Compose
echo "🐳 Starting Docker containers..."
sudo docker-compose up -d --build

# 4. Install PHP Dependencies
echo "📦 Installing Composer dependencies..."
sudo docker-compose exec -u root app composer install

# 5. Generate Application Key
echo "🔑 Generating Laravel Application Key..."
sudo docker-compose exec -u root app php artisan key:generate

# 6. Fix Storage Permissions
echo "🔒 Fixing storage permissions..."
sudo docker-compose exec -u root app chown -R www-data:www-data storage bootstrap/cache
sudo docker-compose exec -u root app chmod -R 775 storage bootstrap/cache

# 7. Install NPM packages & Build Frontend Assets
echo "📦 Installing NPM packages..."
sudo docker-compose run --rm node npm install
echo "⚡ Building frontend assets (Vite)..."
sudo docker-compose run --rm node npm run build

# 8. Start Node Container
echo "🟢 Starting Vite development server..."
sudo docker-compose up -d node

echo "============================================="
echo "🎉 Setup selesai! Aplikasi siap digunakan."
echo "🌐 Web: http://localhost:8000"
echo "🔐 Admin: http://localhost:8000/admin"
echo "============================================="
