FROM php:7.4-cli-alpine AS builder

# pg drivers
RUN apk add postgresql-dev bash coreutils \
    && docker-php-ext-configure pgsql --with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo_pgsql

RUN apk add --no-cache unzip

# install composer
ENV COMPOSER_ALLOW_SUPERUSER 1
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/bin --filename=composer --quiet

WORKDIR /app

COPY ./composer.json ./composer.lock ./

RUN composer install --prefer-dist --no-progress --no-suggest --optimize-autoloader \
    && rm -rf /root/.composer/cache


FROM php:7.4-cli-alpine

RUN apk add --no-cache postgresql-dev bash coreutils \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo_pgsql opcache

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/bin --filename=composer --quiet

# make php.ini
RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

# copy local php config to container config
COPY ./docker/testing/php/conf.d /usr/local/etc/php/conf.d

# wait for it
COPY ./docker/common/wait-for-it.sh /usr/local/bin/wait-for-it
RUN chmod 555 /usr/local/bin/wait-for-it

# add user
RUN adduser -D -u 1000 nana

WORKDIR /app

COPY --from=builder /app ./
COPY ./ ./

USER nana
