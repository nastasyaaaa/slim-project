FROM php:7.4-fpm-alpine

RUN apk update && apk add autoconf make g++ \
    && pecl install xdebug \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable xdebug

# pg drivers
RUN apk add postgresql-dev \
    && docker-php-ext-configure pgsql --with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo_pgsql


RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

COPY ./php/conf.d /usr/local/etc/php/conf.d
COPY ./php-fpm/conf.d /usr/local/etc/php/conf.d

# entrypoint to resolve internal network
COPY ./php-fpm/entrypoint.sh /usr/local/bin/docker-php-entrypoint
RUN chmod +x /usr/local/bin/docker-php-entrypoint

WORKDIR /cli

