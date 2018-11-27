<?php

namespace Trikoder\JsonApiBundle\Model;

/**
 * Interface ModelFactoryResolverInterface
 */
interface ModelFactoryResolverInterface
{
    /**
     *
     */
    public function resolve(string $modelClass): ModelFactoryInterface;

    /**
     */
    public function registerFactory(ModelFactoryInterface $factory, string $modelClass = null);
}
