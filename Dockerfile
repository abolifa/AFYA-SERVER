# /opt/apps/afya/Dockerfile

FROM php:8.3-fpm

WORKDIR /var/www

# install system libs (intl, zip, gd, mysql, etc)
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

# install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# copy app & install PHP deps
COPY . .
RUN composer install --no-interaction --optimize-autoloader \
 && chown -R www-data:www-data /var/www \
 && chmod -R 755 /var/www

CMD ["php-fpm"]
