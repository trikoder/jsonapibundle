<?php

declare(strict_types=1);

namespace Trikoder\JsonApiBundle\Services\ModelInput;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Trikoder\JsonApiBundle\Contracts\ModelTools\ModelMetaDataInterface;

final class DoctrineModelMetaData implements ModelMetaDataInterface
{
    /**
     * @var ClassMetadata
     */
    private $classMetaData;

    public function __construct(ClassMetadata $classMetaData)
    {
        $this->classMetaData = $classMetaData;
    }

    public function getAllFields(): array
    {
        // get all fields, relations, and identifiers
        $fields = array_unique(array_merge(
            $this->classMetaData->getFieldNames(),
            $this->classMetaData->getAssociationNames(),
            $this->classMetaData->getIdentifierFieldNames()
        ));

        return $fields;
    }

    /**
     * @return null|string
     */
    public function getTypeForField(string $fieldName)
    {
        return $this->classMetaData->getTypeOfField($fieldName);
    }
}
