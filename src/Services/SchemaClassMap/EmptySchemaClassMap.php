<?php

namespace Trikoder\JsonApiBundle\Services\SchemaClassMap;

use stdClass;
use Trikoder\JsonApiBundle\Schema\Builtin\StdClassSchema;
use Trikoder\JsonApiBundle\Services\AbstractSchemaClassMapService;

/**
 * Class EmptySchemaClassMap
 */
class EmptySchemaClassMap extends AbstractSchemaClassMapService
{
    public function __construct()
    {
        $this->add(stdClass::class, StdClassSchema::class);
    }
}
