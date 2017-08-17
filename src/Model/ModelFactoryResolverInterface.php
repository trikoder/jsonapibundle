<?php

namespace Trikoder\JsonApiBundle\Model;

/**
 * Interface ModelFactoryResolverInterface
 * @package Trikoder\JsonApiBundle\Model
 */
interface ModelFactoryResolverInterface
{
    /**
     * @param string $modelClass
     * @return ModelFactoryInterface
     */
    public function resolve(string $modelClass) : ModelFactoryInterface;

    /**
     * @param ModelFactoryInterface $factory
     * @param string|null $modelClass
     */
    public function registerFactory(ModelFactoryInterface $factory, string $modelClass = null);
}
