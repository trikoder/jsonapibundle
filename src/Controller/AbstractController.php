<?php

namespace Trikoder\JsonApiBundle\Controller;

use Closure;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Trikoder\JsonApiBundle\Config\Config;
use Trikoder\JsonApiBundle\Contracts\Config\ConfigInterface;
use Trikoder\JsonApiBundle\Contracts\RepositoryInterface;
use Trikoder\JsonApiBundle\Contracts\SchemaClassMapProviderInterface;

/**
 * Class AbstractController
 * @package Trikoder\JsonApiBundle\Controller
 */
abstract class AbstractController extends Controller implements JsonApiEnabledInterface
{
    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * config for controller, it is populated on first access to config inside getConfig
     * private visibility so not direct access is enabled
     *
     * @var ConfigInterface
     */
    private $config;

    /**
     * Returns schema class map provider
     * Public visibility so listeners can get info of it
     *
     * @return SchemaClassMapProviderInterface
     */
    public function getSchemaClassMapProvider()
    {
        return $this->get('trikoder.jsonapi.schema_class_map_provider');
    }

    /**
     * @param ConfigInterface $config
     */
    public function setJsonApiConfig(ConfigInterface $config)
    {
        if (null !== $this->config) {
            throw new \LogicException("Config for controller is already set");
        } else {
            $this->config = $config;
        }
    }

    /**
     * Returns config
     *
     * @return ConfigInterface
     * @throws Exception
     */
    public function getJsonApiConfig()
    {
        return $this->config;
    }

    /**
     * Helper methods for IDEs and type hinting
     *
     * @return RepositoryInterface
     */
    protected function getRepository()
    {
        return $this->getJsonApiConfig()->getApi()->getRepository();
    }
}
