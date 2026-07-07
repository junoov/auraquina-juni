#!/bin/sh

cd /var/www

echo "[entrypoint] Setting up environment..."

# Fix git ownership warning
git config --global --add safe.directory /var/www

# Detect host UID/GID from bind mount (avoids root-owned files on host)
HOST_UID=$(stat -c '%u' /var/www 2>/dev/null || echo "0")
HOST_GID=$(stat -c '%g' /var/www 2>/dev/null || echo "0")

if [ "$HOST_UID" != "0" ]; then
    echo "[entrypoint] Running as host user UID=$HOST_UID GID=$HOST_GID"
    addgroup -g "$HOST_GID" -o hostgroup 2>/dev/null || true
    adduser -u "$HOST_UID" -G hostgroup -D -h /tmp hostuser 2>/dev/null || true
    RS_USER="hostuser"
else
    RS_USER="root"
fi

# Fix permissions (storage & cache only)
chmod -R 777 storage bootstrap/cache 2>/dev/null || true

# Docker gets runtime configuration from docker-compose.yml and .env.docker.
# Do not mutate the bind-mounted .env file; it belongs to the local host runtime.

# Persist object storage env from Docker into Laravel .env when provided.
for key in R2_ACCESS_KEY_ID R2_SECRET_ACCESS_KEY R2_ENDPOINT R2_BUCKET R2_URL AWS_ACCESS_KEY_ID AWS_SECRET_ACCESS_KEY AWS_BUCKET AWS_ENDPOINT AWS_URL; do
    value=$(printenv "$key")
    if [ -n "$value" ]; then
        if grep -q "^$key=" .env 2>/dev/null; then
            sed -i "s|^$key=.*|$key=$value|" .env 2>/dev/null || true
        else
            printf '\n%s=%s\n' "$key" "$value" >> .env
        fi
    fi
done

# Generate app key if needed
if [ -f .env ] && ! grep -q "APP_KEY=base64:" .env; then
    echo "[entrypoint] Generating app key..."
    php artisan key:generate 2>&1 || true
fi

# Always run composer install to ensure all dependencies are present
# (named volumes can be stale from previous builds)
echo "[entrypoint] Installing/updating composer dependencies..."
su-exec "$RS_USER" composer install --no-interaction --optimize-autoloader --no-scripts 2>&1 || \
su-exec "$RS_USER" rsync -a /tmp/vendor/ /var/www/vendor/ 2>/dev/null || true

# Ensure autoloader is up-to-date
echo "[entrypoint] Rebuilding autoloader..."
su-exec "$RS_USER" composer dump-autoload --optimize --no-scripts 2>&1 || true

# Wait for MySQL to be ready (max 60s timeout, then continue)
echo "[entrypoint] Waiting for MySQL..."
RETRIES=0
MAX_RETRIES=12
until mysqladmin ping -h "${DB_HOST:-mysql}" -u "${DB_USERNAME:-root}" -p"${DB_PASSWORD:-root}" --skip-ssl --silent 2>/dev/null; do
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

# Clear stale caches
echo "[entrypoint] Clearing caches..."
php artisan config:clear 2>&1 || true
php artisan route:clear 2>&1 || true
php artisan view:clear 2>&1 || true

echo "[entrypoint] Starting Laravel development server..."
exec php artisan serve --host=0.0.0.0 --port=8000
