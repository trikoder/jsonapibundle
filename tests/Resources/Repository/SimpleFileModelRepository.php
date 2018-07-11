<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\Repository;

use Trikoder\JsonApiBundle\Contracts\ObjectListCollectionInterface;
use Trikoder\JsonApiBundle\Contracts\RepositoryInterface;
use Trikoder\JsonApiBundle\Tests\Resources\Model\SimpleFileModel;

/**
 * Class SimpleFileModelRepository
 */
class SimpleFileModelRepository implements RepositoryInterface
{
    /**
     * Returns list of objects
     *
     * @param array $filter array of filters to apply
     * @param array $sort array of sort rules
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return ObjectListCollectionInterface|array|null
     */
    public function getList($filter = [], $sort = [], $limit = null, $offset = null)
    {
        return [];
    }

    /**
     * Returns one object for Id with filtering applied
     *
     * @param $id
     * @param array $filter
     *
     * @return object|null
     */
    public function getOne($id, $filter = [])
    {
        return new SimpleFileModel();
    }

    /**
     * Saves model to arbitrary persistance layer
     *
     * @param object $model model to save
     */
    public function save($model)
    {
        // NOOP
    }

    /**
     * Removes model from arbitrary persistance layer
     *
     * @param object $model model to save
     */
    public function remove($model)
    {
        // NOOP
    }
}
