# Schemas

Schemas are classes that translate model to api data.
They are mapped to models using schema maps, see [schema class map documentation](schema_class_map.md)

## Schema class
For simple schema see [simple schema example](examples/ExampleSimpleSchema.php).

## Schemas with service dependacies
Schema can can have outside dependancies from service container. 
For example see [schema with services example](examples/ExampleServiceSchema.php)
Such schemas must be added using custom implementation (this is subject to change in the future).
See example of [schema class map](examples/ExampleServiceSchemaClassMap.php)
