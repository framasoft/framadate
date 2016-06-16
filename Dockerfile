FROM library/php:5-fpm

RUN apt update
RUN apt install -y git unzip libicu-dev libpq-dev --no-install-recommends
RUN apt-get clean

RUN docker-php-ext-install intl pgsql pdo_pgsql pdo_mysql

RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/bin/composer
