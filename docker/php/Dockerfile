FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
        bash git curl libpng-dev libjpeg-turbo-dev libwebp-dev libxml2-dev \
        oniguruma-dev icu-dev libzip-dev mysql-client redis nginx supervisor \
        $PHPIZE_DEPS \
    && docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql bcmath intl gd mbstring zip exif pcntl opcache \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del $PHPIZE_DEPS

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && php artisan storage:link || true \
    && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
