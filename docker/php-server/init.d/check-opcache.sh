#!/usr/bin/env bash

set -e

if [[ "$ENABLE_OPCACHE" = "yes" ]]; then
  sed -i 's/opcache.enable=0/opcache.enable=1/' /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini
fi