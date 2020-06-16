#!/usr/bin/env sh

set -e

# Runs application with nginx and FPM and makes some preparations for application
# migrations, folders structure, permissions, etc.
sleep 10
file=./.env
if test -f "$file"; then
    echo ".env exists."
else
    echo create .env file
    touch .env
    a=$DATABASE_URL
    b='DATABASE_URL='
    echo "${b}\"${a}\"" >> .env
fi
echo Run migrations
vendor/bin/doctrine orm:schema-tool:update --force --dump-sql
echo Run seeds
php ./database/init.php
echo Give permissions to www-data to mounted volume directories
# "/var/tmp/nginx" owned by "nginx" user is unusable on heroku dyno so re-create on runtime
mkdir /var/tmp/nginx

touch /etc/nginx/conf.d/default.template && \
    envsubst '\$PORT' < /etc/nginx/conf.d/default.conf > /etc/nginx/conf.d/default.template && \
    mv /etc/nginx/conf.d/default.template /etc/nginx/conf.d/default.conf && \
    php-fpm -D -R && \
    nginx
    