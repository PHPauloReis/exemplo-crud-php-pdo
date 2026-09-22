FROM composer:2 AS dependencies
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-progress --prefer-dist --optimize-autoloader --no-scripts

FROM node:22-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci --ignore-scripts
COPY resources ./resources
COPY public ./public
COPY vite.config.js ./
RUN npm run build

FROM php:8.4-fpm-alpine
WORKDIR /var/www/html
RUN apk add --no-cache icu-libs libzip libpng libjpeg-turbo freetype oniguruma \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS icu-dev libzip-dev libpng-dev libjpeg-turbo-dev freetype-dev oniguruma-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" bcmath gd intl opcache pdo_mysql zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps
COPY docker/php/php.ini /usr/local/etc/php/conf.d/99-production.ini
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/zz-production.conf
COPY --from=dependencies /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build
COPY . .
COPY docker/entrypoint/production-entrypoint.sh /usr/local/bin/production-entrypoint
RUN chmod +x /usr/local/bin/production-entrypoint \
    && chown -R www-data:www-data storage bootstrap/cache public
ENTRYPOINT ["production-entrypoint"]
CMD ["php-fpm", "-F"]
