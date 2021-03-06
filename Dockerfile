FROM php:7.4-fpm-alpine

RUN apk add \
    libzip libzip-dev \
    nginx && \
    docker-php-ext-install bcmath zip mysqli pdo_mysql && \
    apk del libzip-dev && \
    mv /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini && \
    curl -s https://getcomposer.org/installer | php &&\
    mv composer.phar /usr/local/bin/composer && \
    rm -vrf /var/cache/apk/*

COPY .docker/ .docker/

RUN chmod +x .docker/entrypoint.sh && \
    mv .docker/entrypoint.sh /usr/local/bin/entrypoint && \
    # chmod -R 777 /var/tmp/nginx && \
    rm -rf /usr/local/etc/php-fpm.d && \
    mv .docker/php/php-fpm.d /usr/local/etc/php-fpm.d && \
    mv -f .docker/php/php-fpm.conf /usr/local/etc/php-fpm.conf && \
    cp -rf .docker/nginx /etc && \
    rm -rf .docker

WORKDIR /home/www-data
COPY src ./

RUN ls -la && composer install

USER root
#RUN chown -R www-data:www-data ./

ENTRYPOINT "entrypoint"
