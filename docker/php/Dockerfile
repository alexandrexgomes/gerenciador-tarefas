FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git unzip curl \
    pkg-config \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
    libicu-dev \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install -j$(nproc) \
    pdo_mysql mbstring xml zip bcmath intl gd opcache \
  && rm -rf /var/lib/apt/lists/*

RUN pecl install xdebug \
  && docker-php-ext-enable xdebug

COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

WORKDIR /var/www/html