# Idea

Idea of this bundle is to give ability quickly create fully functional jsonapi crud api with high level of customizability.

This is achieved by giving out-of-the box ready controllers and traits. 
The simpler behaviour changes can be made via configuration and method overriding.

For more complex changes, the bundle can be extended and customized by overriding or replacing service classes.

# Arhitecture idea

## Minimal effort
Bundle should require minimal effort when creating "default" behaviour apis. 
Consumers should  be able to only write their domain code like models, schemas and controllers.

## Configurability and Extendability
Default behaviour can be changed with config directives (either inside controler, services definition or global configuration).

All service definition from from bundle can have it's class changed with parameter class.
 
All services should have their logic methods defined as protected to enable quick change of entry and middle level points.

## Services

All services should be defined by interfaces and only interfaces should be referenced withing the code (never implementation classes). 
Interfaces should only define methods that are in functional role. Any implementation hints should be invisible to the interface (eg. dependant services, etc ...).

## Code organization

The code should follow [Best Practices for Reusable Bundles](http://symfony.com/doc/current/bundles/best_practices.html)

## OOP

For all of the functional parts, there should be minimal interface defined that is needed to keep the chanin working.
Any implementation hints should be excluded from interface (dependant services injections, etc ...)

To remove code duplication and give support for popular components, some abstract classes are defined with ready-to-go functionalities (eg. DoctrineRepository).

# Arhitecture in depth

## Model

## Schema

## Repository

## Controller

## Action

## Model input handler
It's responsibility is to merge input array of data to provided model.
Is class that implements `\Trikoder\JsonApiBundle\Contracts\ModelTools\ModelInputHandlerInterface`.

There are few built in model input handlers:
- `\Trikoder\JsonApiBundle\Services\ModelInput\GenericFormModelInputHandler` - handler that uses symfony Form built from model's metadata
- `\Trikoder\JsonApiBundle\Services\ModelInput\CustomFormModelInputHandler` - handler that uses custom provided symfony Form to update the model
- `\Trikoder\JsonApiBundle\Services\ModelInput\ValidatingCustomFormModelInputHandler` - handler that uses custom provided symfony Form to update the model but it also uses form for validation (and any constraint rules on the form will be checked)

## Model validator

## Model factory

## Response factory

## Encoder
