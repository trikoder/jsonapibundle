<?php

namespace Trikoder\JsonApiBundle\Bridge\Doctrine;

use Trikoder\JsonApiBundle\Contracts\ObjectListCollectionInterface;

// TODO - move this from doctrine bridge, it has not doctrine in it, see #11

class ObjectListCollection implements ObjectListCollectionInterface
{
    /**
     * @var array
     */
    private $collection;
    /**
     * @var int|null
     */
    private $total;

    /**
     * ObjectListCollection constructor.
     *
     * @param array $collection
     * @param null $total
     */
    public function __construct(array $collection, $total = null)
    {
        if (false === is_array($collection) || null === $collection) {
            $collection = [];
        }
        $this->collection = $collection;

        // if null, we presume full list is returned
        if (null === $total) {
            $total = count($collection);
        }

        $this->total = $total;
    }

    /**
     * @return int total number of items
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @return array
     */
    public function getCollection()
    {
        return $this->collection;
    }
}
