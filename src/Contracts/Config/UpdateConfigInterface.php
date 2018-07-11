<?php

namespace Trikoder\JsonApiBundle\Contracts\Config;

/**
 * Interface UpdateConfigInterface
 */
interface UpdateConfigInterface
{
    /**
     * List of allowed fields in body, [] for nothing is allowed, null for everything is allowed
     *
     * @return array|null
     */
    public function getUpdateAllowedFields();

    /**
     * List of roles required to access action, [] for nothing is allowed, null for everything is allowed
     *
     * @return array|null
     */
    public function getUpdateRequiredRoles();
}
