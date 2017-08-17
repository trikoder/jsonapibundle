<?php

namespace Trikoder\JsonApiBundle\Model\Factory;

use Trikoder\JsonApiBundle\Model\ModelFactoryInterface;

/**
 * Class SimpleModelFactory
 * @package Trikoder\JsonApiBundle\Model\Factory
 */
class SimpleModelFactory implements ModelFactoryInterface
{
    /**
     * @param string $modelClass
     * @return object
     */
    public function create(string $modelClass)
    {
        return new $modelClass;
    }
}
