<?php

namespace Trikoder\JsonApiBundle\Config;

use Trikoder\JsonApiBundle\Contracts\Config\UpdateConfigInterface;

/**
 * Class UpdateConfig
 * @package Trikoder\JsonApiBundle\Config
 */
final class UpdateConfig implements UpdateConfigInterface
{
    /**
     * @return array|null
     */
    private $allowedFields;

    /**
     * @return array|null
     */
    private $requiredRoles;

    /**
     * UpdateConfig constructor.
     * @param array|null $allowedFields
     * @param array|null $requiredRoles
     */
    public function __construct(
        array $allowedFields = null,
        array $requiredRoles = null
    )
    {
        $this->allowedFields = $allowedFields;
        $this->requiredRoles = $requiredRoles;
    }

    /**
     * @return array|null
     */
    public function getUpdateAllowedFields()
    {
        return $this->allowedFields;
    }

    /**
     * @return array|null
     */
    public function getUpdateRequiredRoles()
    {
        return $this->requiredRoles;
    }
}