<?php

namespace Trikoder\JsonApiBundle\Bridge\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\MappingException;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Trikoder\JsonApiBundle\Contracts\RelationshipDoesNotExistException;
use Trikoder\JsonApiBundle\Contracts\RelationshipRepositoryInterface;
use Trikoder\JsonApiBundle\Contracts\RepositoryInterface;
use Trikoder\JsonApiBundle\Contracts\ResourceDoesNotExistException;

/**
 * Class DoctrineRepository
 */
class DoctrineRepository implements RepositoryInterface, RelationshipRepositoryInterface
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
     * @var PropertyAccessorInterface
     */
    protected $propertyAccessor;

    /**
     * DoctrineRepository constructor.
     */
    public function __construct(
        EntityRepository $entityRepository,
        EntityManager $entityManager,
        PropertyAccessorInterface $propertyAccessor
    ) {
        $this->entityRepository = $entityRepository;
        $this->entityManager = $entityManager;
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * {@inheritdoc}
     */
    public function getList($filter = [], $sort = [], $limit = null, $offset = null)
    {
        // TODO - rewrite this so only have one level of dependancy - right now we are inside 4
        // we need to be able to send filters and other params and array and get count and result list here
        $persister = $this->entityManager->getUnitOfWork()->getEntityPersister($this->entityRepository->getClassName());
        $collection = $persister->loadAll($filter, $sort, $limit, $offset);
        $count = $persister->count($filter);

        return new ObjectListCollection($collection, $count);
    }

    /**
     * {@inheritdoc}
     */
    public function getOne($id, $filter = [])
    {
        // TODO - this should check which fields is indentifier
        $filter['id'] = $id;

        return $this->entityRepository->findOneBy($filter);
    }

    /**
     * {@inheritdoc}
     */
    public function save($model)
    {
        $this->entityManager->persist($model);
        $this->entityManager->flush();

        return $model;
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

    /**
     * {@inheritdoc}
     */
    public function addToRelationship($model, string $relationshipName, array $relationshipData)
    {
        $modelMeta = $this->entityManager->getClassMetadata($this->entityRepository->getClassName());

        try {
            $relationshipModelClassName = $modelMeta->getAssociationMapping($relationshipName)['targetEntity'];
        } catch (MappingException $e) {
            throw new RelationshipDoesNotExistException($relationshipName);
        }

        $relationshipModelMeta = $this->entityManager->getClassMetadata($relationshipModelClassName);
        $relationshipIdentifier = $relationshipModelMeta->getSingleIdentifierFieldName();

        $repository = $this->entityManager->getRepository($relationshipModelClassName);
        $currentRelationshipModels = $this->propertyAccessor->getValue($model, $relationshipName);

        $relationshipModels = [];

        //add current relationship resources to array
        foreach ($currentRelationshipModels as $data) {
            $relationshipModels[$this->propertyAccessor->getValue($data, $relationshipIdentifier)] = $data;
        }

        $newRelationshipModels = $repository->findBy([$relationshipIdentifier => array_column($relationshipData, 'id')]);

        /*
         * @see https://gitlab.trikoder.net/trikoder/jsonapibundle/merge_requests/102#note_251076
         */
        if (\count($newRelationshipModels) !== \count($relationshipData)) {
            throw new ResourceDoesNotExistException();
        }

        //add new relationship resources to array
        foreach ($newRelationshipModels as $data) {
            $relationshipModels[$this->propertyAccessor->getValue($data, $relationshipIdentifier)] = $data;
        }

        $this->propertyAccessor->setValue($model, $relationshipName, $relationshipModels);

        return $this->save($model);
    }

    /**
     * {@inheritdoc}
     */
    public function removeFromRelationship($model, string $relationshipName, array $relationshipData)
    {
        $modelMeta = $this->entityManager->getClassMetadata($this->entityRepository->getClassName());

        try {
            $relationshipModelClassName = $modelMeta->getAssociationMapping($relationshipName)['targetEntity'];
        } catch (MappingException $e) {
            throw new RelationshipDoesNotExistException($relationshipName);
        }

        $relationshipModelMeta = $this->entityManager->getClassMetadata($relationshipModelClassName);
        $relationshipIdentifier = $relationshipModelMeta->getSingleIdentifierFieldName();

        $relationshipModels = $this->propertyAccessor->getValue($model, $relationshipName);
        $relationshipIds = array_column($relationshipData, 'id');

        $this->propertyAccessor->setValue(
            $model,
            $relationshipName,
            array_filter(
                iterator_to_array($relationshipModels),
                function ($data) use ($relationshipIdentifier, $relationshipIds) {
                    return !\in_array($this->propertyAccessor->getValue($data, $relationshipIdentifier), $relationshipIds);
                }
            )
        );

        return $this->save($model);
    }
}
