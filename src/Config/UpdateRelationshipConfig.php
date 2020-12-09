<?php

namespace Trikoder\JsonApiBundle\Config;

use Trikoder\JsonApiBundle\Contracts\Config\UpdateRelationshipConfigInterface;

/**
 * Class UpdateConfig
 */
final class UpdateRelationshipConfig implements UpdateRelationshipConfigInterface
{
    /**
     * @return array|null
     */
    private $allowedRelationships;

    /**
     * @return array|null
     */
    private $requiredRoles;

    /**
     * UpdateConfig constructor.
     */
    public function __construct(
        array $allowedRelationships = null,
        array $requiredRoles = null
    ) {
        $this->allowedRelationships = $allowedRelationships;
        $this->requiredRoles = $requiredRoles;
    }

    /**
     * @return array|null
     */
    public function getAllowedRelationships()
    {
        return $this->allowedRelationships;
    }

    /**
     * @return array|null
     */
    public function getRequiredRoles()
    {
        return $this->requiredRoles;
    }
}
