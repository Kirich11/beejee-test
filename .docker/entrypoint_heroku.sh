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

# make php-fpm be able to listen request from nginx (current user is nginx executor)
sed -i -E "s/^;listen.owner = .*/listen.owner = $(whoami)/" /usr/local/etc/php-fpm.d/www.conf

# make current user the executor of nginx and php-fpm expressly for local environment
sed -i -E "s/^user = .*/user = $(whoami)/" /usr/local/etc/php-fpm.d/www.conf
sed -i -E "s/^group = (.*)/;group = \1/" /usr/local/etc/php-fpm.d/www.conf
sed -i -E "s/^user .*/user $(whoami);/" /etc/nginx/nginx.conf

touch /etc/nginx/conf.d/default.template && \
    envsubst '\$PORT' < /etc/nginx/conf.d/default.conf > /etc/nginx/conf.d/default.template && \
    mv /etc/nginx/conf.d/default.template /etc/nginx/conf.d/default.conf && \
    nginx && \
    php-fpm restart