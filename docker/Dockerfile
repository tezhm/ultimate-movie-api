FROM php:7.1-apache

LABEL maintainer="tezmarks@gmail.com"

RUN apt-get -qq update && apt-get -qq upgrade && \
    apt-get -qq install ca-certificates && \

    # Composer
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php -r "if (hash_file('SHA384', 'composer-setup.php') === '544e09ee996cdf60ece3804abc52599c22b1f40f4323403c44d44fdfdd586475ca9813a858088ffbc1f233e9b180f061') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" && \
    php composer-setup.php --install-dir=/usr/bin/ --filename=composer && \
    php -r "unlink('composer-setup.php');" && \

    # PHP modules
    apt-get -qq install libmcrypt-dev mysql-client git zip && \
    docker-php-ext-install mcrypt mbstring pdo_mysql

COPY . /var/www/html
COPY .env.example /var/www/html/.env
COPY docker/apache2.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html
RUN composer install && \
    chown -R www-data:www-data /var/www/html/storage/ && \
    a2enmod rewrite

ENTRYPOINT ["docker/entrypoint.sh"]
