#!/bin/sh
set -e

php artisan uma:database:ping
php artisan doctrine:migrations:migrate
php artisan doctrine:generate:proxies

docker-php-entrypoint apache2-foreground
