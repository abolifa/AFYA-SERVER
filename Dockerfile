# /opt/apps/afya/Dockerfile
FROM php:8.2-fpm

WORKDIR /var/www

# 1) Install system libs for intl + zip + gd + mysql
RUN apt-get update \
 && apt-get install -y \
      git \
      curl \
      zip \
      unzip \
      libpng-dev \
      libonig-dev \
      libxml2-dev \
      libicu-dev \
      libzip-dev \
 && docker-php-ext-install \
      pdo_mysql \
      mbstring \
      exif \
      pcntl \
      bcmath \
      gd \
      intl \
      zip \
 && rm -rf /var/lib/apt/lists/*

# 2) Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3) Copy app & install PHP deps
COPY . .
RUN composer install --no-interaction --optimize-autoloader \
 && chown -R www-data:www-data /var/www \
 && chmod -R 755 /var/www

CMD ["php-fpm"]
