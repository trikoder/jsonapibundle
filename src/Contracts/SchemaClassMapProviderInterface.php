<?php

namespace Trikoder\JsonApiBundle\Contracts;

interface SchemaClassMapProviderInterface
{
    /**
     * @return array whole list of defined maps
     */
    public function getMap();

    /**
     * @param $class string class for which schema is applied
     * @param $schema string class or service of schema used
     * @return void
     */
    public function add($class, $schema);
}