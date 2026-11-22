# Build CSS and JavaScript assets with Vite.
FROM node:22-bookworm-slim AS frontend
WORKDIR /app
COPY package.json package-lock.json .npmrc ./
RUN npm ci
COPY . .
RUN npm run build

# Serve Laravel 13 with PHP 8.4 and Apache.
FROM php:8.4-apache-bookworm

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
       libicu-dev libonig-dev libpq-dev libsqlite3-dev libzip-dev unzip git \
    && docker-php-ext-install -j"$(nproc)" intl mbstring opcache pdo_mysql pdo_pgsql pdo_sqlite zip \
    && rm -rf /var/lib/apt/lists/* \
    && a2enmod rewrite headers \
    && sed -ri 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
    && printf '%s\n' \
       '<Directory /var/www/html/public>' \
       '    Options FollowSymLinks' \
       '    AllowOverride All' \
       '    Require all granted' \
       '</Directory>' > /etc/apache2/conf-available/laravel.conf \
    && a2enconf laravel \
    && printf '%s\n' 'ServerName localhost' > /etc/apache2/conf-available/servername.conf \
    && a2enconf servername

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
WORKDIR /var/www/html
COPY . .

RUN mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache database \
    && composer install --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader --no-scripts \
    && php artisan package:discover --ansi

COPY --from=frontend /app/public/build /var/www/html/public/build
COPY docker/start-render.sh /usr/local/bin/start-render.sh
RUN chmod +x /usr/local/bin/start-render.sh \
    && chown -R www-data:www-data storage bootstrap/cache database

EXPOSE 10000
CMD ["/usr/local/bin/start-render.sh"]
