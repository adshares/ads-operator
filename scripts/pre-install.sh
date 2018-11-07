#!/usr/bin/env bash

set -e

apt-get -qq -y install php7.2-fpm php-pear

pecl install mongodb
echo 'extension=mongodb.so' > /etc/php/7.2/mods-available/mongodb.ini
phpenmod mongodb
