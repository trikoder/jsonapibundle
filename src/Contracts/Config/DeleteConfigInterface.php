<?php

namespace Trikoder\JsonApiBundle\Contracts\Config;

/**
 * Interface UpdateConfigInterface
 */
interface DeleteConfigInterface
{
    /**
     * List of roles required to access action, [] for nothing is allowed, null for everything is allowed
     *
     * @return array|null
     */
    public function getDeleteRequiredRoles();
}
