# Schema automapping

## General mechanism
During a custom compiler pass, all directories matching the patterns defined in config under schema_automap_scan_patterns key (default is src/*Schema/) are scanned for php files.

All those files are then additionally checked to see if they actually are schema classes and if they are, if they implement the Trikoder\JsonApiBundle\Schema\MappableInterface interface.

All files for which both is true are then automapped by automatically modifying the classmap service definition by adding a call to it's "add" method, where the actual class that the schema is mapped to is determined by the return value of the static method getMappedClassnames of the schema class (required by the Trikoder\JsonApiBundle\Schema\MappableInterface interface)

## How to automap my schemas?
Extremely simple, just do following:

1. make sure that your schemas implement Trikoder\JsonApiBundle\Schema\MappableInterface
2. make sure that your schema is located in one of the directories which match the pattern defined in your config.yml under schema_automap_scan_patterns key (default is src/*Schema/)
3. clear your symfony cache (cache:clear)

bam. you're done. your schemas are now all mapped into a classmap and are ready to use. 

And you didn't have to soil your fingers by editing any yml files

## Automappable schema example
````php
<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\AutomapTestSchema;

use Trikoder\JsonApiBundle\Schema\AbstractSchema;
use Trikoder\JsonApiBundle\Schema\MappableInterface;

class MappableExampleSchema extends AbstractSchema implements MappableInterface
{
    protected $resourceType = 'mappable_example';

    public static function getMappedClassnames(): array
    {
        return [
            'SomeModelClass',
        ];
    }

    /**
     * Get resource identity.
     *
     * @param object $resource
     *
     * @return string
     */
    public function getId($resource)
    {
        return $resource->getId();
    }

    /**
     * Get resource attributes.
     *
     * @param object $resource
     *
     * @return array
     */
    public function getAttributes($resource)
    {
        return [
            'someAttribute' => $resource->getSomeAttribute(),
        ];
    }
}

````

## Known side-effects
None.