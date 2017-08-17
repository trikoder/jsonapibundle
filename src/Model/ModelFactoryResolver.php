<?php

namespace Trikoder\JsonApiBundle\Model;

use \RuntimeException;

/**
 * Class ModelFactoryResolver
 * @package Trikoder\JsonApiBundle\Model
 */
class ModelFactoryResolver implements ModelFactoryResolverInterface
{
    /**
     * @var ModelFactoryInterface|null
     */
    private $defaultFactory;

    /**
     * @var ModelFactoryInterface[]
     */
    private $registry = [];

    /**
     * @param string $modelClass
     * @return ModelFactoryInterface
     * @throws RuntimeException
     */
    public function resolve(string $modelClass) : ModelFactoryInterface
    {
        if (array_key_exists($modelClass, $this->registry)) {
            return $this->registry[$modelClass];
        } elseif (null !== $this->defaultFactory) {
            return $this->defaultFactory;
        } else {
            throw new RuntimeException(sprintf("No factory defined for model %s (no default factory also). Did you forget to register any in ModelFactoryInterface?",
                $modelClass));
        }
    }

    /**
     * @param ModelFactoryInterface $factory
     * @param string|null $modelClass
     */
    public function registerFactory(ModelFactoryInterface $factory, string $modelClass = null)
    {
        if (null === $modelClass) {
            $this->defaultFactory = $factory;
        } elseif (true === array_key_exists($modelClass, $this->registry)) {
            throw new RuntimeException(sprintf("Factory for model %s is already defined", $modelClass));
        } else {
            $this->registry[$modelClass] = $factory;
        }
    }
}
