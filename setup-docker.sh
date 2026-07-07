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

# Detect docker compose vs docker-compose
if sudo docker compose version >/dev/null 2>&1; then
    DC_CMD="docker compose"
elif command -v docker-compose >/dev/null 2>&1; then
    DC_CMD="docker-compose"
else
    echo "❌ Docker Compose tidak ditemukan! Pastikan Docker Compose terinstal."
    exit 1
fi

echo "Using command: sudo $DC_CMD"

# 3. Clean up orphan containers and start Docker Compose
echo "🧹 Cleaning up old containers..."
sudo $DC_CMD down --remove-orphans -v 2>/dev/null || true

echo "🐳 Starting Docker containers..."
sudo $DC_CMD up -d --build

# 4. Wait for app container to be fully running
echo "⏳ Menunggu container app siap (ini mungkin agak lama karena Composer install berjalan di background)..."
echo "   Untuk melihat progress: sudo $DC_CMD logs -f app"
MAX_WAIT=120
COUNT=0
while [ $COUNT -lt $MAX_WAIT ]; do
    STATUS=$(sudo docker inspect --format='{{.State.Status}}' auraquina-app-1 2>/dev/null || echo "unknown")
    RESTARTS=$(sudo docker inspect --format='{{.RestartCount}}' auraquina-app-1 2>/dev/null || echo "0")
    
    if [ "$STATUS" = "running" ] && [ "$RESTARTS" -lt 5 ]; then
        # Check if composer finished by looking for vendor/autoload.php
        if sudo $DC_CMD exec -T app ls vendor/autoload.php >/dev/null 2>&1; then
            if sudo $DC_CMD exec -T app php artisan --version >/dev/null 2>&1; then
                echo "✅ Container app sudah siap!"
                break
            fi
        fi
    fi
    
    if [ "$RESTARTS" -ge 5 ]; then
        echo "❌ Container terlalu banyak restart. Cek logs: sudo $DC_CMD logs app"
        exit 1
    fi
    
    COUNT=$((COUNT + 5))
    echo "   Menunggu... (${COUNT}s) - Status: $STATUS, Restarts: $RESTARTS"
    sleep 5
done

if [ $COUNT -ge $MAX_WAIT ]; then
    echo "❌ Timeout menunggu container. Cek logs: sudo $DC_CMD logs app"
    exit 1
fi

# 5. Generate Application Key (if not exists)
echo "🔑 Checking Application Key..."
if ! sudo $DC_CMD exec -T app grep -q "APP_KEY=base64:" .env 2>/dev/null; then
    echo "   Generating key..."
    sudo $DC_CMD exec -u root app php artisan key:generate
else
    echo "   Key sudah ada, skip."
fi

# 6. Fix Storage Permissions
echo "🔒 Fixing storage permissions..."
sudo $DC_CMD exec -u root app chmod -R 777 storage bootstrap/cache 2>/dev/null || true

# 7. Build Frontend Assets
echo "⚡ Installing frontend dependencies & building assets (Vite)..."
sudo $DC_CMD exec node npm install
sudo $DC_CMD exec node npm run build

echo ""
echo "============================================="
echo "🎉 Setup selesai! Aplikasi siap digunakan."
echo "🌐 Web: http://localhost:8001"
echo "🔐 Admin: http://localhost:8001/admin"
echo "---------------------------------------------"
echo "💡 Perintah Sehari-hari:"
echo "   - Mematikan : sudo $DC_CMD stop"
echo "   - Menyalakan: sudo $DC_CMD start"
echo "   - Lihat Log : sudo $DC_CMD logs -f app"
echo "============================================="
