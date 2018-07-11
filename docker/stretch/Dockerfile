FROM php:apache

MAINTAINER kyane@kyane.fr

RUN apt-get -y update && DEBIAN_FRONTEND=noninteractive apt-get install -y -qq zip unzip git zlib1g-dev libicu-dev g++ mysql-client
RUN docker-php-ext-install intl && docker-php-ext-install pdo_mysql


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

COPY . /var/www/framadate

WORKDIR /var/www/framadate

ARG FRAMADATE_VERSION=develop

# Some Apache and PHP configuration
RUN if [ "$ENV" = "dev" ] ; then echo Using PHP production mode ; else echo Using PHP development mode && echo "error_reporting = E_ERROR | E_WARNING | E_PARSE\ndisplay_errors = On" > /usr/local/etc/php/conf.d/php.ini ; fi

# # Install Framadate
# RUN git clone https://github.com/framasoft/framadate.git /var/www/framadate && cd /var/www/framadate \
#     && touch admin/stdout.log && chmod 700 admin/stdout.log && mkdir tpl_c \
#     && git checkout $FRAMADATE_VERSION

RUN cd /var/www/framadate && if [ "$ENV" = "dev" ] ; then echo Installing PHP development dependencies && composer install --no-interaction --no-progress ; else echo Installing PHP production dependencies && composer install -o  --no-interaction --no-progress --prefer-dist --no-dev \
    && composer dump-autoload --optimize --no-dev --classmap-authoritative ; fi

RUN rm /etc/apache2/sites-enabled/000-default.conf
EXPOSE 80
ENTRYPOINT ["entrypoint"]
