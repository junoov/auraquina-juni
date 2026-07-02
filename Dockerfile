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
    dos2unix

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

# Create vendor directory with proper permissions
RUN mkdir -p /var/www/vendor && chmod 777 /var/www/vendor

# Copy entrypoint script
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN dos2unix /usr/local/bin/entrypoint.sh && chmod +x /usr/local/bin/entrypoint.sh

# Allow composer to run as root
ENV COMPOSER_ALLOW_SUPERUSER=1

# Copy existing application directory permissions
COPY . /var/www

# Install Composer dependencies (build-time, not runtime)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install Octane (creates Caddyfile and binary at build-time)
RUN php artisan octane:install --server=frankenphp --no-interaction

# Run as root so entrypoint can fix permissions
USER root

# Expose port 8000 (for Octane)
EXPOSE 8000

# Use entrypoint to handle composer install + start server
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
