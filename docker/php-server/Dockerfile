FROM fsilva/php:8.2-apache

ARG ENABLE_XDEBUG_FOR_MAC=no

# Add init scripts
COPY init.d /docker-entrypoint-init.d/
COPY etc /usr/local/etc

WORKDIR /var/www/app

ENV PATH=./vendor/bin:/var/www/.composer/vendor/bin:/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin


CMD ["apache2-foreground"]
