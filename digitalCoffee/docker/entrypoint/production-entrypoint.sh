#!/bin/sh
set -eu

mkdir -p \
    bootstrap/cache \
    storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/testing \
    storage/framework/views \
    storage/logs
chown -R www-data:www-data bootstrap/cache storage

if [ "${APP_ENV:-production}" = "production" ] && [ -n "${APP_KEY:-}" ]; then
    php artisan optimize --no-interaction
fi

exec "$@"
