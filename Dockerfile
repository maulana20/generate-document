FROM php:8.1-fpm

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN apt-get update && \
    apt-get install ghostscript libreoffice libzip-dev -y libmagickwand-dev --no-install-recommends && \
    rm -rf /var/lib/apt/lists/*

# imagick
RUN mkdir -p /usr/src/php/ext/imagick; \
    curl -fsSL https://github.com/Imagick/imagick/archive/06116aa24b76edaf6b1693198f79e6c295eda8a9.tar.gz | tar xvz -C "/usr/src/php/ext/imagick" --strip 1; \
    docker-php-ext-install imagick gd zip;

# php.ini
RUN cp "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini" && \
    sed -i -e 's/^display_errors = On$/display_errors = Off/g' "$PHP_INI_DIR/php.ini" && \
    sed -i -e 's/^error_reporting = E_ALL$/error_reporting = E_ERROR/g' "$PHP_INI_DIR/php.ini"

WORKDIR /app

COPY ./generate ./

ENV COMPOSER_ALLOW_SUPERUSER=1
RUN /app/startup.sh
