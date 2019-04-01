<?php

declare(strict_types=1);

namespace Trikoder\JsonApiBundle\Services\ModelInput;

use Doctrine\Common\Persistence\ObjectManager;
use Trikoder\JsonApiBundle\Contracts\ModelTools\ModelMetaDataInterface;

final class ModelMetaDataFactory
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    public function __construct(ObjectManager $objectManager)
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
