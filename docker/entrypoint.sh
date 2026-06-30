#!/bin/bash
set -e

echo "[entrypoint] Fixing permissions..."
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache || true
chmod -R 775 /var/www/storage /var/www/bootstrap/cache || true

if [ ! -d "/var/www/vendor" ]; then
    echo "[entrypoint] vendor not found, running composer install..."
    composer install --no-interaction --optimize-autoloader
else
    echo "[entrypoint] vendor exists, skipping composer install."
fi

# Ensure app key is set if not exists
if ! grep -q "APP_KEY=base64:" /var/www/.env; then
    echo "[entrypoint] Generating app key..."
    php artisan key:generate
fi

echo "[entrypoint] Starting Laravel development server..."
exec php artisan serve --host=0.0.0.0 --port=8000
