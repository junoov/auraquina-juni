#!/bin/bash

cd /var/www

echo "[entrypoint] Setting up environment..."

# Fix git ownership warning
git config --global --add safe.directory /var/www

# Fix permissions for mounted volume
echo "[entrypoint] Fixing permissions..."
chown -R root:root /var/www 2>/dev/null || true
chmod -R 755 /var/www 2>/dev/null || true
chmod -R 777 storage bootstrap/cache 2>/dev/null || true

# Install dependencies if vendor missing
if [ ! -f "vendor/autoload.php" ]; then
    echo "[entrypoint] Running composer install..."
    composer install --no-interaction --optimize-autoloader 2>&1
    if [ $? -ne 0 ]; then
        echo "[entrypoint] ERROR: Composer install failed!"
        exit 1
    fi
fi

# Generate app key if needed
if [ -f .env ] && ! grep -q "APP_KEY=base64:" .env; then
    echo "[entrypoint] Generating app key..."
    php artisan key:generate 2>&1 || true
fi

echo "[entrypoint] Starting Laravel server..."
exec php artisan serve --host=0.0.0.0 --port=8000
