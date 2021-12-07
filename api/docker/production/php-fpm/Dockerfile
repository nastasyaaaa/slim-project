FROM php:7.4-fpm-alpine

# pg drivers
RUN apk add postgresql-dev \
    && docker-php-ext-configure pgsql --with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo_pgsql


RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

COPY ./docker/production/php/conf.d /usr/local/etc/php/conf.d
COPY ./docker/production/php-fpm/conf.d /usr/local/etc/php/conf.d

# entrypoint to resolve internal network
COPY ./docker/production/php-fpm/entrypoint.sh /usr/local/bin/docker-php-entrypoint
RUN chmod +x /usr/local/bin/docker-php-entrypoint

WORKDIR /app
COPY ./ ./

