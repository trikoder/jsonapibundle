<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\JsonApi\Schema;

use Trikoder\JsonApiBundle\Schema\AbstractSchema;
use Trikoder\JsonApiBundle\Tests\Resources\Entity\Product;

class ProductSchema extends AbstractSchema
{
    protected $resourceType = 'product';

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
        /* @var Product $resource */
        return [
            'title' => $resource->getTitle(),
            'price' => $resource->getPrice(),
        ];
    }

    /**
     * @param object $resource
     * @param bool $isPrimary
     *
     * @return array
     */
    public function getRelationships($resource, $isPrimary, array $includeRelationships)
    {
        /** @var Product $resource */
        $relationships = [];

        return $relationships;
    }
}
