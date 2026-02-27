FROM php:8.4-cli-alpine

RUN apk add --no-cache tzdata
ENV TZ=America/Sao_Paulo

RUN apk add --no-cache \
    postgresql-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    linux-headers \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    $PHPIZE_DEPS

RUN docker-php-ext-configure gd --with-freetype --with-jpeg

RUN docker-php-ext-install pdo_pgsql zip exif gd \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && pecl install pcov \
    && docker-php-ext-enable pcov

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]
