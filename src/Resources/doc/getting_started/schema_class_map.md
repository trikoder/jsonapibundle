# Schema class map

Schema class map is object that providers map of models and their respective schemas for the api engine.
It must implement `\Trikoder\JsonApiBundle\Contracts\SchemaClassMapProviderInterface`.

The schema class map can be provided and used in several ways.

## Defining

Recommended way is to define as service and register all schemas in yml:
```yaml
Trikoder\JsonApiBundle\Contracts\SchemaClassMapProviderInterface:
      class: "%trikoder.jsonapi.schema_class_map_provider.class%"
      calls:
        - [add, ['\stdClass', '\Trikoder\JsonApiBundle\Schema\Builtin\StdClassSchema']]
```

### 1. Method in controller
Api engine calls controller method `getSchemaClassMapProvider` to get map provider. 
In your code, you can override this method to return your implementation of SchemaClassMapProviderInterface.
By default, the `Trikoder\JsonApiBundle\Contracts\SchemaClassMapProviderInterface` service is fetched.

This method can be used to append or change schema map for controller. Override the method, call parent method and update the result before returning.

### 2. Change class of Trikoder\JsonApiBundle\Contracts\SchemaClassMapProviderInterface service
In your configuration you can change value of `trikoder.jsonapi.schema_class_map_provider.class` parameter to be any class that you implement SchemaClassMapProviderInterface.

### 3. Redefine Trikoder\JsonApiBundle\Contracts\SchemaClassMapProviderInterface
In your services configuration you can redefine `Trikoder\JsonApiBundle\Contracts\SchemaClassMapProviderInterface` service with your required class.

## Usage
`SchemaClassMapProviderInterface` implements two methods. One of them is add method that enables to register schemas for models.

Add method receives two parameters:
- $class - FQN of class that provided schema should be used for
- $schema - FQN or class of closure that returns instance of the schema that should be used for the model. 
Using Closure is not recommended as any dependancies can be autowired and injected by the bundle. See [schemas](schemas.md) for more details. 
Closure must accept one argument: `SchemaFactoryInterface $factory`
