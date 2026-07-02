#!/bin/bash

cd /var/www

echo "[entrypoint] Setting up environment..."

# Fix git ownership warning
git config --global --add safe.directory /var/www

# Fix permissions for storage & cache only (skip full chown - slow on volumes)
echo "[entrypoint] Fixing permissions..."
chmod -R 777 storage bootstrap/cache 2>/dev/null || true

# Copy .env if missing
if [ ! -f ".env" ]; then
    echo "[entrypoint] Copying .env.example to .env..."
    cp .env.example .env 2>/dev/null || true
fi

# Install composer dependencies if vendor missing
if [ ! -f "vendor/autoload.php" ]; then
    echo "[entrypoint] Running composer install..."
    composer install --no-interaction --optimize-autoloader --no-dev 2>&1
    if [ $? -ne 0 ]; then
        echo "[entrypoint] Retrying composer install without cache..."
        composer install --no-interaction --no-cache 2>&1
        if [ $? -ne 0 ]; then
             exit 1
        fi
    fi
fi

# Generate app key if needed
if [ -f .env ] && ! grep -q "APP_KEY=base64:" .env; then
    echo "[entrypoint] Generating app key..."
    php artisan key:generate 2>&1 || true
fi

# Run migrations
echo "[entrypoint] Running migrations..."
php artisan migrate --force 2>&1 || true

# Build frontend assets if not built yet
if [ ! -d "public/build" ] || [ ! -f "public/build/manifest.json" ]; then
    echo "[entrypoint] Installing npm dependencies..."
    npm ci --ignore-scripts 2>&1
    echo "[entrypoint] Building frontend assets..."
    npm run build 2>&1
fi

# Remove hot file to use built assets
rm -f public/hot

# Cache config & routes for performance
echo "[entrypoint] Caching config & routes..."
php artisan config:cache 2>&1 || true
php artisan route:cache 2>&1 || true
php artisan view:cache 2>&1 || true

echo "[entrypoint] Starting Laravel server..."
exec php artisan serve --host=0.0.0.0 --port=8000
