[![Quality Status](https://sonarcloud.io/api/project_badges/measure?project=adshares-ads-manager&metric=alert_status)](https://sonarcloud.io/dashboard?id=adshares-ads-manager)
[![Build Status](https://travis-ci.org/adshares/ads-manager.svg?branch=master)](https://travis-ci.org/adshares/ads-manager#master "Master")
[![Build Status](https://travis-ci.org/adshares/ads-manager.svg?branch=develop)](https://travis-ci.org/adshares/ads-manager#develop "Develop")

# ADS Operator
Backend application which connects ADS and ADS Panel. It contains two parts:
1. REST API
2. Command to synchronize `blockexplorer` with `ADS blockchain` 

## Development
### Installation
To run project locally we use docker. Steps to run project locally:
1. Clone repository:  `git clone https://github.com/adshares/ads-manager.git`
2. Create configuration file `.env` based on `.env.dist` in the root of the application
3. Run docker containers with docker-compose: `docker-compose up -d --force-recreate`
4. Install all dependencies using composer: `docker-compose exec php composer install`

## Links
1. Local application url: http://127.0.0.1:8080
