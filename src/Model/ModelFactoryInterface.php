<?php

namespace Trikoder\JsonApiBundle\Model;

/**
 * Interface ModelFactoryInterface
 */
interface ModelFactoryInterface
{
    /**
     * @param string $modelClass
     *
     * @return object
     */
    public function create(string $modelClass);
}
