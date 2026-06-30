#!/bin/bash
set -e

echo "[entrypoint] Fixing permissions..."
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

if [ ! -d "/var/www/vendor" ]; then
    echo "[entrypoint] vendor not found, running composer install..."
    composer install --no-interaction --optimize-autoloader
else
    echo "[entrypoint] vendor exists, skipping composer install."
fi

echo "[entrypoint] Starting Laravel development server..."
exec php artisan serve --host=0.0.0.0 --port=8000
