<?php

namespace Trikoder\JsonApiBundle\Config;

use Trikoder\JsonApiBundle\Contracts\Config\IndexConfigInterface;

/**
 * Class IndexConfig
 */
final class IndexConfig implements IndexConfigInterface
{
    /**
     * @return array|null
     */
    private $allowedSortFields;

    /**
     * @return array|null
     */
    private $allowedFilteringParameters;

    /**
     * @return array
     */
    private $defaultSort = [];

    /**
     * @return array
     */
    private $defaultPagination = [];

    /**
     * @return array|null
     */
    private $allowedFields;

    /**
     * @return array|null
     */
    private $requiredRoles;

    /**
     * IndexConfig constructor.
     */
    public function __construct(
        array $allowedSortFields = null,
        array $allowedFilteringParameters = null,
        array $defaultSort = [],
        array $defaultPagination = [],
        array $allowedFields = null,
        array $requiredRoles = null
    ) {
        $this->allowedSortFields = $allowedSortFields;
        $this->allowedFilteringParameters = $allowedFilteringParameters;
        $this->defaultSort = $defaultSort;
        $this->defaultPagination = $defaultPagination;
        $this->allowedFields = $allowedFields;
        $this->requiredRoles = $requiredRoles;
    }

    /**
     * @return array|null
     */
    public function getIndexAllowedSortFields()
    {
        return $this->allowedSortFields;
    }

    /**
     * @return array|null
     */
    public function getIndexAllowedFilteringParameters()
    {
        return $this->allowedFilteringParameters;
    }

    /**
     * @return array
     */
    public function getIndexDefaultSort()
    {
        return $this->defaultSort;
    }

    /**
     * @return array
     */
    public function getIndexDefaultPagination()
    {
        return $this->defaultPagination;
    }

    /**
     * @return array|null
     */
    public function getIndexAllowedFields()
    {
        return $this->allowedFields;
    }

    /**
     * List of roles required to access action, [] for nothing is allowed, null for everything is allowed
     *
     * @return array|null
     */
    public function getIndexRequiredRoles()
    {
        return $this->requiredRoles;
    }
}
