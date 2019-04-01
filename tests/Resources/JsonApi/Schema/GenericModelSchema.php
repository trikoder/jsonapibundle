<?php

declare(strict_types=1);

namespace Trikoder\JsonApiBundle\Tests\Resources\JsonApi\Schema;

use Trikoder\JsonApiBundle\Schema\AbstractSchema;
use Trikoder\JsonApiBundle\Tests\Resources\Entity\GenericModel;

class GenericModelSchema extends AbstractSchema
{
    protected $resourceType = 'generic-model';

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
        /* @var GenericModel $resource */
        return [
            'title' => $resource->getTitle(),
            'isActive' => $resource->isActive(),
        ];
    }
}
