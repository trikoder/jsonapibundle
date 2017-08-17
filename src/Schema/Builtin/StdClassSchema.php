<?php

namespace Trikoder\JsonApiBundle\Schema\Builtin;

use stdClass;
use Trikoder\JsonApiBundle\Schema\AbstractSchema;

/**
 * Class StdClassSchema
 * @package Trikoder\JsonApiBundle\Schema\Builtin
 */
class StdClassSchema extends AbstractSchema
{
    protected $resourceType = 'object';

    /**
     * Get resource identity.
     *
     * @param object $resource
     *
     * @return string
     */
    public function getId($resource)
    {
        if (isset($resource->id)) {
            return $resource->id;
        } else {
            return null;
        }
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
        /** @var stdClass $resource */
        $attributes = [];

        foreach ((array)$resource as $attributeKey => $attributeValue) {
            $attributes[$attributeKey] = $attributeValue;
        }

        return $attributes;
    }
}
