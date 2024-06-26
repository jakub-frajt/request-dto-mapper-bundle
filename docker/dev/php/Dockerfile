FROM php:8.1.29-cli-alpine3.20

ARG uid
ARG gid

ENV APP_USER=app \
    APP_UID=$uid \
    APP_GID=$gid \
    APP_GROUP=app \
    APP_DIR=/app \
    COMPOSER_VERSION=2.7.7 \
    COMPOSER_CACHE_DIR=/app/.composer

RUN addgroup -S -g "$APP_GID" "$APP_GROUP" \
    && adduser -u "$APP_UID" -SDH "$APP_USER" "$APP_GROUP" \
    && mkdir -p "$APP_DIR" \
    && chown "$APP_USER:$APP_GROUP" "$APP_DIR" \
    && chmod -R 755 "$APP_DIR"

RUN curl --silent --show-error https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --version="$COMPOSER_VERSION"

WORKDIR $APP_DIR
USER $APP_USER