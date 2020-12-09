<?php

namespace Trikoder\JsonApiBundle\Bridge\Doctrine;

use Doctrine\ORM\EntityManager;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
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
    private $propertyAccessor;

    /**
     * RepositoryFactory constructor.
     */
    public function __construct(EntityManager $entityManager, PropertyAccessorInterface $propertyAccessor)
    {
        $this->entityManager = $entityManager;
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     *
     */
    public function create(string $modelClass): RepositoryInterface
    {
        return new DoctrineRepository(
            $this->entityManager->getRepository($modelClass),
            $this->entityManager,
            $this->propertyAccessor
        );
    }
}
