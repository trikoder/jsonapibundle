<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\JsonApi\Schema;

use Trikoder\JsonApiBundle\Schema\AbstractSchema;
use Trikoder\JsonApiBundle\Tests\Resources\Entity\Cart;

class CartSchema extends AbstractSchema
{
    protected $resourceType = 'cart';

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
        /* @var Cart $resource */
        return [];
    }

    /**
     * @param object $resource
     * @param bool $isPrimary
     *
     * @return array
     */
    public function getRelationships($resource, $isPrimary, array $includeRelationships)
    {
        /** @var Cart $resource */
        $relationships = [];

        $relationships['author'] = [
            self::DATA => function () use ($resource) {
                return $resource->getAuthor();
            },
        ];

        $relationships['products'] = [
            self::DATA => function () use ($resource) {
                return $resource->getProducts();
            },
        ];

        return $relationships;
    }
}
