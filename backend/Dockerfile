FROM php:8.3-fpm-alpine AS base

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    icu-dev \
    oniguruma-dev

# Install PHP extensions
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pgsql \
    zip \
    intl \
    mbstring \
    opcache

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# ─── Development stage ────────────────────────────────────────────────────────
FROM base AS dev

ENV APP_ENV=dev
ENV PHP_IDE_CONFIG="serverName=optifleet"

# Install Xdebug for coverage
RUN apk add --no-cache $PHPIZE_DEPS linux-headers \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

COPY docker/php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
COPY docker/php/php.ini /usr/local/etc/php/conf.d/app.ini

COPY composer.json composer.lock ./
RUN composer install --prefer-dist --no-scripts --no-interaction

COPY . .
RUN composer run-script post-install-cmd --no-interaction || true

EXPOSE 8000
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]

# ─── Production stage ─────────────────────────────────────────────────────────
FROM base AS prod

ENV APP_ENV=prod

COPY docker/php/php.prod.ini /usr/local/etc/php/conf.d/app.ini

COPY composer.json composer.lock ./
RUN composer install --prefer-dist --no-dev --no-scripts --no-interaction --optimize-autoloader

COPY . .
RUN composer dump-autoload --optimize --no-dev \
    && php bin/console cache:warmup --env=prod

EXPOSE 8000
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
