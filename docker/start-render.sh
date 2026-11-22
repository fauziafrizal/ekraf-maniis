#!/bin/sh
set -eu

# Render provides PORT (usually 10000). Apache must listen on that port.
PORT="${PORT:-10000}"
sed -i -E "s/^Listen [0-9]+$/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

if [ -z "${APP_KEY:-}" ]; then
    echo "ERROR: APP_KEY must be set in Render Environment Variables."
    exit 1
fi

mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache database

# SQLite is only for a disposable demo on Render Free.
# Database files and uploaded images do NOT persist across restarts.
if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    touch database/database.sqlite
fi

chown -R www-data:www-data storage bootstrap/cache database

php artisan migrate --force --no-interaction

if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    php artisan db:seed --force --no-interaction
fi

if [ ! -e public/storage ]; then
    php artisan storage:link
fi

php artisan config:cache
php artisan view:cache

exec apache2-foreground
