#!/usr/bin/env bash

set -e

env | sort

if [ ! -v TRAVIS ]; then
  # Checkout repo and change directory

  # Install git
  git --version || apt-get install -y git

  git clone \
    --depth=1 \
    https://github.com/adshares/ads-operator.git \
    --branch ${ADS_OPERATOR_BRANCH} \
    ${BUILD_PATH}/build

  cd ${BUILD_PATH}/build
fi

envsubst < .env.dist | tee .env

composer install --no-ansi --no-scripts --no-interaction --no-progress --no-suggest

pecl install mongodb
echo 'extension=mongodb.so' > /etc/php/7.2/mods-available/mongodb.ini
phpenmod mongodb
