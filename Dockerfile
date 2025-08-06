FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
    git \
    oniguruma-dev \
    libzip-dev \
    zip \
    unzip \
    mysql-client \
    icu-dev \
    autoconf \
    g++

# Install and enable PHP extensions (intl first)
RUN docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && docker-php-ext-install pdo_mysql mbstring zip

# Install Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
 && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
 && rm composer-setup.php

WORKDIR /var/www

# Copy composer files first for build cache
COPY composer.json composer.lock ./
# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copy application code
COPY . .

# Set file permissions
RUN chown -R www-data:www-data /var/www \
 && chmod -R 755 /var/www

EXPOSE 9000
CMD ["php-fpm"]
