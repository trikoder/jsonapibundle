<?php

namespace Trikoder\JsonApiBundle\Services\Neomerx;

use Closure;
use Doctrine\Common\Util\ClassUtils;
use Neomerx\JsonApi\Contracts\Schema\SchemaFactoryInterface;
use \Neomerx\JsonApi\Schema\Container as BaseContainer;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Container
 * @package Trikoder\JsonApiBundle\Services\Neomerx
 */
class Container extends BaseContainer
{
    /**
     * @var ContainerInterface
     */
    private $serviceContainer;

    /**
     * Container constructor.
     * @param ContainerInterface $serviceContainer
     * @param SchemaFactoryInterface $factory
     * @param array $schemas
     */
    public function __construct(
        ContainerInterface $serviceContainer,
        SchemaFactoryInterface $factory,
        array $schemas = []
    ) {
        parent::__construct($factory, $schemas);
        $this->serviceContainer = $serviceContainer;
    }

    /**
     * @inheritdoc
     */
    protected function getResourceType($resource)
    {
        return ClassUtils::getRealClass(get_class($resource));
    }

    /**
     * @param Closure $closure
     * @return mixed
     */
    protected function createSchemaFromClosure(Closure $closure)
    {
        $schema = $closure($this->getFactory(), $this->serviceContainer);
        return $schema;
    }
}