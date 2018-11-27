# Quick start guide

Quick start guide is the fastest way to get started with jsonapi. 
There are some prerequisites to comply to be as fast as possible.

## Assumptions !

- models are defined as doctrine entities 

## First time setup (Installation)

1. Install package `composer require trikoder/jsonapibundle`
2. Enabled the bundle in AppKernel `new Trikoder\JsonApiBundle\TrikoderJsonApiBundle()`
3. Add any custom configuration you wish, see [configuration](../configuration/configuration.md)
4. Define your schema service, eg: 
```
Trikoder\JsonApiBundle\Contracts\SchemaClassMapProviderInterface:
        class: "%trikoder.jsonapi.schema_class_map_provider.class%"
```

## Adding resources/apis

### 1. Create schema for model
Create new schema class that extends `\Trikoder\JsonApiBundle\Schema\AbstractSchema`.
Suggested naming is `<modelClass>Schema`.
For [simple schema see example](examples/ExampleSimpleSchema.php)
For more advanced examples see [schemas documentation](schemas.md)

### 2. Register new schema
There are few ways to give schema info to your controller. 
If you followed steps from first time setup you can register it by service call eg.:
`- [add, ['\stdClass', '\Trikoder\JsonApiBundle\Schema\Builtin\StdClassSchema']]` 
To find out more methods and usages on schema class map see (schema_class_map.md)

Alternatively (and especially if you like your life to be as easy as possible), use [schema automapping feature](schema_automapping.md)
### 3. Create controller
Create your API controller that extends `\Trikoder\JsonApiBundle\Controller\AbstractController`.
Your controller will be automatically registered as a service as the 
`\Trikoder\JsonApiBundle\Controller\AbstractController` is already tagged with the `controller.service_arguments` tag.
Minimal non-symfony configuration is to define model class this controller serves eg.:
```php
use Trikoder\JsonApiBundle\Config\Annotation as JsonApiConfig;
/**
 * @JsonApiConfig\Config(modelClass="\modelClass")
 */
```
[See example controller](examples/ExampleController.php).

You must use traits for action you need in your controller.

### 4. Done
You now have complete working json api.

Feel free to continue reading [advanced usage documentation](advanced_usage.md)
