<?php

namespace Trikoder\JsonApiBundle\Repository;

use Trikoder\JsonApiBundle\Contracts\RepositoryInterface;

/**
 * Interface RepositoryFactoryInterface
 */
interface RepositoryFactoryInterface
{
    /**
     * @param string $modelClass
     *
     * @return RepositoryInterface
     */
    public function create(string $modelClass): RepositoryInterface;
}
