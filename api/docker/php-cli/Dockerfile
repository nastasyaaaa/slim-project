FROM php:7.4-cli-alpine


# install xdebug
RUN apk update && apk add autoconf make g++ \
    && pecl install xdebug \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable xdebug

RUN apk add unzip

# pg drivers
RUN apk add postgresql-dev \
    && docker-php-ext-configure pgsql --with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo_pgsql

# make php.ini
RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

# copy local php config to container config
COPY ./php/conf.d /usr/local/etc/php/conf.d
COPY ./php-cli/conf.d /usr/local/etc/php/conf.d

# install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/bin --filename=composer --quiet

# add user
RUN adduser -D -u 1000 nana
USER nana

WORKDIR /app

RUN composer global require hirak/prestissimo --no-plugins --no-scripts \
    && rm -rf /home/nana/.composer/cache
