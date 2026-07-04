# Optimized Dockerfile for Laravel (Alpine-based)
FROM php:8.4-fpm-alpine

# Install ALL system deps + PHP extensions in ONE layer (faster build, smaller image)
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    onig-dev \
    libxml2-dev \
    libzip-dev \
    icu-dev \
    libsodium-dev \
    mariadb-client \
    rsync \
    su-exec \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl sodium \
    && pecl install redis && docker-php-ext-enable redis \
    && apk del --no-cache .build-deps 2>/dev/null || true

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy PHP configuration
COPY docker/php.ini /usr/local/etc/php/conf.d/auraquina.ini

# Set working directory
WORKDIR /var/www

# Set git safe.directory for volume mounts
RUN git config --global --add safe.directory /var/www 2>/dev/null || true

# Allow composer to run as root (needed for build)
ENV COMPOSER_ALLOW_SUPERUSER=1

# Layer-cached dependency install: copy lock files first, install, then copy rest
COPY composer.json composer.lock ./
RUN composer install --optimize-autoloader --no-interaction --no-scripts

# Save vendor to /tmp for first-boot copy (named volume will overlay /var/www/vendor)
RUN cp -r /var/www/vendor /tmp/vendor

# Copy entrypoint script
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Copy full application code (overlays the partial copy above)
COPY . /var/www

# Fix storage permissions (needed for bind mount)
RUN chmod -R 777 storage bootstrap/cache 2>/dev/null || true

# Expose port 8000
EXPOSE 8000

# Use entrypoint to handle first-boot setup + start server
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
