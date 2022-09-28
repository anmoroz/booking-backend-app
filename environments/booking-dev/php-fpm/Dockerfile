FROM php:8.1-fpm-alpine
ARG TIMEZONE

COPY ./php-fpm/php.ini /usr/local/etc/php/conf.d/docker-php-config.ini

RUN docker-php-ext-install pdo pdo_mysql

RUN apk update && apk add --no-cache \
    bash \
    curl \
   $PHPIZE_DEPS

# Set timezone
RUN ln -snf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime && echo ${TIMEZONE} > /etc/timezone \
    && printf '[PHP]\ndate.timezone = "%s"\n', ${TIMEZONE} > /usr/local/etc/php/conf.d/tzone.ini \
    && "date"


# Xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
     --install-dir=/usr/local/bin --filename=composer\
  && composer --version \

RUN export PATH=$PATH":/usr/bin"

RUN apk add bash

WORKDIR /var/www/booking-backend-app