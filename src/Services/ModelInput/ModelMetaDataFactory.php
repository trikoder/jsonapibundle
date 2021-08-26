<?php

declare(strict_types=1);

namespace Trikoder\JsonApiBundle\Services\ModelInput;

use Doctrine\Common\Persistence\ObjectManager as LegacyObjectManager;
use Doctrine\Persistence\ObjectManager;
use Trikoder\JsonApiBundle\Contracts\ModelTools\ModelMetaDataInterface;

final class ModelMetaDataFactory
{
    /**
     * @var ObjectManager|LegacyObjectManager
     */
    private $objectManager;

    /**
     * @param ObjectManager|LegacyObjectManager $objectManager
     */
    public function __construct($objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function getMetaDataForModel($modelClass): ModelMetaDataInterface
    {
        if (!$this->objectManager->getMetadataFactory()->isTransient($modelClass)) {
            return new DoctrineModelMetaData($this->objectManager->getClassMetadata($modelClass));
        }

        return new GenericModelMetaData($modelClass);
    }
}
