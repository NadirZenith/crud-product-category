FROM php:7.4-fpm-alpine3.10

RUN docker-php-ext-install pdo pdo_mysql

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# XDEBUG debug only
ARG WITH_XDEBUG=false
RUN if [ $WITH_XDEBUG = "true" ] ; then \
    apk add --no-cache --update --virtual buildDeps autoconf gcc make g++; \
    pecl install xdebug-3.0.4; \
    docker-php-ext-enable xdebug; \

    echo "xdebug.mode=develop,debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "xdebug.log=/tmp/xdebug.log" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "xdebug.discover_client_host=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
   fi ;

WORKDIR /srv
COPY srv /srv

# override parent entry point
# composer install & run tests
ADD bin/entrypoint.sh /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]


CMD ["php-fpm"]
