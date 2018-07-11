<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\JsonApi\Schema;

use Trikoder\JsonApiBundle\Schema\AbstractSchema;
use Trikoder\JsonApiBundle\Tests\Resources\Model\SimpleFileModel;

class SimpleFileSchema extends AbstractSchema
{
    protected $resourceType = 'simple-file';

    /**
     * Get resource identity.
     *
     * @param object $resource
     *
     * @return string
     */
    public function getId($resource)
    {
        return sprintf('%s.%s', $resource->getName(), $resource->getExtension());
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
        /* @var SimpleFileModel $resource */
        return [
            'name' => $resource->getName(),
            'title' => $resource->getTitle(),
            'extension' => $resource->getExtension(),
        ];
    }
}
