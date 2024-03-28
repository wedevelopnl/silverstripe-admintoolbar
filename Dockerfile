# syntax=docker/dockerfile:1.4

FROM composer/composer:2-bin AS composer_upstream
FROM php:8.1-cli-alpine3.17 AS php-cli

RUN apk add php make --no-cache

WORKDIR /app

COPY --from=composer_upstream --link /composer /usr/bin/composer

COPY composer.json ./
ENV COMPOSER_ALLOW_SUPERUSER 1
RUN composer install --prefer-dist --no-progress --no-interaction --no-plugins --ignore-platform-reqs

COPY dev/docker/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]

CMD ["php"]