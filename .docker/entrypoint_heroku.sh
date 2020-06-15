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
env
envsubst '\$PORT' < /etc/nginx/conf.d/default.conf > /etc/nginx/conf.d/default.template  && mv /etc/nginx/conf.d/default.template /etc/nginx/conf.d/default.conf
cat /etc/nginx/conf.d/default.template
cat /etc/nginx/conf.d/default.conf
echo Run migrations
vendor/bin/doctrine orm:schema-tool:update --force --dump-sql
echo Run seeds
php ./database/init.php
echo Give permissions to www-data to mounted volume directories
#chown -R www-data: /home/www-data/public
#chown -R www-data: /home/www-data/storage
echo "Check nginx is fine"
nginx -t
echo "Run FPM"
php-fpm
echo "Run NGINX as root process"
nginx