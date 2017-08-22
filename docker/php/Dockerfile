FROM ubuntu:16.04

RUN echo 'Europe/Moscow' | tee /etc/timezone \
    && apt-get update \
    && apt-get -yq install php7.0 \
        php7.0-common \
        php7.0-fpm \
        php7.0-cli \
        php7.0-mysql \
    && mkdir -p /var/run/php

ADD files /

EXPOSE 9000

CMD ["php-fpm7.0"]