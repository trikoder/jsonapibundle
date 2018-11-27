<?php

namespace Trikoder\JsonApiBundle\Bridge\Doctrine;

use Doctrine\ORM\EntityManager;
use Trikoder\JsonApiBundle\Contracts\RepositoryInterface;
use Trikoder\JsonApiBundle\Repository\RepositoryFactoryInterface;

/**
 * @deprecated @see \Trikoder\JsonApiBundle\Repository\RepositoryFactoryInterface
 */
class RepositoryFactory implements RepositoryFactoryInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * RepositoryFactory constructor.
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     *
     */
    public function create(string $modelClass): RepositoryInterface
    {
        return new DoctrineRepository(
            $this->entityManager->getRepository($modelClass),
            $this->entityManager
        );
    }
}
