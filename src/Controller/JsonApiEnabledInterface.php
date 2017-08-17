<?php

namespace Trikoder\JsonApiBundle\Controller;

use Trikoder\JsonApiBundle\Contracts\Config\ConfigInterface;
use Trikoder\JsonApiBundle\Contracts\SchemaClassMapProviderInterface;

/**
 * Interface JsonApiEnabledInterface
 * @package Trikoder\JsonApiBundle\Controller
 */
interface JsonApiEnabledInterface
{
    /**
     * Returns schema class map provider
     *
     * @return SchemaClassMapProviderInterface
     */
    public function getSchemaClassMapProvider();

    /**
     * Returns controllers config
     *
     * @return ConfigInterface
     */
    public function getJsonApiConfig();
}
