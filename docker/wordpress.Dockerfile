FROM wordpress:php8.2-apache

# WordPress is published on host port 8080, and Duplicator makes loopback
# requests to the site URL from inside the container. Listen on both ports.
RUN printf '\nListen 8080\n' >> /etc/apache2/ports.conf \
    && sed -i 's/<VirtualHost \*:80>/<VirtualHost *:80 *:8080>/' /etc/apache2/sites-available/000-default.conf
