<?php

namespace Trikoder\JsonApiBundle\Contracts\Config;

/**
 * Interface UpdateRelationshipConfigInterface
 */
interface UpdateRelationshipConfigInterface
{
    /**
     * List of what relationships can be changed, [] for nothing is allowed, null for everything is allowed
     *
     * @return array|null
     */
    public function getAllowedRelationships();

    /**
     * List of roles required to access action, [] for nothing is allowed, null for everything is allowed
     *
     * @return array|null
     */
    public function getRequiredRoles();
}
