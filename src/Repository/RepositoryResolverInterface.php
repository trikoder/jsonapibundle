<?php

namespace Trikoder\JsonApiBundle\Repository;

use Trikoder\JsonApiBundle\Contracts\RepositoryInterface;

/**
 * Interface RepositoryResolverInterface
 */
interface RepositoryResolverInterface
{
    /**
     * @param string $modelClass
     *
     * @return RepositoryInterface
     */
    public function resolve(string $modelClass): RepositoryInterface;

    /**
     * @param RepositoryFactoryInterface $factory
     * @param string|null $modelClass
     */
    public function registerFactory(RepositoryFactoryInterface $factory, string $modelClass = null);

    /**
     * @param RepositoryInterface $repository
     * @param string $modelClass
     */
    public function registerRepository(RepositoryInterface $repository, string $modelClass);
}
