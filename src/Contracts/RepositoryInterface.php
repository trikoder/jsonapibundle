<?php

namespace Trikoder\JsonApiBundle\Contracts;

interface RepositoryInterface
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
    public function getList($filter = [], $sort = [], $limit = null, $offset = null);

    /**
     * Returns one object for Id with filtering applied
     *
     * @param $id
     * @param array $filter
     *
     * @return object|null
     */
    public function getOne($id, $filter = []);

    /**
     * Saves model to arbitrary persistance layer
     *
     * @param object $model model to save
     *
     * @return object|null null or newly saved object
     */
    public function save($model);

    /**
     * Removes model from arbitrary persistance layer
     *
     * @param object $model model to save
     * TODO - check return values
     */
    public function remove($model);
}
