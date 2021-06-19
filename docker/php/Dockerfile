FROM php:7.4-cli
RUN apt-get -y clean \
    && apt-get -y update \
    && apt-get install -y libzip-dev \
    && docker-php-ext-install opcache \
    && docker-php-ext-install zip \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer