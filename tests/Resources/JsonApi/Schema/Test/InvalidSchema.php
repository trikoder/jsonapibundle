<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\JsonApi\Schema\Test;

use Neomerx\JsonApi\Contracts\Schema\SchemaFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\RememberMe\RememberMeServicesInterface;
use Trikoder\JsonApiBundle\Schema\AbstractSchema;
use Trikoder\JsonApiBundle\Tests\Resources\Entity\User;

class InvalidSchema extends AbstractSchema
{
    protected $resourceType = 'invalid';

    public function __construct(SchemaFactoryInterface $factory, string $myString)
    {
        parent::__construct($factory);
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
            "title" => $resource->getTitle(),
        ];
    }
}
