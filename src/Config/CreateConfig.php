<?php

namespace Trikoder\JsonApiBundle\Config;

use Trikoder\JsonApiBundle\Config\Traits\LoadLazyPropertyTrait;
use Trikoder\JsonApiBundle\Contracts\Config\CreateConfigInterface;
use Trikoder\JsonApiBundle\Model\ModelFactoryInterface;

/**
 * Class CreateConfig
 * @package Trikoder\JsonApiBundle\Config
 */
final class CreateConfig implements CreateConfigInterface
{
    use LoadLazyPropertyTrait;

    /**
     * @return ModelFactoryInterface
     */
    private $factory;

    /**
     * @return array|null
     */
    private $allowedFields;

    /**
     * @return array|null
     */
    private $requiredRoles;

    /**
     * CreateConfig constructor.
     * @param ModelFactoryInterface $factory
     * @param array|null $allowedFields
     * @param array|null $requiredRoles
     */
    public function __construct(
        ModelFactoryInterface $factory,
        array $allowedFields = null,
        array $requiredRoles = null
    ) {
        $this->allowedFields = $allowedFields;
        $this->requiredRoles = $requiredRoles;
        $this->factory = $factory;
    }

    /**
     * @return ModelFactoryInterface
     */
    public function getCreateFactory()
    {
        return $this->lazyLoadProperty('factory');
    }

    /**
     * @return array|null
     */
    public function getCreateAllowedFields()
    {
        return $this->allowedFields;
    }

    /**
     * @return array|null
     */
    public function getCreateRequiredRoles()
    {
        return $this->requiredRoles;
    }
}
