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

# 4. Tunggu beberapa detik agar container App & MySQL hidup
echo "⏳ Menunggu container hidup..."
sleep 5

# 5. Jalankan perintah manual di luar background
echo "📦 Menginstall dependencies (Composer)..."
sudo docker-compose exec -u root app composer install --no-interaction

echo "🔑 Generate Application Key..."
sudo docker-compose exec -u root app php artisan key:generate

echo "🔒 Memperbaiki hak akses file..."
sudo docker-compose exec -u root app chmod -R 777 storage bootstrap/cache

echo "⚡ Membangun frontend assets (Vite)..."
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
echo "🎉 Setup selesai! Aplikasi siap digunakan."
echo "🌐 Web: http://localhost:8000"
echo "🔐 Admin: http://localhost:8000/admin"
echo "---------------------------------------------"
echo "💡 Perintah Sehari-hari:"
echo "   - Mematikan : docker-compose stop"
echo "   - Menyalakan: docker-compose start"
echo "============================================="
