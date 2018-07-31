[![Quality Status](https://sonarcloud.io/api/project_badges/measure?project=adshares-ads-manager&metric=alert_status)](https://sonarcloud.io/dashboard?id=adshares-ads-manager)
[![Build Status](https://travis-ci.org/adshares/ads-operator.svg?branch=master)](https://travis-ci.org/adshares/ads-manager#master "Master")
[![Build Status](https://travis-ci.org/adshares/ads-operator.svg?branch=develop)](https://travis-ci.org/adshares/ads-manager#develop "Develop")

# ADS Operator
RESTful API to operate ADS blockchain data. 

## Getting Started

### Installing

1. Make sure you're using PHP (+mongodb module) 7.1 or higher and have Composer installed
1. Clone repository: `git clone https://github.com/adshares/ads-operator.git`
1. Create configuration file `.env` based on `.env.dist` in the root of the application
1. Create Behat configuration file `behat.yml` based on `behat.yml.dist` in the root of the application
1. Install all dependencies using composer: `composer install`
1. Add `127.0.0.1 ads-operator.ads` entry to your hosts

##### Nginx
* We assume that you've installed ADS Operator project in `/ads-operator` directory.

ads-operator.conf:
```
server {
	listen 80;

	server_name ads-operator.ads;
	root /ads-operator/public;

	index index.htm index.html index.php;

	location ~ /\.  {
		return 403;
	}

	location / {
		# try to serve file directly, fallback to rewrite
		try_files $uri $uri/ /index.php?$query_string;
	}

	location ~ \.php$ {
		include snippets/fastcgi-php.conf;
		fastcgi_pass unix:/run/php/php7.2-fpm.sock;
	}

	location = /favicon.ico  {
		log_not_found off;		
	}
}
``` 


## Usage

### ADS Operator Importer
Importer script copies data from Blockchain to MongoDB database. 
Make sure you have installed MongoDB and configure connection in `.env` file. 

To import data run the command below:

``` 
./bin/console ads:import 
```

### ADS Operator REST API

API documentation is available at `http://ads-operator.ads/api/doc`

Official documentation is still in progress.


## Quality
Application was created using BDD (business) and TDD (unit) approach. 

To run unit tests execute:
```
./vendor/bin/phpunit
```

To run BDD tests execute:
```
./vendor/bin/behat
```
**Sensio Security Checker**, **PHP Static Analysis Tool**, **PHP Codesniffer**, **PHP Parallel Lint** were used to test code quality.

To run all above tools in one command execute:
```
composer qa-check
```


## Versioning

We use [Semantic Versioning](https://semver.org/spec/v2.0.0.html) for versioning. For the versions available, see the 
[tags on this repository](https://github.com/adshares/ads-tools/tags).


## Authors

* **Przemysław Furtak** - _programmer_ - [c3zi](https://github.com/c3zi)
* **Maciej Pilarczyk** - _programmer_ - [m-pilarczyk](https://github.com/m-pilarczyk)

See also the list of [contributors](https://github.com/adshares/ads-operator/graphs/contributors) who participated in this 
project.


## License ![CC BY-ND](https://mirrors.creativecommons.org/presskit/buttons/80x15/svg/by-nd.svg "CC BY-ND 4.0")

This work is licensed under the Creative Commons Attribution-NoDerivatives 4.0 International License. To view a copy of 
this license, visit http://creativecommons.org/licenses/by-nd/4.0/ or send a letter to Creative Commons, PO Box 1866, 
Mountain View, CA 94042, USA.
 
See the [LICENSE](LICENSE) file for details.
