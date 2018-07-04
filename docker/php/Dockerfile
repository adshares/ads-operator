FROM php:7.2-fpm

RUN apt-get update && apt-get install -y \
    openssl \
    git \
    unzip \
    vim

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer --version

RUN mkdir -p /ads-manager/var/cache
RUN mkdir -p /ads-manager/var/log

COPY . /ads-manager
WORKDIR /ads-manager

RUN usermod -u 1000 www-data
RUN chown -R www-data:www-data /ads-manager/var/cache
RUN chown -R www-data:www-data /ads-manager/var/log