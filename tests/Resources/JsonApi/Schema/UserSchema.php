<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\JsonApi\Schema;

use Trikoder\JsonApiBundle\Schema\AbstractSchema;
use Trikoder\JsonApiBundle\Tests\Resources\Entity\User;

class UserSchema extends AbstractSchema
{
    protected $resourceType = 'user';

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
        /** @var User $resource */
        return [
            "email" => $resource->getEmail(),
            "active" => $resource->isActive(),
        ];
    }
}
