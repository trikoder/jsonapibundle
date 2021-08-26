ARG PHP_VERSION_MAJOR
ARG PHP_VERSION_MINOR

FROM phpdockerio/php${PHP_VERSION_MAJOR}${PHP_VERSION_MINOR}-fpm

ARG PHP_VERSION_MAJOR
ARG PHP_VERSION_MINOR

MAINTAINER Alen Pokos <alen.pokos@trikoder.net>

# xdebug for code coverage
RUN apt-get update \
    && apt-get -y --no-install-recommends install \
        php${PHP_VERSION_MAJOR}.${PHP_VERSION_MINOR}-xdebug \
        php${PHP_VERSION_MAJOR}.${PHP_VERSION_MINOR}-mysql \
        php${PHP_VERSION_MAJOR}.${PHP_VERSION_MINOR}-mbstring \
    && echo 'xdebug.mode=coverage' > /etc/php/${PHP_VERSION_MAJOR}.${PHP_VERSION_MINOR}/mods-available/xdebug.ini

# show php version
RUN php -v
RUN php -m

# Composer
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer && \
    chmod +x /usr/local/bin/composer

# Symfony
RUN curl -sS https://get.symfony.com/cli/installer | bash && \
    mv /root/.symfony/bin/symfony /usr/local/bin/symfony

WORKDIR /usr/src/bundle
