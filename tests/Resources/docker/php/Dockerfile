FROM phpdockerio/php71-fpm

MAINTAINER Alen Pokos <alen.pokos@trikoder.net>

# xdebug for code coverage
RUN apt-get update \
    && apt-get -y --no-install-recommends install php7.1-xdebug php7.1-mysql php7.1-mbstring

# Composer
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer && \
    chmod +x /usr/local/bin/composer

WORKDIR /usr/src/bundle
