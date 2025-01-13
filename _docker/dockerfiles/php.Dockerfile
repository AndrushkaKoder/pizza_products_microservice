FROM php:8.2-fpm-alpine

WORKDIR /var/www/html

RUN docker-php-ext-install pdo pdo_mysql

RUN mkdir -p /var/www/html/public/upload

RUN chown -R www-data:www-data /var/www/html/public/upload/
RUN chmod -R 775 /var/www/html/public/upload/

RUN chmod -R 777 /tmp/