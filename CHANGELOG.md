# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## Unreleased

### Changed
- Form validation errors now generate pointers from cause instead of origin

### Fixed
- Form validation errors pointers now correctly target deep properties 
- Specified includes now get properly included on create requests

## [0.17.5] 2019-11-15

### Added
- Added CI checks for symfony 4.3

### Fixed
- Show routes with additional parameters now generate properly
- CI checks for Symfony 3.4 now uses same version router component

### Changed
- Show route calculations no longer use route collection but instead use convention naming
- Self url calculation no longer uses route collection but instead uses current route parameters

## [0.17.4] 2019-10-21

### Added
- Support for relationship editing trait, see `src/Resources/doc/flow/write_actions.md#updaterelationship`

### Depretacted
- dropped support for symfony versions 3.1, 3.2, 3.3

## [0.17.3] 2019-09-16

### Changed
- default content type in responses changed to `application/vnd.api+json`

## [0.17.2] 2019-08-28

### Fixed
- Create trait uses property accessor to get ID instead of expecting getId method

## [0.17.1] 2019-08-22

### Fixed
- JsonApi controller check to allow for callable
- Form errors now provide correct code from violation

## [0.17.0] 2019-08-09

### Added
- The `\Trikoder\JsonApiBundle\Listener\KernelListener` listener priorities for the
`kernel.view` and `kernel.exception` events can now be configured via the extension's
`kernel_listener_on_kernel_view_priority` and `kernel_listener_on_kernel_exception_priority`
keys respectively.
- `Trikoder\JsonApiBundle\Schema\Builtin\GenericSchema` to be used for exposing 1:1 any resource
- `Trikoder\JsonApiBundle\Services\Client\ResponseBodyDecoder` to be used as client for jsonapi response

## [0.16.0] 2019-02-14

### Added
- `GenericFormModelInputHandler` can now auto-magicaly handle PHP objects using return typehints, see `GenericModelMetaData`

### Changed
- `Trikoder\JsonApiBundle\Contracts\RepositoryInterface::save` can now return a object that is saved
- InputHandlers will now throw `UnhandleableModelInputException` exceptions when input cannot be properly handled onto model


## [0.14.0] 2018-10-23

### Added
- Added support for automatically mapped schema files, see [schema automapping docs](src/Resources/doc/getting_started/schema_automapping.md)
- Implemented support for required roles config directive, see [config reference](src/Resources/doc/configuration/configuration.md)

### Changed
- `\Trikoder\JsonApiBundle\Controller\AbstractController` is now auto-tagged with the `controller.service_arguments` tag.


## [0.11.0] 2018-08-03

### Changed
- Changed route annotation use to symfony/routing, previously was sensio/framework-extra-bundle
- Added symfony/routing as dependency on 3.4
- Added "doctrine/common": "<2.9" as dependency to cover deprecation notices

### Removed
- Removed second argument (ServiceContainer) of schema as closure definition. see [Manual](src/Resources/doc/getting_started/schema_class_map.md)

### Deprecated
- Deprecated RepositoryFactoryInterface (`\Trikoder\JsonApiBundle\Repository\RepositoryFactoryInterface`) in favour of using DIC factory options

## [0.1.0] 2017-07-27

First tag of internal release.
