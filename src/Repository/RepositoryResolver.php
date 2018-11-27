<?php

namespace Trikoder\JsonApiBundle\Repository;

use RuntimeException;
use Trikoder\JsonApiBundle\Contracts\RepositoryInterface;

/**
 * Class RepositoryResolver
 */
class RepositoryResolver implements RepositoryResolverInterface
{
    /**
     * @var RepositoryFactoryInterface|null
     */
    private $defaultFactory;

    /**
     * @var RepositoryFactoryInterface[]
     */
    private $factoryRegistry = [];

    /**
     * @var RepositoryInterface[]
     */
    private $repositoryRegistry = [];

    /**
     *
     */
    public function resolve(string $modelClass): RepositoryInterface
    {
        // first check for repository
        $resolvedRepository = null;

        if (true === array_key_exists($modelClass, $this->repositoryRegistry)) {
            $resolvedRepository = $this->repositoryRegistry[$modelClass];
        }

        if (null === $resolvedRepository && true === array_key_exists($modelClass, $this->factoryRegistry)) {
            $resolvedRepository = $this->factoryRegistry[$modelClass]->create($modelClass);
            $this->registerRepository($resolvedRepository, $modelClass);
        }

        if (null === $resolvedRepository && false === (null === $this->defaultFactory)) {
            $resolvedRepository = $this->defaultFactory->create($modelClass);
            $this->registerRepository($resolvedRepository, $modelClass);
        }

        if (null === $resolvedRepository) {
            throw new RuntimeException(sprintf('No repository found for model %s (no defaults also). Did you forget to register any in RepositoryResolverInterface?',
                $modelClass));
        }

        return $resolvedRepository;
    }

    /**
     */
    public function registerRepository(RepositoryInterface $repository, string $modelClass)
    {
        if (true === array_key_exists($modelClass, $this->repositoryRegistry)) {
            throw new RuntimeException(sprintf('Repository for model %s is already defined', $modelClass));
        } else {
            $this->repositoryRegistry[$modelClass] = $repository;
        }
    }

    /**
     * @deprecated @see \Trikoder\JsonApiBundle\Repository\RepositoryFactoryInterface
     */
    public function registerFactory(RepositoryFactoryInterface $factory, string $modelClass = null)
    {
        if (true === (null === $modelClass)) {
            $this->defaultFactory = $factory;
        } elseif (true === array_key_exists($modelClass, $this->factoryRegistry)) {
            throw new RuntimeException(sprintf('Repository factory for model %s is already defined', $modelClass));
        } else {
            $this->factoryRegistry[$modelClass] = $factory;
        }
    }
}
