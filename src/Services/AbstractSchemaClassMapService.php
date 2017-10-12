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
        $this->classMap[$this->normalizeClassFQN($class)] = $this->normalizeClassFQN($schema);
    }

    /**
     * @param $class
     * @return string
     */
    protected function normalizeClassFQN($class)
    {
        if (true === is_string($class)) {
            $class = trim($class, "\\");
        }
        return $class;
    }

    /**
     * @param array $schemas
     */
    public function addSchemas(array $schemas)
    {
        foreach ($schemas as $class => $schema) {
            $this->add($class, $schema);
        }
    }
}
