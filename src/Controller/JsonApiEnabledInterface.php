<?php

namespace Trikoder\JsonApiBundle\Controller;

use Trikoder\JsonApiBundle\Contracts\Config\ConfigInterface;
use Trikoder\JsonApiBundle\Contracts\SchemaClassMapProviderInterface;

/**
 * Interface JsonApiEnabledInterface
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
     */
    public function getJsonApiConfig(): ConfigInterface;

    /**
     */
    public function setJsonApiConfig(ConfigInterface $config);
}
