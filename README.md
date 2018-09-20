<p align="center">
    <a href="https://adshares.net/" title="Adshares sp. z o.o." target="_blank">
        <img src="https://adshares.net/logos/ads.svg" alt="Adshares" width="100" height="100">
    </a>
</p>
<h3 align="center"><small>Adshares - ADS Operator</small></h3>
<p align="center">
    <a href="https://github.com/adshares/ads-operator/issues/new?template=bug_report.md&labels=Bug">Report bug</a>
    ·
    <a href="https://github.com/adshares/ads-operator/issues/new?template=feature_request.md&labels=New%20Feature">Request feature</a>
    ·
    <a href="https://github.com/adshares/ads-operator/wiki">Wiki</a>
</p>

[![Quality Status](https://sonarcloud.io/api/project_badges/measure?project=adshares-ads-manager&metric=alert_status)](https://sonarcloud.io/dashboard?id=adshares-ads-manager)
[![Build Status](https://travis-ci.org/adshares/ads-operator.svg?branch=master)](https://travis-ci.org/adshares/ads-manager#master "Master")
[![Build Status](https://travis-ci.org/adshares/ads-operator.svg?branch=develop)](https://travis-ci.org/adshares/ads-manager#develop "Develop")


## Documentation

- [Wiki](https://github.com/adshares/adserver/wiki)
- [Changelog](CHANGELOG.md)


# ADS Operator
ADS Operator consist of three parts:
1. ADS Importer script to fetch data from ADS blockchain
2. Blockexplorer web application
3. User management and transactional system 

## Getting Started

### Installing

It's described on the Wiki (https://github.com/adshares/ads-operator/wiki/Installation)
 


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
