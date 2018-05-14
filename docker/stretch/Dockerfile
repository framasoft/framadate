FROM php:apache

MAINTAINER kyane@kyane.fr

RUN apt-get -y update && DEBIAN_FRONTEND=noninteractive apt-get install -y -qq zip unzip git zlib1g-dev libicu-dev g++ mysql-client
RUN docker-php-ext-install intl && docker-php-ext-install pdo_mysql

RUN curl -s -f -L -o /tmp/installer.php https://getcomposer.org/installer \
 && php -r " \
    \$signature = '544e09ee996cdf60ece3804abc52599c22b1f40f4323403c44d44fdfdd586475ca9813a858088ffbc1f233e9b180f061'; \
    \$hash = hash('SHA384', file_get_contents('/tmp/installer.php')); \
    if (!hash_equals(\$signature, \$hash)) { \
        unlink('/tmp/installer.php'); \
        echo 'Integrity check failed, installer is either corrupt or worse.' . PHP_EOL; \
        exit(1); \
    }" \
 && php /tmp/installer.php --install-dir=/usr/bin --filename=composer \
 && rm -rf /tmp/installer.php

ARG FRAMADATE_VERSION=develop
ARG DEBUG=false

# Some Apache and PHP configuration
RUN if [ "$DEBUG" = false ] ; then echo Using PHP production mode ; else echo Using PHP development mode && echo "error_reporting = E_ERROR | E_WARNING | E_PARSE\ndisplay_errors = On" > /usr/local/etc/php/conf.d/php.ini ; fi

# Install Framadate
RUN git clone https://github.com/framasoft/framadate.git /var/www/framadate && cd /var/www/framadate \
 && touch admin/stdout.log && chmod 700 admin/stdout.log && mkdir tpl_c \
 && git checkout $FRAMADATE_VERSION

RUN cd /var/www/framadate && if [ "$DEBUG" = true ] ; then echo Installing PHP development dependencies && composer install --no-interaction --no-progress ; else echo Installing PHP production dependencies && composer install -o  --no-interaction --no-progress --prefer-dist --no-dev \
    && composer dump-autoload --optimize --no-dev --classmap-authoritative ; fi

COPY entrypoint.sh /entrypoint.sh
COPY apache-framadate.conf /etc/apache2/sites-enabled/framadate.conf
RUN rm /etc/apache2/sites-enabled/000-default.conf
EXPOSE 80
ENTRYPOINT ["/entrypoint.sh"]
