#!/bin/bash
set -e
php artisan down
php artisan migrate --force
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

if [ -z "$DEV_MODE" ]; then
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

php artisan up

exec "/usr/local/bin/docker-php-entrypoint" "$@"
