# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## Unreleased

## [0.14.0] 2018-10-23

### Added
- Added support for automatically mapped schema files, see [schema automapping docs](src/Resources/doc/getting_started/schema_automapping.md)
- Implemented support for required roles config directive, see [config reference](src/Resources/doc/configuration/configuration.md)

### Changed
- `\Trikoder\JsonApiBundle\Controller\AbstractController` is now auto-tagged with the `controller.service_arguments` tag.


## [0.11.0] 2018-08-03

### Changed
- Changed route annotation use to symfony/routing, previously was sensio/framework-extra-bundle
- Added symfony/routing as dependancy on 3.4
- Added "doctrine/common": "<2.9" as dependancy to cover deprication notices

### Removed
- Removed second argument (ServiceContainer) of schema as closure definition. see [Manual](src/Resources/doc/getting_started/schema_class_map.md)

### Deprecated
- Deprecated RepositoryFactoryInterface (`\Trikoder\JsonApiBundle\Repository\RepositoryFactoryInterface`) in favour of using DIC factory options

## [0.1.0] 2017-07-27

First tag of internal release.
