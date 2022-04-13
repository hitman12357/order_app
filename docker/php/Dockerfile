FROM php:7.4-fpm

MAINTAINER Stefanie Drost <contact@stefaniedrost.com>

WORKDIR /var/www/html

# install git
RUN apt-get update && \
    apt-get install -y --no-install-recommends git

RUN usermod -u 1000 www-data

#install some base extensions
RUN apt-get update && \
    apt-get install -y \
        build-essential \
        libssl-dev \
        zlib1g-dev \
        libzip-dev \
        libwebp-dev \
        zip \
        unzip \
        libpng-dev \
        libjpeg-dev \
        exiftool \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libicu-dev \
        libpq-dev \
        libxpm-dev \
        libvpx-dev \
        mariadb-client \
        libxml2-dev \
        wget \
        cron

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --version=1.10.16 --install-dir=/usr/local/bin --filename=composer
RUN composer --version

# Configure GD
RUN docker-php-ext-configure gd \
    --with-freetype --with-webp --with-jpeg \
   && docker-php-ext-install gd

RUN docker-php-ext-install -j$(nproc) \
   exif \
   bcmath \
   intl \
   pcntl \
   mysqli \
   pdo \
   pdo_mysql \
   pdo_pgsql \
   soap \
   opcache

# install xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && echo "zend_extension=$(find /usr/local/lib/php/ -name xdebug.so)" > /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_autostart=on" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.profiler_enable=0" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_port=9999" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.idekey=PHPSTORM" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "display_startup_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "display_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN touch /var/log/cron.log &&\
    touch /etc/cron.d/crontab &&\
    echo "PATH=/usr/sbin:/usr/bin:/sbin:/bin:/usr/local/bin" >> /etc/cron.d/crontab &&\
    echo "* * * * * root /var/www/html/bin/console app:delay-orders" >> /etc/cron.d/crontab &&\
    echo "# Don't remove the empty line at the end of this file. It is required to run the cron job" >> /etc/cron.d/crontab &&\
    crontab /etc/cron.d/crontab &&\
    crontab -

RUN cd /var/www/html/ && composer install

EXPOSE 9000

CMD ["php-fpm"]
