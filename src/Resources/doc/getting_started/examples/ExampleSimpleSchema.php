<?php

namespace Trikoder\JsonApiBundle\Tests\Schema;

use Trikoder\JsonApiBundle\Schema\AbstractSchema;

/**
 * Class ExampleSimpleSchema
 * @package Trikoder\JsonApiBundle\Tests\Schema
 */
class ExampleSimpleSchema extends AbstractSchema
{
    protected $resourceType = 'example';

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
            "attribute" => $resource->getValue()
        ];
    }
}
