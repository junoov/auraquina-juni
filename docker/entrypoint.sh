#!/bin/bash

cd /var/www

echo "[entrypoint] Fixing permissions..."
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

if [ ! -d "vendor" ]; then
    echo "[entrypoint] vendor not found, running composer install..."
    composer install --no-interaction --optimize-autoloader 2>&1 || true
else
    echo "[entrypoint] vendor exists, skipping."
fi

if [ -f .env ] && ! grep -q "APP_KEY=base64:" .env; then
    echo "[entrypoint] Generating app key..."
    php artisan key:generate 2>&1 || true
fi

echo "[entrypoint] Starting Laravel server on port 8000..."
exec php artisan serve --host=0.0.0.0 --port=8000
