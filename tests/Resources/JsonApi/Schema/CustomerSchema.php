<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\JsonApi\Schema;

use Neomerx\JsonApi\Contracts\Schema\SchemaFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\RouterInterface;
use Trikoder\JsonApiBundle\Schema\AbstractSchema;
use Trikoder\JsonApiBundle\Tests\Resources\Entity\User;

class CustomerSchema extends AbstractSchema
{
    protected $resourceType = 'customer';
    /**
     * @var Router
     */
    private $router;

    public function __construct(SchemaFactoryInterface $factory, RouterInterface $router)
    {
        parent::__construct($factory);
        $this->router = $router;
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
        /* @var User $resource */
        return [
            'email' => function () use ($resource) {
                return $resource->getEmail();
            },
            'profile' => $this->router->generate('customer_dummy_action', ['id' => $resource->getId()]),
        ];
    }
}
