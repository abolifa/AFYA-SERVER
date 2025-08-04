# Dockerfile
FROM php:8.2-fpm

WORKDIR /var/www

# install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev \
 && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
 && rm -rf /var/lib/apt/lists/*

# install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# copy application code
COPY . .

# install PHP dependencies
RUN composer install --no-interaction --optimize-autoloader \
 && chown -R www-data:www-data /var/www \
 && chmod -R 755 /var/www

# run php-fpm
CMD ["php-fpm"]
