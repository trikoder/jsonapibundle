<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\AutomapTestSchema;

use Trikoder\JsonApiBundle\Schema\AbstractSchema;
use Trikoder\JsonApiBundle\Schema\MappableInterface;

class MappableTestSchema extends AbstractSchema implements MappableInterface
{
    protected $resourceType = 'some_test';

    public static function getMappedClassnames(): array
    {
        return [
            'SomeTestClass',
        ];
    }

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
            'someAttribute' => $resource->getSomeAttribute(),
        ];
    }
}
