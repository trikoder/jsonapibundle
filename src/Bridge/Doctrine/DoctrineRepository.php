<?php

namespace Trikoder\JsonApiBundle\Bridge\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Trikoder\JsonApiBundle\Contracts\RepositoryInterface;

/**
 * Class DoctrineRepository
 * @package Trikoder\JsonApiBundle\Bridge\Doctrine
 */
class DoctrineRepository implements RepositoryInterface
{
    /**
     * @var EntityRepository
     */
    protected $entityRepository;
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * DoctrineRepository constructor.
     * @param EntityRepository $entityRepository
     * @param EntityManager $entityManager
     */
    public function __construct(EntityRepository $entityRepository, EntityManager $entityManager)
    {
        $this->entityRepository = $entityRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @inheritdoc
     */
    public function getList($filter = [], $sort = [], $limit = null, $offset = null)
    {
        // TODO - rewrite this so only have one level of dependancy - right now we are inside 4
        // TODO we need to be able to send filters and other params and array and get count and result list here
        $persister = $this->entityManager->getUnitOfWork()->getEntityPersister($this->entityRepository->getClassName());
        $collection = $persister->loadAll($filter, $sort, $limit, $offset);
        $count = $persister->count($filter);

        return new ObjectListCollection($collection, $count);
    }

    /**
     * @inheritdoc
     */
    public function getOne($id, $filter = [])
    {
        // TODO - this should check which fields is indentifier
        $filter['id'] = $id;
        return $this->entityRepository->findOneBy($filter);
    }

    /**
     * @inheritdoc
     */
    public function save($model)
    {
        $this->entityManager->persist($model);
        $this->entityManager->flush();
    }

    /**
     * Removes model from arbitrary persistance layer
     *
     * @param object $model model to save
     */
    public function remove($model)
    {
        $this->entityManager->remove($model);
        $this->entityManager->flush();
    }
}