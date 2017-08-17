# Advanced usage

There are several places to customize and build up jsonapi to your needs.

## Customize model input handler
Model input handler can be customized globaly by changing class of `trikoder.jsonapi.model_tools_factory` that must extend `\Trikoder\JsonApiBundle\Services\ModelInput\ModelToolsFactory`.

Any controller can also define `getCreateInputHandler` and `getUpdateInputHandler` for respective actions that should return `ModelInputHandlerInterface`. 

## Customize model validator
Model validator can be customized globaly by changing class of `trikoder.jsonapi.model_tools_factory` that must extend `\Trikoder\JsonApiBundle\Services\ModelInput\ModelToolsFactory`.

Any controller can also define `getCreateValidator` and `getUpdateValidator` for respective actions that should return `ModelValidatorInterface`.

## Manipulate schema class map in controller
Use case where you need to append schema class map in one controller or modify it with another schema.
Override method, call parent method, and update the result before returning, eg.:
```php
public function getSchemaClassMapProvider()
{
    // replace one property
    $mapService = parent::getSchemaClassMapProvider();
    $mapService->add(User::class, function (SchemaFactoryInterface $factory, ContainerInterface $serviceContainer) {
        return new CustomerSchema($factory, $serviceContainer->get('router'));
    });
    return $mapService;
}
```
