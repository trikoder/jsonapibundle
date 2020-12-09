<?php

namespace Trikoder\JsonApiBundle\Schema\Builtin;

use DateTimeInterface;
use Neomerx\JsonApi\Contracts\Schema\SchemaFactoryInterface;
use RuntimeException;
use Serializable;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Trikoder\JsonApiBundle\Contracts\ModelTools\ModelMetaDataInterface;
use Trikoder\JsonApiBundle\Schema\AbstractSchema;
use Trikoder\JsonApiBundle\Services\ModelInput\ModelMetaDataFactory;

class GenericSchema extends AbstractSchema
{
    /**
     * @var ModelMetaDataInterface
     */
    private $modelMetaData;
    /**
     * @var ModelMetaDataFactory
     */
    private $modelMetaDataFactory;

    /**
     * @var string[]|null
     */
    private $attributesList;

    /**
     * @var string[]|null
     */
    private $relationshipList;
    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    public function __construct(string $className, SchemaFactoryInterface $factory, ModelMetaDataFactory $modelMetaDataFactory)
    {
        $this->modelMetaDataFactory = $modelMetaDataFactory;
        $this->setModelClassName($className);

        parent::__construct($factory);

        $this->propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->enableMagicCall()
            ->enableExceptionOnInvalidIndex()
            //->enableExceptionOnInvalidPropertyPath()
            ->getPropertyAccessor();
    }

    private function setModelClassName(string $className): void
    {
        if (\in_array(ResourceInterface::class, class_implements($className))) {
            $resourceType = \call_user_func([$className, 'getJsonApiResourceType']);
        } else {
            $resourceType = strtolower($className);
        }

        $this->setResourceType($resourceType);

        $this->modelMetaData = $this->modelMetaDataFactory->getMetaDataForModel($className);
    }

    private function setResourceType(string $resourceType): void
    {
        if (null !== $this->resourceType) {
            throw new RuntimeException('Change of resource type for generic schema in runtime is not allowed');
        }
        $this->resourceType = $resourceType;
    }

    private function prepareAttributeAndRelationshipsList(): void
    {
        $this->attributesList = [];
        $this->relationshipList = [];

        foreach ($this->modelMetaData->getAllFields() as $fieldName) {
            $fieldType = $this->modelMetaData->getTypeForField($fieldName);

            if (null != $fieldType) {
                $this->attributesList[] = $fieldName;
            } else {
                $this->relationshipList[] = $fieldName;
            }
        }
    }

    /**
     * Get resource identity.
     *
     * @param object $resource
     */
    public function getId($resource): string
    {
        return (string) $this->propertyAccessor->getValue($resource, 'id');
    }

    /**
     * Get resource attributes.
     *
     * @param object $resource
     *
     * @return array
     */
    public function getAttributes($resource)
    {
        if (null === $this->attributesList) {
            $this->prepareAttributeAndRelationshipsList();
        }

        /** @var object $resource */
        $attributes = [];

        foreach ($this->attributesList as $fieldName) {
            if ('id' === $fieldName) {
                continue;
            }
            $attributes[$fieldName] = $this->parseAttributeValue($this->propertyAccessor->getValue($resource, $fieldName));
        }

        return $attributes;
    }

    public function getRelationships($resource, $isPrimary, array $includeRelationships)
    {
        /** @var Post $resource */
        $relationships = [];

        foreach ($this->relationshipList as $relationName) {
            $relationships[$relationName] = [
                self::DATA => function () use ($resource, $relationName) {
                    return $this->propertyAccessor->getValue($resource, $relationName);
                },
            ];
        }

        return $relationships;
    }

    public function getIncludePaths()
    {
        return $this->relationshipList;
    }

    private function parseAttributeValue($value)
    {
        //handle special cases such as datetime...

        // datetime classes
        if ($value instanceof DateTimeInterface) {
            /* @var DateTimeInterface $value */
            return $value->format($value::ATOM);
        }

        // Serializable
        if ($value instanceof Serializable) {
            /* @var Serializable $value */
            return $value->serialize();
        }

        return $value;
    }
}
