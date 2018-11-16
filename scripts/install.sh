#!/usr/bin/env bash

set -e

# Install mongo

mv mongodb.so /usr/lib/php/20170718/mongodb.so
echo 'extension=mongodb.so' > /etc/php/7.2/mods-available/mongodb.ini
phpenmod mongodb

${INSTALLATION_PATH}/bin/console doctrine:mongodb:schema:create

TEMP_CRON_FILE=`mktemp`

## Configure cron
# Dump current cron
crontab -l > ${TEMP_CRON_FILE}

# Add new config
echo  "*/5 * * * * \
      date >> ${INSTALLATION_PATH}/var/log/import.log \
      && \
      ${INSTALLATION_PATH}/bin/console ads:import \
      >> ${INSTALLATION_PATH}/var/log/import.log \
      2>&1" >> ${TEMP_CRON_FILE}

# Install new cron
crontab ${TEMP_CRON_FILE}
# Cleanup
rm ${TEMP_CRON_FILE}
