# ---------- PHP-FPM (Laravel) ----------
FROM php:8.3-fpm-alpine

# System deps
RUN apk add --no-cache \
    git curl zip unzip icu-dev oniguruma-dev libzip-dev \
    shadow bash mysql-client

# PHP extensions
RUN docker-php-ext-configure intl \
 && docker-php-ext-install intl pdo_mysql mbstring zip

# Composer
RUN php -r "copy('https://getcomposer.org/installer','composer-setup.php');" \
 && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
 && rm composer-setup.php

WORKDIR /var/www

# Leverage Docker cache: only composer files first
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-scripts --no-progress || true

# Copy app
COPY . .

# Re-run composer to ensure autoload & scripts
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress \
 && php artisan package:discover --ansi || true

# Cache/config warmups are done at runtime after .env exists
# Permissions
RUN chown -R www-data:www-data /var/www \
 && find storage -type d -exec chmod 775 {} \; || true \
 && find bootstrap/cache -type d -exec chmod 775 {} \; || true

EXPOSE 9000
CMD ["php-fpm"]
