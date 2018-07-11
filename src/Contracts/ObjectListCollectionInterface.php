<?php

namespace Trikoder\JsonApiBundle\Contracts;

interface ObjectListCollectionInterface
{
    /**
     * @return int total number of items
     */
    public function getTotal();

    /**
     * @return array collection of items
     */
    public function getCollection();
}
