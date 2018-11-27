<?php

namespace Trikoder\JsonApiBundle\Model;

/**
 * Interface ModelFactoryInterface
 */
interface ModelFactoryInterface
{
    /**
     * @return object
     */
    public function create(string $modelClass);
}
