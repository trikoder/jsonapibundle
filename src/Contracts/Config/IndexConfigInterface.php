<?php

namespace Trikoder\JsonApiBundle\Contracts\Config;

/**
 * Interface IndexConfigInterface
 * @package Trikoder\JsonApiBundle\Contracts\Config
 */
interface IndexConfigInterface
{
    /**
     * List of fields supported in sort params, [] for nothing is allowed, null for everything is allowed
     *
     * @return array|null
     */
    public function getIndexAllowedSortFields();

    /**
     * List of fields supported in filtering, [] for nothing is allowed, null for everything is allowed
     *
     * @return array|null
     */
    public function getIndexAllowedFilteringParameters();

    /**
     * Default sorting, in format field=>direction, or just list of fields
     *
     * @return array
     */
    public function getIndexDefaultSort();

    /**
     * Default pagination params, in format offset/limit
     *
     * @return array
     */
    public function getIndexDefaultPagination();

    /**
     * List of supported fields params, [] for nothing is allowed, null for everything is allowed
     *
     * @return array|null
     */
    public function getIndexAllowedFields();
}
