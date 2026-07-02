#!/bin/bash

cd /var/www

echo "[entrypoint] Setting up environment..."

# Fix git ownership warning
git config --global --add safe.directory /var/www

# Detect host UID/GID from bind mount (avoids root-owned files on host)
HOST_UID=$(stat -c '%u' /var/www 2>/dev/null || echo "0")
HOST_GID=$(stat -c '%g' /var/www 2>/dev/null || echo "0")

if [ "$HOST_UID" != "0" ]; then
    echo "[entrypoint] Running as host user UID=$HOST_UID GID=$HOST_GID"
    groupadd -g "$HOST_GID" -o hostgroup 2>/dev/null || true
    useradd -u "$HOST_UID" -g "$HOST_GID" -o -m hostuser 2>/dev/null || true
    RS_USER="hostuser"
else
    RS_USER="root"
fi

# Fix permissions (storage & cache only)
chmod -R 777 storage bootstrap/cache 2>/dev/null || true

# Copy .env if missing
if [ ! -f ".env" ]; then
    echo "[entrypoint] Copying .env.example to .env..."
    cp .env.example .env 2>/dev/null || true
fi

# Generate app key if needed
if [ -f .env ] && ! grep -q "APP_KEY=base64:" .env; then
    echo "[entrypoint] Generating app key..."
    php artisan key:generate 2>&1 || true
fi

# Sync vendor from image to named volume every boot
# Run as host user to avoid root-owned files on host filesystem
if [ ! -f "vendor/autoload.php" ]; then
    echo "[entrypoint] First boot: copying vendor from image..."
    gosu "$RS_USER" rsync -a /tmp/vendor/ /var/www/vendor/ 2>/dev/null || \
    gosu "$RS_USER" composer install --no-interaction --optimize-autoloader 2>&1
else
    echo "[entrypoint] Syncing vendor from image..."
    gosu "$RS_USER" rsync -a --delete /tmp/vendor/ /var/www/vendor/ 2>/dev/null || true
fi

# Wait for MySQL to be ready (max 60s timeout, then continue)
echo "[entrypoint] Waiting for MySQL..."
RETRIES=0
MAX_RETRIES=12
until php artisan db:monitor > /dev/null 2>&1; do
    RETRIES=$((RETRIES + 1))
    if [ $RETRIES -ge $MAX_RETRIES ]; then
        echo "[entrypoint] WARNING: MySQL not ready after 60s, continuing anyway..."
        break
    fi
    echo "[entrypoint] Waiting for MySQL... ($RETRIES/$MAX_RETRIES)"
    sleep 5
done

# Run migrations (use --force for non-interactive, skip if already migrated)
echo "[entrypoint] Running migrations..."
php artisan migrate --force 2>&1 || true

# Cache config & routes for performance
echo "[entrypoint] Caching config & routes..."
php artisan config:cache 2>&1 || true
php artisan route:cache 2>&1 || true
php artisan view:cache 2>&1 || true

echo "[entrypoint] Starting Laravel Octane (FrankenPHP)..."
# --max-requests=500 prevents memory leaks (restarts worker after 500 requests)
exec php artisan octane:start --server=frankenphp --host=0.0.0.0 --port=8000 --max-requests=500