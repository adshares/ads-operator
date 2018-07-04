FROM php:7.2-fpm
ARG TIMEZONE

RUN apt-get update && apt-get install -y openssl git unzip vim zlib1g-dev

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer --version

# Set timezone
RUN ln -snf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime && echo ${TIMEZONE} > /etc/timezone
RUN printf '[PHP]\ndate.timezone = "%s"\n', ${TIMEZONE} > /usr/local/etc/php/conf.d/tzone.ini
RUN "date"

# Type docker-php-ext-install to see available extensions
RUN docker-php-ext-install zip

RUN mkdir -p /ads-manager/var/cache
RUN mkdir -p /ads-manager/var/log

COPY . /ads-manager
WORKDIR /ads-manager

RUN usermod -u 1000 www-data
RUN chown -R www-data:www-data /ads-manager/var/cache
RUN chown -R www-data:www-data /ads-manager/var/log
