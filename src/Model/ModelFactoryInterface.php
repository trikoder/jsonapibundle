<?php

namespace Trikoder\JsonApiBundle\Model;

/**
 * Interface ModelFactoryInterface
 * @package Trikoder\JsonApiBundle\Model
 */
interface ModelFactoryInterface
{
    /**
     * @param string $modelClass
     * @return object
     */
    public function create(string $modelClass);
}
