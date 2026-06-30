#!/bin/bash
set -e

echo "[entrypoint] Starting Laravel development server..."
php artisan serve --host=0.0.0.0 --port=8000
