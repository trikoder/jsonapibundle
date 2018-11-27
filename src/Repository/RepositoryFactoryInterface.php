<?php

namespace Trikoder\JsonApiBundle\Repository;

use Trikoder\JsonApiBundle\Contracts\RepositoryInterface;

/**
 * @deprecated in favour of DIC factory option, use https://symfony.com/doc/current/service_container/factories.html
 */
interface RepositoryFactoryInterface
{
    /**
     *
     */
    public function create(string $modelClass): RepositoryInterface;
}
