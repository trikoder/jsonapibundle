<?php

namespace Trikoder\JsonApiBundle\Controller;

/**
 * Interface IndexActionEnabledInterface
 */
interface IndexActionEnabledInterface
{
    /**
     * Returns list of fixed filtering parameters
     * It is not affected by allowed filtering parameters
     *
     * @return array
     */
    public function getFixedFiltering();

    /**
     * Returns list of allowed include paths for request
     * Return [] for nothing is allowed, null for everything is allowed
     *
     * @return array|null
     */
    public function getAllowedIncludePaths();

    /**
     * Returns list of allowed sorting parameters for request
     * Return [] for nothing is allowed, null for everything is allowed
     *
     * @return array|null
     */
    public function getAllowedSortFields();

    /**
     * Returns the list of allowed filtering parameters for request
     * Return [] for nothing is allowed, null for everything is allowed
     *
     * @return array|null
     */
    public function getAllowedFilteringParameters();
}
