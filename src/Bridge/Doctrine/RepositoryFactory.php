<?php

namespace Trikoder\JsonApiBundle\Bridge\Doctrine;

use Doctrine\ORM\EntityManager;
use Trikoder\JsonApiBundle\Contracts\RepositoryInterface;
use Trikoder\JsonApiBundle\Repository\RepositoryFactoryInterface;

class RepositoryFactory implements RepositoryFactoryInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * RepositoryFactory constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $modelClass
     * @return RepositoryInterface
     */
    public function create(string $modelClass) : RepositoryInterface
    {
        return new DoctrineRepository(
            $this->entityManager->getRepository($modelClass),
            $this->entityManager
        );
    }
}
