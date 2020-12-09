<?php

declare(strict_types=1);

namespace Trikoder\JsonApiBundle\Contracts\ModelTools;

interface ModelMetaDataInterface
{
    public function getAllFields(): array;

    /**
     * @return string|null
     */
    public function getTypeForField(string $fieldName);
}
