<?php

namespace Trikoder\JsonApiBundle\Tests\Schema;

use Neomerx\JsonApi\Contracts\Schema\SchemaFactoryInterface;
use Symfony\Component\Routing\RouterInterface;
use Trikoder\JsonApiBundle\Schema\AbstractSchema;

/**
 * Class ExampleSimpleSchema
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
        $router = $this->router;

        return [
            'attribute' => $resource->getValue(),
            // any attribute or value can be closure that is evaluated on first ready. lazy af
            'url' => function () use ($router) {
                return $router->generate('route_to_something_great');
            },
        ];
    }
}
