FROM opendatastack/apache-php-fpm:latest
MAINTAINER Willie Seabrook<willie@angrycactus.io>

COPY ./src/dkan-opendatastack/src/dkan_starter /var/www/html
RUN chown www-data:www-data -R /var/www/html
