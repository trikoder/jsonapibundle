<?php

namespace Trikoder\JsonApiBundle\Services\Neomerx;

use Closure;
use Doctrine\Common\Util\ClassUtils;
use Neomerx\JsonApi\Contracts\Schema\SchemaFactoryInterface;
use \Neomerx\JsonApi\Schema\Container as BaseContainer;
use ReflectionClass;
use ReflectionParameter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use \RuntimeException;

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

    /**
     * @param string $className
     * @return mixed
     */
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
                throw new RuntimeException(sprintf("Argument %s for schema %s is not type hinted and cannot be autowired!",
                    $argumentIndex, $className));
            }
            $resolvedDependacy = $this->resolveSchemaClassDependancy($argumentClassHint);

            // if we cannot autowire it we should fail
            if (null === $resolvedDependacy) {
                throw new RuntimeException(sprintf("Cannot resolve argument %s for schema %s with hint %s. Did you forget to register service or alias?",
                    $argumentIndex, $className, $argumentClassHint->getName()));
            }
            $callArguments[$argumentIndex] = $resolvedDependacy;
        }

        // create schema
        $schema = new $className(...$callArguments);

        return $schema;
    }

    /**
     * @param ReflectionClass $dependancyClass
     * @return object
     */
    private function resolveSchemaClassDependancy(ReflectionClass $dependancyClass)
    {
        $dependancyClassName = $dependancyClass->getName();

        // resolve internfal services first
        if ($dependancyClassName === SchemaFactoryInterface::class) {
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
