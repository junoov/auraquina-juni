# Dockerfile for Laravel (Optimized with FrankenPHP + Octane)
FROM dunglas/frankenphp:php8.4 AS base

# Install system dependencies
ENV DEBIAN_FRONTEND=noninteractive
RUN apt-get update && apt-get install -y --no-install-recommends \
    -o Dpkg::Options::="--force-confdef" -o Dpkg::Options::="--force-confold" \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    mariadb-client \
    dos2unix \
    rsync

# Install Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions (including Redis)
RUN apt-get update && apt-get install -y --no-install-recommends \
    -o Dpkg::Options::="--force-confdef" -o Dpkg::Options::="--force-confold" \
    libicu-dev && \
    pecl install redis && docker-php-ext-enable redis && \
    docker-php-ext-configure intl && \
    docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl

# Install sodium
RUN apt-get install -y --no-install-recommends \
    -o Dpkg::Options::="--force-confdef" -o Dpkg::Options::="--force-confold" \
    libsodium-dev && docker-php-ext-install sodium

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy PHP configuration
COPY docker/php.ini /usr/local/etc/php/conf.d/auraquina.ini

# Set working directory
WORKDIR /var/www

# Set git safe.directory for volume mounts
RUN git config --global --add safe.directory /var/www

# Allow composer to run as root (needed for build)
ENV COMPOSER_ALLOW_SUPERUSER=1

# Layer-cached dependency install: copy lock files first, install, then copy rest
COPY composer.json composer.lock ./
RUN composer install --optimize-autoloader --no-interaction

# Save vendor to /tmp for first-boot copy (named volume will overlay /var/www/vendor)
RUN cp -r /var/www/vendor /tmp/vendor

# Copy entrypoint script
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN dos2unix /usr/local/bin/entrypoint.sh && chmod +x /usr/local/bin/entrypoint.sh

# Copy full application code (overlays the partial copy above)
COPY . /var/www

# Fix storage permissions (needed for bind mount)
RUN chmod -R 777 storage bootstrap/cache 2>/dev/null || true

# Expose port 8000 (for Octane)
EXPOSE 8000

# Use entrypoint to handle first-boot setup + start server
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
