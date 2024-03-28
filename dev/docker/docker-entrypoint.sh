#!/bin/sh
set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
    set -- php "$@"
fi

if [ "$1" = 'php' ]; then
    composer install --prefer-dist --no-progress --no-interaction --no-plugins --ignore-platform-reqs
    echo "Container is ready!"
fi

exec docker-php-entrypoint "$@"