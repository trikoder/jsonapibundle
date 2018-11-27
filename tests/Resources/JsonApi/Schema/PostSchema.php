<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\JsonApi\Schema;

use Trikoder\JsonApiBundle\Schema\AbstractSchema;
use Trikoder\JsonApiBundle\Tests\Resources\Entity\Post;

class PostSchema extends AbstractSchema
{
    protected $resourceType = 'post';

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
        /* @var Post $resource */
        return [
            'title' => $resource->getTitle(),
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
        /** @var Post $resource */
        $relationships = [];

        $relationships['author'] = [
            self::DATA => function () use ($resource) {
                return $resource->getAuthor();
            },
        ];

        return $relationships;
    }
}
