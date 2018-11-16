#!/usr/bin/env bash

set -e

apt-get -qq -y --no-install-recommends install \
    php7.2-fpm php-pear php7.2-dev

pecl install mongodb
echo 'extension=mongodb.so' > /etc/php/7.2/mods-available/mongodb.ini
phpenmod mongodb
