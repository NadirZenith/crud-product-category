FROM php:7.4-fpm-alpine3.10

RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /srv
COPY srv /srv

CMD ["php-fpm"]
