<?php

namespace Trikoder\JsonApiBundle\Services;

use Trikoder\JsonApiBundle\Contracts\SchemaClassMapProviderInterface;

/**
 * Class AbstractSchemaClassMapService
 * @package Trikoder\JsonApiBundle\Services
 */
abstract class AbstractSchemaClassMapService implements SchemaClassMapProviderInterface
{
    /**
     * @var array map for schemas
     */
    protected $classMap = [];

    /**
     * @inheritdoc
     */
    public function getMap()
    {
        return $this->classMap;
    }

    /**
     * @inheritdoc
     */
    public function add($class, $schema)
    {
        // TODO - do we need to prevent double adds?
        $this->classMap[$class] = $schema;
    }

    // TODO - add suport for add array list of schemas
}