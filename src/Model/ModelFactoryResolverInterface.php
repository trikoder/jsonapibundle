<?php

namespace Trikoder\JsonApiBundle\Model;

/**
 * Interface ModelFactoryResolverInterface
 */
interface ModelFactoryResolverInterface
{
    /**
     * @param string $modelClass
     *
     * @return ModelFactoryInterface
     */
    public function resolve(string $modelClass): ModelFactoryInterface;

    /**
     * @param ModelFactoryInterface $factory
     * @param string|null $modelClass
     */
    public function registerFactory(ModelFactoryInterface $factory, string $modelClass = null);
}
