<?php

namespace Trikoder\JsonApiBundle\Tests\Schema;

use Neomerx\JsonApi\Contracts\Schema\SchemaFactoryInterface;
use Trikoder\JsonApiBundle\Schema\AbstractSchema;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

/**
 * Class ExampleSimpleSchema
 * @package Trikoder\JsonApiBundle\Tests\Schema
 */
class ExampleServiceSchema extends AbstractSchema
{
    protected $resourceType = 'example';

    /**
     * @var Router
     */
    private $router;

    /**
     * ExampleSimpleSchema constructor.
     * @param SchemaFactoryInterface $factory
     * @param Router $router
     */
    public function __construct(SchemaFactoryInterface $factory, Router $router)
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
        return [
            "attribute" => $resource->getValue(),
            'url' => $this->router->generate('route_to_something_great'),
        ];
    }
}
