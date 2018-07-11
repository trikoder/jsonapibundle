<?php

namespace Trikoder\JsonApiBundle\Config;

use Trikoder\JsonApiBundle\Contracts\Config\DeleteConfigInterface;

/**
 * Class DeleteConfig
 */
final class DeleteConfig implements DeleteConfigInterface
{
    /**
     * @return array|null
     */
    private $requiredRoles;

    /**
     * DeleteConfig constructor.
     *
     * @param array|null $requiredRoles
     */
    public function __construct(
        array $requiredRoles = null
    ) {
        $this->requiredRoles = $requiredRoles;
    }

    /**
     * @return array|null
     */
    public function getDeleteRequiredRoles()
    {
        return $this->requiredRoles;
    }
}
