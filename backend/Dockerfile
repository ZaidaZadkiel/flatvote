#FROM phpearth/php:7.4-nginx
FROM php:8.1-fpm


# install composer from official image
#COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo_mysql



# install composer dependencies
# COPY ./composer.lock composer.lock
# COPY ./composer.json composer.json
# RUN composer install --no-scripts --no-autoloader --no-dev

# copy project files
COPY . .

# change public path to html
RUN cp -R public/* ./

# generate autoload files
# RUN composer dump-autoload --optimize
