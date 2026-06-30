# Dockerfile for Laravel
FROM php:8.4-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
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

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN apt-get update && apt-get install -y libicu-dev && docker-php-ext-configure intl && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl

# Install sodium
RUN apt-get install -y libsodium-dev && docker-php-ext-install sodium

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

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

# Run as root so entrypoint can fix permissions
USER root

# Expose port 8000
EXPOSE 8000

# Use entrypoint to handle composer install + start server
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
