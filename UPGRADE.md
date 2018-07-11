# Upgrade Guide

This file provides notes on how to upgrade between versions.


# Upgrade from v0.9.*

## Bundle changes
- bundle moves to support symfony 4 with default private services and autowiring
- changes in demo code to reflect `controllers as services` change

## Schema autowiring
!NOTE - change described below will be updated in future version with option to have bundle perform this action automatically by scaning your schemas..
To use any non public services in your schemas, you must redefine `Trikoder\JsonApiBundle\Services\Neomerx\ServiceContainer` and call `set` for each of used service, ie:
```yaml
Trikoder\JsonApiBundle\Services\Neomerx\ServiceContainer:
      calls:
        - method: set
          arguments:
            - 'Symfony\Component\Routing\RouterInterface'
            - '@router'
```

## Service definitions
All services should use Interface hinting names as described by Symfony documentation on autowiring. https://symfony.com/doc/current/service_container/autowiring.html
Bundle defines aliases for old naming to keep compatibility with existing implementations. 
This will be removed in later version.

## Abstract controller
1. No longer inherits `Symfony\Bundle\FrameworkBundle\Controller\Controller` but instead moves to be `Controller as Service`.
2. It defines required setter injection for several services listed below. NOTE - this is subject to change in future versions
`setSchemaClassMapProvider` in abstract controller, and usage of trait `\Trikoder\JsonApiBundle\Controller\Traits\Polyfill\SymfonyAutowiredServicesTrait`
