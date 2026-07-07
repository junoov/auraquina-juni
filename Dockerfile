# Optimized Dockerfile for Laravel (Alpine-based)
FROM php:8.4-fpm-alpine

# Install system deps + PHP extensions in one layer.
# PHPIZE_DEPS is required to compile intl/zip/redis on Alpine; do not mask failures.
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    freetype-dev \
    oniguruma-dev \
    libxml2-dev \
    libzip-dev \
    icu-dev \
    libsodium-dev \
    mariadb-client \
    rsync \
    su-exec \
    $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype \
    && docker-php-ext-install gd \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath zip intl sodium \
    && php -m | grep -E '^(intl|zip)$' \
    && apk del --no-cache $PHPIZE_DEPS

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
