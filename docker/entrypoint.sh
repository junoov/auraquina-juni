#!/bin/bash

cd /var/www

echo "[entrypoint] Setting up environment..."

# Fix git ownership warning
git config --global --add safe.directory /var/www

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

# Install Composer dependencies (populates named volume at runtime)
echo "[entrypoint] Installing Composer dependencies..."
composer install --no-interaction --optimize-autoloader 2>&1

# Wait for MySQL to be ready
echo "[entrypoint] Waiting for MySQL..."
until php artisan db:monitor > /dev/null 2>&1; do
    sleep 1
done

# Run migrations
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