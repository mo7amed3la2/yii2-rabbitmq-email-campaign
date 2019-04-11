FROM ubuntu:16.04

ENV LANG C.UTF-8

RUN apt-get -yq update \
    && apt-get -yq install software-properties-common \
    && add-apt-repository ppa:ondrej/php \
    && apt-get -yq install php7.0 \
        php7.0-common \
        php7.0-fpm \
        php7.0-cli \
        php7.0-bcmath \
        php7.0-mbstring \
        php7.0-dom \
        php7.0-zip \
        php-xdebug \
        wget \
        git \
    && mkdir -p /var/run/php

ADD files /

# Composer
RUN wget -q -O composer-setup.php https://getcomposer.org/installer \
    && php composer-setup.php \
    && rm composer-setup.php \
    && mv composer.phar /usr/local/bin/composer \
    && chmod +x /usr/local/bin/composer

RUN composer global require "fxp/composer-asset-plugin:^1.3.1"

EXPOSE 9000

WORKDIR /var/www

CMD ["php-fpm7.0"]
