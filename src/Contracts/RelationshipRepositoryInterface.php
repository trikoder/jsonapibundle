<?php

namespace Trikoder\JsonApiBundle\Contracts;

interface RelationshipRepositoryInterface
{
    /**
     * Adds new resource to relationship
     *
     * @throws ResourceDoesNotExistException
     * @throws RelationshipDoesNotExistException
     */
    public function addToRelationship($model, string $relationshipName, array $relationshipData);

    /**
     * Remove resource from relationship
     *
     * @throws RelationshipDoesNotExistException
     */
    public function removeFromRelationship($model, string $relationshipName, array $relationshipData);
}
