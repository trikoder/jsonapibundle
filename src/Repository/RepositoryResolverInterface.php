<?php

namespace Trikoder\JsonApiBundle\Repository;

use Trikoder\JsonApiBundle\Contracts\RepositoryInterface;

/**
 * Interface RepositoryResolverInterface
 */
interface RepositoryResolverInterface
{
    /**
     *
     */
    public function resolve(string $modelClass): RepositoryInterface;

    /**
     * @deprecated @see \Trikoder\JsonApiBundle\Repository\RepositoryFactoryInterface
     */
    public function registerFactory(RepositoryFactoryInterface $factory, string $modelClass = null);

    /**
     */
    public function registerRepository(RepositoryInterface $repository, string $modelClass);
}
