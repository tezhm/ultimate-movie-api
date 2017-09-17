To start docker container run (needs docker group permissions):

./run.sh

Tested on debian jessie 8.9.

Requires following dependencies:
- php7.1
- php7.1-xdebug
- php7.1-xml
- php7.1-mbstring
- php7.1-mysql
- curl
- composer

Copy .env.example and set environment variables as per development environment:

    cp .env.example .env

Don't forget to create a database with the database name specified in .env

To setup development environment run:

    composer install
    php artisan doctrine:migrations:migrate
    php artisan doctrine:generate:proxies

To seed database:

    php artisan db:seed --class=Uma\\Database\\Seeds\\DatabaseSeeder

To run unit + component + integration tests:

    ./vendor/bin/phpunit 

API tests can be found in:

    tests/Infrastructure/Http/Controllers/

Note: tests will fail if seed database command has been run - you will need to empty database before running tests

----

The seeded user is:
    username: apitest
    password: password123
