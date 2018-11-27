<?php

namespace Trikoder\JsonApiBundle\Services\Neomerx;

use Doctrine\Common\Persistence\Proxy;
use Neomerx\JsonApi\Contracts\Schema\SchemaFactoryInterface;
use Neomerx\JsonApi\Schema\Container as BaseContainer;
use ReflectionClass;
use ReflectionParameter;
use RuntimeException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Trikoder\JsonApiBundle\Schema\Autowire\Exception\UnresolvedDependencyException;

/**
 * Class Container
 */
class Container extends BaseContainer
{
    /**
     * @var ContainerInterface
     */
    private $serviceContainer;

    /**
     * Container constructor.
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
     * {@inheritdoc}
     */
    protected function getResourceType($resource)
    {
        if ($resource instanceof Proxy) {
            return get_parent_class($resource);
        }

        return \get_class($resource);
    }

    protected function createSchemaFromClassName($className)
    {
        $callArguments = [];

        // get reflection class
        $reflector = new ReflectionClass($className);

        // see if there are any additional dependencies
        $constructorArguments = $reflector->getConstructor()->getParameters();
        /** @var ReflectionParameter $constructorArgument */
        foreach ($constructorArguments as $argumentIndex => $constructorArgument) {
            $argumentClassHint = $constructorArgument->getClass();

            // non type hinted arguments cannot be autowired
            if (null === $argumentClassHint) {
                throw new RuntimeException(sprintf('Argument %s for schema %s is not type hinted and cannot be autowired!',
                    $argumentIndex, $className));
            }
            $resolvedDependacy = $this->resolveSchemaClassDependancy($argumentClassHint);

            // if we cannot autowire it we should fail
            if (null === $resolvedDependacy) {
                throw new UnresolvedDependencyException($argumentIndex, $className, $argumentClassHint->getName());
            }
            $callArguments[$argumentIndex] = $resolvedDependacy;
        }

        // create schema
        $schema = new $className(...$callArguments);

        return $schema;
    }

    /**
     * @return object
     */
    private function resolveSchemaClassDependancy(ReflectionClass $dependancyClass)
    {
        $dependancyClassName = $dependancyClass->getName();

        // resolve internfal services first
        if (SchemaFactoryInterface::class === $dependancyClassName) {
            return $this->getFactory();
        }

        // see service container for exact implementation
        if ($this->serviceContainer->has($dependancyClassName)) {
            return $this->serviceContainer->get($dependancyClassName);
        }

        // try to match by interfaces
        $interfaces = $dependancyClass->getInterfaces();
        foreach ($interfaces as $interface) {
            $resolvedService = $this->resolveSchemaClassDependancy($interface);
            if (null !== $resolvedService) {
                return $resolvedService;
            }
        }

        // fallback to parent class
        if ($parentClass = $dependancyClass->getParentClass()) {
            return $this->resolveSchemaClassDependancy($parentClass);
        }
    }
}
