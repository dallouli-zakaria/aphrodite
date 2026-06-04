FROM php:8.4-apache

ENV APP_ENV=prod \
    APP_DEBUG=0 \
    COMPOSER_ALLOW_SUPERUSER=1 \
    APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip libicu-dev libzip-dev \
    && docker-php-ext-install intl opcache pdo_mysql zip \
    && a2enmod rewrite headers \
    && sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri 's!/var/www/!${APACHE_DOCUMENT_ROOT}/!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && printf '%s\n' \
        'ServerName localhost' \
        '<Directory /var/www/html/public>' \
        '    AllowOverride None' \
        '    Require all granted' \
        '    FallbackResource /index.php' \
        '</Directory>' > /etc/apache2/conf-available/symfony.conf \
    && a2enconf symfony \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock symfony.lock ./
COPY .env ./
COPY bin ./bin
COPY config ./config
COPY migrations ./migrations
COPY public ./public
COPY src ./src
COPY Templates ./Templates
COPY translations ./translations
COPY assets ./assets
COPY importmap.php ./

RUN APP_SECRET=build-time-secret composer install --no-dev --no-interaction --no-progress --prefer-dist --optimize-autoloader \
    && APP_SECRET=build-time-secret php bin/console asset-map:compile --env=prod \
    && mkdir -p var/cache var/log \
    && chown -R www-data:www-data var public/assets

COPY docker/entrypoint.sh /usr/local/bin/aphrodite-entrypoint
RUN chmod +x /usr/local/bin/aphrodite-entrypoint

EXPOSE 8000

ENTRYPOINT ["aphrodite-entrypoint"]
CMD ["apache2-foreground"]
