#!/bin/bash

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
sudo docker-compose down --remove-orphans 2>/dev/null || true

echo "🐳 Starting Docker containers..."
sudo docker-compose up -d --build

# 4. Wait for app container to be fully running (not restarting)
echo "⏳ Menunggu container siap..."
MAX_WAIT=60
COUNT=0
while [ $COUNT -lt $MAX_WAIT ]; do
    STATUS=$(sudo docker inspect --format='{{.State.Status}}' auraquina-app 2>/dev/null || echo "unknown")
    if [ "$STATUS" = "running" ]; then
        # Extra check: try to run a simple command
        if sudo docker-compose exec -T app php artisan --version >/dev/null 2>&1; then
            echo "✅ Container app sudah siap!"
            break
        fi
    fi
    COUNT=$((COUNT + 2))
    echo "   Menunggu... (${COUNT}s)"
    sleep 2
done

if [ $COUNT -ge $MAX_WAIT ]; then
    echo "❌ Container gagal start. Cek logs: sudo docker-compose logs app"
    exit 1
fi

# 5. Generate Application Key (if not exists)
echo "🔑 Checking Application Key..."
if ! sudo docker-compose exec -T app grep -q "APP_KEY=base64:" .env 2>/dev/null; then
    echo "   Generating key..."
    sudo docker-compose exec -u root app php artisan key:generate
else
    echo "   Key sudah ada, skip."
fi

# 6. Fix Storage Permissions
echo "🔒 Fixing storage permissions..."
sudo docker-compose exec -u root app chmod -R 777 storage bootstrap/cache 2>/dev/null || true

# 7. Build Frontend Assets
echo "⚡ Building frontend assets (Vite)..."
sudo docker-compose exec node npm run build 2>/dev/null || true

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
