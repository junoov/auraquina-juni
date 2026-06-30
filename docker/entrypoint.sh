#!/bin/bash
set -e

echo "[entrypoint] Changing to /var/www directory..."
cd /var/www

echo "[entrypoint] Fixing permissions..."
chown -R www-data:www-data storage bootstrap/cache || true
chmod -R 775 storage bootstrap/cache || true

if [ ! -d "vendor" ]; then
    echo "[entrypoint] vendor not found, running composer install..."
    composer install --no-interaction --optimize-autoloader || true
else
    echo "[entrypoint] vendor exists, skipping composer install."
fi

# Ensure app key is set if not exists
if ! grep -q "APP_KEY=base64:" .env; then
    echo "[entrypoint] Generating app key..."
    php artisan key:generate || true
fi

echo "[entrypoint] Starting Laravel development server..."
php artisan serve --host=0.0.0.0 --port=8000
