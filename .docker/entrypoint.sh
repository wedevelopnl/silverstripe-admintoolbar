#!/bin/sh
set -e

# Retry only composer's transport-failure exit (100); any other failure is
# deterministic and must surface immediately.
composer_install() {
    _attempt=1
    while :; do
        composer install --no-interaction && return 0
        _code=$?
        if [ "$_code" -ne 100 ] || [ "$_attempt" -ge 3 ]; then
            echo "composer install failed (exit ${_code}) after ${_attempt} attempt(s)" >&2
            return "$_code"
        fi
        echo "composer install hit a transport failure — retrying in $((_attempt * 5))s" >&2
        sleep $((_attempt * 5))
        _attempt=$((_attempt + 1))
    done
}

composer_install

# FrankenPHP ships a default phpinfo() index.php; replace it with the
# SilverStripe bootstrap.
cp -f vendor/silverstripe/recipe-core/public/index.php /app/public/index.php

# composer install skips vendor-expose when the named volume already holds the
# packages (no post-install event fires), so expose explicitly.
composer vendor-expose

# vendor-plugin resolves library paths with realpath(), which follows the
# vendor/wedevelopnl/silverstripe-admintoolbar → /module symlink. /module is
# outside /app, so the relative path breaks and the module's resources are never
# exposed. Create them manually.
_res=/app/public/_resources/vendor/wedevelopnl/silverstripe-admintoolbar
mkdir -p "$_res/client"
[ -d /module/client/dist ] && ln -sfn /module/client/dist "$_res/client/dist"

vendor/bin/sake dev/build flush=1

touch /tmp/.app-ready

exec frankenphp run --config /etc/caddy/Caddyfile
