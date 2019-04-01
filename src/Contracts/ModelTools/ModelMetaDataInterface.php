<?php

declare(strict_types=1);

namespace Trikoder\JsonApiBundle\Contracts\ModelTools;

interface ModelMetaDataInterface
{
    public function getAllFields(): array;

    /**
     * @return null|string
     */
    public function getTypeForField(string $fieldName);
}
