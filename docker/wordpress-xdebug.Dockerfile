FROM wordpress:php8.2-apache

RUN printf '\nListen 8080\n' >> /etc/apache2/ports.conf \
    && sed -i 's/<VirtualHost \*:80>/<VirtualHost *:80 *:8080>/' /etc/apache2/sites-available/000-default.conf

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

COPY docker/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
