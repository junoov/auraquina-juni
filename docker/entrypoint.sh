#!/bin/bash
set -e

# Fix ownership of all files to www-data
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Ensure storage and cache are writable
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Start PHP-FPM
exec php-fpm
