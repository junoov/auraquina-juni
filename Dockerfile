# Dockerfile for Laravel - Optimized
FROM php:8.4-fpm

# Install ALL system dependencies in ONE layer
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
    libicu-dev \
    libsodium-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl sodium \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Node.js (single layer)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy PHP configuration
COPY docker/php.ini /usr/local/etc/php/conf.d/auraquina.ini

# Set working directory
WORKDIR /var/www

# Set git safe.directory
RUN git config --global --add safe.directory /var/www

# Copy dependency files FIRST (for layer caching)
COPY composer.json composer.lock package.json package-lock.json ./

# Install dependencies (cached if files don't change)
RUN composer install --no-interaction --optimize-autoloader --no-dev \
    && npm ci --ignore-scripts

# Copy entrypoint
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN dos2unix /usr/local/bin/entrypoint.sh && chmod +x /usr/local/bin/entrypoint.sh

# Copy application code
COPY . .

# Fix permissions
RUN chmod -R 777 storage bootstrap/cache 2>/dev/null || true

# Build frontend assets during image build (not at runtime)
RUN npm run build

ENV COMPOSER_ALLOW_SUPERUSER=1

USER root

EXPOSE 8000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
