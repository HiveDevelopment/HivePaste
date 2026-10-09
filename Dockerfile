FROM node:22-alpine AS frontend
WORKDIR /app
COPY package.json ./
COPY resources ./resources
RUN npm install --no-audit --no-fund && npm run build

FROM composer:2 AS dependencies
WORKDIR /app
COPY composer.json ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --no-scripts --ignore-platform-reqs

FROM php:8.3-fpm-alpine
RUN apk add --no-cache nginx supervisor sqlite-libs icu-libs libzip \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS icu-dev libzip-dev sqlite-dev \
    && docker-php-ext-install pdo_sqlite pdo_mysql intl opcache zip \
    && apk del .build-deps
WORKDIR /var/www/html
COPY . .
COPY --from=dependencies /app/vendor ./vendor
COPY --from=frontend /app/public/assets ./public/assets
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/start.sh /usr/local/bin/hivepaste-start
RUN chmod +x /usr/local/bin/hivepaste-start \
    && mkdir -p /var/lib/nginx/tmp /run/nginx /var/www/html/database \
    && chown -R www-data:www-data storage bootstrap/cache database /var/lib/nginx /run/nginx
EXPOSE 8080
CMD ["/usr/local/bin/hivepaste-start"]
