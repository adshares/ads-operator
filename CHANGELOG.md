# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Added
- Import node version
 
## [0.2.1] - 2018-10-25
### Added
- Change account key use case

### Changed
- Add `version` field for connection transactions

### Fixed
- Display connection transactions on a message view
 
## [0.2.0] - 2018-09-28
### Added
- Import data starting from the last block
- Use MongoDB `upsert` functionality to insert or update data
- User email change
- User password change

### Changed
- Update ADS PHP Client library
- Removed transformation from hexadecimal `nodeId` to decimal regarding to the new version of ADS PHP Client 

## [0.1.0] - 2018-09-24
### Added
- Support for all block explorer endpoints:
  - List of nodes,
  - List of blocks,
  - List of accounts,
  - List of messages,
  - List of transactions,
  - Get a single node,
  - Get a single block,
  - Get a single account,
  - Get a single message,
  - Get a single transaction,
  - List of accounts for a single node,
  - List of messages for a single node,
  - List of transactions for a single node,
  - List of transactions for a single account,  
  - List of messages for a single block,
  - List of transactions for a single block,
  - List of transactions for a single message,

- Support for importing data from a network

- Support for user area:
  - Register a new user
  - Login to the system using JWT tokens 
          
          
- Readme
- License
- Changelog
- Contributing


[Unreleased]: https://github.com/adshares/ads-operator/compare/v0.2.1...HEAD

[0.2.1]: https://github.com/adshares/ads-operator/compare/v0.2.0...v0.2.1
[0.2.0]: https://github.com/adshares/ads-operator/compare/v0.1.0...v0.2.0
[0.1.0]: https://github.com/adshares/ads-operator/releases/tag/v0.1.0
