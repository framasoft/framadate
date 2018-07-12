FROM php:apache

MAINTAINER kyane@kyane.fr

RUN apt-get -y update && DEBIAN_FRONTEND=noninteractive apt-get install -y -qq zip unzip git zlib1g-dev libicu-dev g++ mysql-client
RUN docker-php-ext-install intl && docker-php-ext-install pdo_mysql

RUN a2enmod rewrite

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY docker/stretch/php.ini /usr/local/etc/php/php.ini
COPY docker/stretch/apache-framadate.conf /etc/apache2/sites-enabled/framadate.conf
COPY docker/stretch/entrypoint.sh /usr/local/bin/entrypoint

ENV COMPOSER_ALLOW_SUPERUSER=1
RUN set -eux; \
    composer global require "hirak/prestissimo:^0.3" --prefer-dist --no-progress --no-suggest --classmap-authoritative; \
    composer clear-cache
ENV PATH="${PATH}:/root/.composer/vendor/bin"
ENV COMPOSER_ALLOW_SUPERUSER 0

WORKDIR /var/www/framadate

# Some Apache and PHP configuration
RUN if [ "$ENV" = "dev" ] ; then echo Using PHP production mode ; else echo Using PHP development mode && echo "error_reporting = E_ERROR | E_WARNING | E_PARSE\ndisplay_errors = On" > /usr/local/etc/php/conf.d/php.ini ; fi

RUN rm /etc/apache2/sites-enabled/000-default.conf
EXPOSE 80
ENTRYPOINT ["entrypoint"]
