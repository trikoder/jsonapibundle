<?php

namespace Trikoder\JsonApiBundle\Tests\Schema;

use Neomerx\JsonApi\Contracts\Schema\SchemaFactoryInterface;
use Symfony\Component\Routing\RouterInterface;
use Trikoder\JsonApiBundle\Schema\AbstractSchema;

/**
 * Class ExampleSimpleSchema
 * @package Trikoder\JsonApiBundle\Tests\Schema
 */
class ExampleServiceSchema extends AbstractSchema
{
    protected $resourceType = 'example';

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * ExampleServiceSchema constructor.
     * @param SchemaFactoryInterface $factory
     * @param RouterInterface $router
     */
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
        return [
            "attribute" => $resource->getValue(),
            'url' => $this->router->generate('route_to_something_great'),
        ];
    }
}
