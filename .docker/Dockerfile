ARG PHP_VERSION=8.3
FROM dunglas/frankenphp:php${PHP_VERSION}

RUN install-php-extensions \
    intl \
    gd \
    mysqli \
    pdo_mysql \
    zip \
    bcmath \
    exif \
    pcov

RUN echo 'pcov.directory=/module/src' > /usr/local/etc/php/conf.d/99-pcov.ini

# Coverage over the full suite exceeds PHP's 128M default under phpunit 12 /
# php-code-coverage 12 — it OOMs partway through, and only on some
# `executionOrder="random"` seeds, so it reads as a flaky test rather than a
# memory ceiling. This image is the test/dev harness only.
RUN echo 'memory_limit=512M' > /usr/local/etc/php/conf.d/99-memory.ini

# Major-pinned: :latest would silently adopt a future Composer major release
# mid-build, and the binary it copies runs against this module's composer.json.
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Halved download concurrency trades a few seconds of boot for markedly fewer
# resets and 429s on shared-IP CI runners. NO_AUDIT drops one more round trip —
# this image is the throwaway test harness.
ENV COMPOSER_MAX_PARALLEL_HTTP=6 \
    COMPOSER_NO_AUDIT=1

WORKDIR /app

# The lock is committed so the boot install replays a known set instead of
# re-resolving against Packagist on every container start. `task relock`
# writes it (a weekly workflow does too); nothing else may.
COPY app/composer.json /app/composer.json
COPY app/composer.lock /app/composer.lock

# SilverStripe requires Page and PageController in the project. They are
# generated here, not stored in the repo, to avoid duplicate class detection
# when the module is symlinked into vendor/. The _config dir makes /app a
# module, so /app/src is scanned by the class manifest.
RUN mkdir -p /app/src /app/_config \
    && printf '<?php\n\nuse SilverStripe\\CMS\\Model\\SiteTree;\n\nclass Page extends SiteTree\n{\n}\n' > /app/src/Page.php \
    && printf '<?php\n\nuse SilverStripe\\CMS\\Controllers\\ContentController;\n\nclass PageController extends ContentController\n{\n}\n' > /app/src/PageController.php

# Harness-only page types and config; merges into the generated dirs above.
COPY app/src/ /app/src/
COPY app/_config/ /app/_config/

COPY app/templates/ /app/templates/
COPY app/verify-lock.sh /app/verify-lock.sh
RUN chmod +x /app/verify-lock.sh

COPY Caddyfile /etc/caddy/Caddyfile
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]
