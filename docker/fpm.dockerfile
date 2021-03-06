FROM php:7.4.3-fpm

RUN buildDeps="libpq-dev libzip-dev libicu-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev libmagickwand-6.q16-dev libxslt-dev" && \
    apt-get update && \
    apt-get install -y $buildDeps --no-install-recommends && \
    docker-php-ext-install pdo pdo_mysql pdo_pgsql gd zip pcntl exif && \
    docker-php-ext-configure exif \
        --enable-exif

RUN docker-php-ext-install opcache  && \
     docker-php-ext-enable opcache

WORKDIR /var/www/laravel

COPY . /var/www/laravel

ADD "docker/php.ini" "/usr/local/etc/php/php.ini"

RUN chown -R www-data:www-data /var/www/laravel

EXPOSE 9000

CMD ["php-fpm"]
