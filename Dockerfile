FROM php:8.3-apache

WORKDIR /var/www/html

RUN apt-get update && apt-get upgrade -y

RUN apt-get install -y --no-install-recommends \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libpq-dev \
    git \
    unzip \
    libonig-dev \
    libzip-dev \
    libicu-dev
RUN rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo pdo_pgsql mbstring zip exif pcntl gd intl opcache mysqli pgsql

RUN php -m | grep pgsql
RUN php -i | grep pgsql

RUN docker-php-ext-enable opcache

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY composer.json composer.lock ./

RUN composer install --no-interaction --optimize-autoloader --no-dev

COPY . .

RUN chown -R www-data:www-data writable

COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

EXPOSE 80