#!/usr/bin/env bash

${ADS_OPERATOR_INSTALLATION_DIR}/bin/console doctrine:mongodb:schema:create

TEMP_CRON_FILE=`mktemp`

## Configure cron
# Dump current cron
crontab -l > ${TEMP_CRON_FILE}

# Add new config
echo  "*/5 * * * * \
      date >> ${ADS_OPERATOR_INSTALLATION_DIR}/var/log/import.log \
      && \
      ${ADS_OPERATOR_INSTALLATION_DIR}/bin/console ads:import \
      >> ${ADS_OPERATOR_INSTALLATION_DIR}/var/log/import.log \
      2>&1" >> ${TEMP_CRON_FILE}

# Install new cron
crontab ${TEMP_CRON_FILE}
# Cleanup
rm ${TEMP_CRON_FILE}
