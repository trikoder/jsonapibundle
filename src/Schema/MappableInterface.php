<?php

namespace Trikoder\JsonApiBundle\Schema;

interface MappableInterface
{
    /**
     * returns an array of FQNs for all the classes that this particular schema can be used
     */
    public static function getMappedClassnames(): array;
}
