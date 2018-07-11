<?php

namespace Trikoder\JsonApiBundle\Model\Factory;

use Trikoder\JsonApiBundle\Model\ModelFactoryInterface;

/**
 * Class SimpleModelFactory
 */
class SimpleModelFactory implements ModelFactoryInterface
{
    /**
     * @param string $modelClass
     *
     * @return object
     */
    public function create(string $modelClass)
    {
        return new $modelClass();
    }
}
