<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\JsonApi;

use Trikoder\JsonApiBundle\Services\AbstractSchemaClassMapService;
use Trikoder\JsonApiBundle\Tests\Schema\ExampleServiceSchema;

/**
 * Class SchemaClassMapService
 */
class ExampleServiceSchemaClassMap extends AbstractSchemaClassMapService
{
    /**
     * ExampleServiceSchemaClassMap constructor.
     */
    public function __construct()
    {
        $this->add(Example::class, ExampleServiceSchema::class);
    }
}
