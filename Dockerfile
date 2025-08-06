# Dockerfile

# 1) Base image: PHP 8.4 FPM on Alpine
FROM php:8.4-fpm-alpine

# 2) Install system deps + build tools for intl
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

# 3) Configure & install PHP extensions
RUN docker-php-ext-configure intl \
 && docker-php-ext-install intl pdo_mysql mbstring zip

# 4) Install Composer globally
RUN php -r "copy('https://getcomposer.org/installer','composer-setup.php');" \
 && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
 && rm composer-setup.php

# 5) Set working directory
WORKDIR /var/www

# 6) Copy your entire app (including artisan)
COPY . .

# 7) Ensure correct permissions
RUN chown -R www-data:www-data /var/www \
 && chmod -R 755 /var/www

# 8) Expose PHP-FPM port
EXPOSE 9000

# 9) Default command
CMD ["php-fpm"]
