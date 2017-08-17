<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\JsonApi;

use Neomerx\JsonApi\Contracts\Schema\SchemaFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Trikoder\JsonApiBundle\Services\AbstractSchemaClassMapService;
use Trikoder\JsonApiBundle\Tests\Schema\ExampleServiceSchema;

/**
 * Class SchemaClassMapService
 * @package Trikoder\JsonApiBundle\Tests\Resources\JsonApi
 */
class ExampleServiceSchemaClassMap extends AbstractSchemaClassMapService
{
    /**
     * ExampleServiceSchemaClassMap constructor.
     */
    public function __construct()
    {
        $this->add('\Example', function (SchemaFactoryInterface $factory, ContainerInterface $serviceContainer) {
            return new ExampleServiceSchema($factory, $serviceContainer->get('router'));
        });
    }
}
