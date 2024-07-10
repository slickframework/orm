#!/bin/bash
set -e

# Set Apache public
if [ -f /etc/apache2/sites-available/000-default.conf ]; then
sed -i -e "s|webroot|${WEBROOT}|g" /etc/apache2/sites-available/000-default.conf
fi