#!/bin/sh
set -e

php artisan uma:database:ping
php artisan doctrine:migrations:migrate
php artisan doctrine:generate:proxies
php artisan db:seed --class=Uma\\Database\\Seeds\\DatabaseSeeder

docker-php-entrypoint apache2-foreground
