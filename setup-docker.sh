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

# 3. Clean up orphan containers and start Docker Compose
echo "🧹 Cleaning up old containers..."
sudo docker-compose down --remove-orphans

echo "🐳 Starting Docker containers..."
sudo docker-compose up -d --build

# 4. Wait for MySQL to be ready
echo "⏳ Waiting for MySQL to be ready..."
sleep 10

# 5. Install PHP Dependencies
echo "📦 Installing Composer dependencies..."
sudo docker-compose exec -u root app composer install

# 6. Generate Application Key
echo "🔑 Generating Laravel Application Key..."
sudo docker-compose exec -u root app php artisan key:generate

# 7. Fix Storage Permissions
echo "🔒 Fixing storage permissions..."
sudo docker-compose exec -u root app chmod -R 777 storage bootstrap/cache

# 8. Build Frontend Assets
echo "⚡ Building frontend assets (Vite)..."
sudo docker-compose exec node npm run build

echo ""
echo "============================================="
echo "🎉 Setup selesai! Aplikasi siap digunakan."
echo "🌐 Web: http://localhost:8000"
echo "🔐 Admin: http://localhost:8000/admin"
echo "---------------------------------------------"
echo "💡 Perintah Sehari-hari:"
echo "   - Mematikan : docker-compose stop"
echo "   - Menyalakan: docker-compose start"
echo "============================================="
