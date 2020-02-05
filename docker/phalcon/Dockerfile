FROM php:7.4-fpm

RUN apt-get update
RUN apt-get install -y libpcre3-dev libzip-dev postgresql-server-dev-11 zlib1g-dev libicu-dev libbz2-dev \
        curl libcurl4-openssl-dev curl git gcc make autoconf libc-dev pkg-config software-properties-common \
        locales nginx
RUN docker-php-ext-install -j$(nproc) iconv pdo_pgsql zip intl bz2 bcmath gettext curl

RUN git clone https://github.com/phalcon/cphalcon
RUN cd cphalcon/build && ./install
RUN pecl install psr
RUN docker-php-ext-enable phalcon psr

COPY ./default.conf /etc/nginx/sites-available/default
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /var/www/app
CMD service nginx start && php-fpm
