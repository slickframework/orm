#!/usr/bin/env bash
set -e

if [[ "$ENABLE_XDEBUG_FOR_MAC" = "yes" ]]; then
    XDEBUG_MODE=${XDEBUG_MODE:-'debug,develop'}
    cat > /usr/local/etc/php/conf.d/xdebug.ini << EOL

[xdebug]
xdebug.mode=${XDEBUG_MODE}
xdebug.start_with_request=yes
xdebug.client_host=host.docker.internal
xdebug.client_port=9003
xdebug.idekey=PHPSTORM
xdebug.max_nesting_level=1500
xdebug.output_dir=/var/www/app/tmp
xdebug.discover_client_host=0
xdebug.profiler_append=0

EOL

    mkdir -p /usr/local/etc/php/xdebug.d && \
    mv /usr/local/etc/php/conf.d/*xdebug.ini /usr/local/etc/php/xdebug.d/
fi