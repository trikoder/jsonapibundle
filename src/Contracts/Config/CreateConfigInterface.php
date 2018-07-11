<?php

namespace Trikoder\JsonApiBundle\Contracts\Config;

use Closure;
use Trikoder\JsonApiBundle\Model\ModelFactoryInterface;

/**
 * Interface CreateConfigInterface
 */
interface CreateConfigInterface
{
    /**
     * Closure that returns empty model
     *
     * @return ModelFactoryInterface
     */
    public function getCreateFactory();

    /**
     * List of allowed fields in body, [] for nothing is allowed, null for everything is allowed
     *
     * @return array|null
     */
    public function getCreateAllowedFields();

    /**
     * List of roles required to access action, [] for nothing is allowed, null for everything is allowed
     *
     * @return array|null
     */
    public function getCreateRequiredRoles();
}
