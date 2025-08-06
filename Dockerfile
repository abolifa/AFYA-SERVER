# Use the official PHP 8.1 FPM image based on Alpine
FROM php:8.1-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    git \
    oniguruma-dev \
    libzip-dev \
    zip \
    unzip \
    mysql-client

# Install and enable PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring zip

# Install Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
 && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
 && rm composer-setup.php

WORKDIR /var/www

# Copy only composer files first (to leverage build cache)
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copy application code
COPY . .

# Set proper permissions
RUN chown -R www-data:www-data /var/www && \
    chmod -R 755 /var/www

EXPOSE 9000
CMD ["php-fpm"]
