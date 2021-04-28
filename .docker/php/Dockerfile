FROM php:8.0-fpm

RUN apt-get update && apt-get install -my \
  curl \
  wget \
  git \
  zlib1g-dev \
  libzip-dev \
  libicu-dev \
  zip \
  librdkafka-dev \
  g++

RUN docker-php-ext-install -j$(nproc) \
    mysqli \
    pdo \
    pdo_mysql \
    zip \
    opcache \
    intl

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer
RUN chmod +x /usr/local/bin/composer
RUN ln -snf /usr/share/zoneinfo/Europe/Berlin /etc/localtime

RUN pecl install \ 
    xdebug \
    apcu \
    rdkafka

RUN docker-php-ext-enable \
    xdebug \
    apcu \
    intl \
    rdkafka

RUN echo 'xdebug.mode=debug' >> /usr/local/etc/php/php.ini
RUN echo 'xdebug.start_with_request=yes' >> /usr/local/etc/php/php.ini
RUN echo 'xdebug.discover_client_host=true' >> /usr/local/etc/php/php.ini
RUN echo 'xdebug.client_host=host.docker.internal' >> /usr/local/etc/php/php.ini

WORKDIR /var/www
