[![License](https://img.shields.io/badge/license-Apache-blue.svg?style=flat-square)](LICENSE-2.0.txt)

# Movie API

A lumen RESTful API which provides methods for storing, categorising and retrieving Movie information.

## Docker build

The docker container can be started by running following from project root:

```
./run.sh
```

The docker container listens on http://localhost:8000

## Contributing

### OS dependencies

Following requirements for developing on Debian Jessie 8.9:

- php7.1
- php7.1-xdebug
- php7.1-xml
- php7.1-mbstring
- php7.1-mysql (if using MySQL database)
- curl
- composer

### Setting up environment

Copy .env.example and set environment variables as per development environment:

```
cp .env.example .env
```

_Don't forget to create a database with the database name specified in `.env`_

To setup development environment run:

```
composer install
php artisan doctrine:migrations:migrate
php artisan doctrine:generate:proxies
```

### Seeding database

Database can be seeded by running:

```
php artisan db:seed --class=Uma\\Database\\Seeds\\DatabaseSeeder
```

The seeded user is:

```
username: apitest
password: password123
```

### Generating documentation

Documentation can be generated using the swagger-php plugin:

```
./vendor/bin/swagger -o docs/ src/
```

The output `swagger.json` file can be found in `docs/`.

### Running tests

Running unit + component + integration tests can be done from project root with:

```
./vendor/bin/phpunit
```

API tests can be found in (to see examples of testing lumen controllers):

```
tests/Infrastructure/Http/Controllers/
```

**Note: index tests will fail if database has been seeded - database needs to be emptied before running tests**
