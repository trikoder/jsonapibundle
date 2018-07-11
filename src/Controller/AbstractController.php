<?php

namespace Trikoder\JsonApiBundle\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Trikoder\JsonApiBundle\Config\Config;
use Trikoder\JsonApiBundle\Contracts\Config\ConfigInterface;
use Trikoder\JsonApiBundle\Contracts\RepositoryInterface;
use Trikoder\JsonApiBundle\Contracts\SchemaClassMapProviderInterface;
use Trikoder\JsonApiBundle\Controller\Traits\Polyfill\SymfonyAutowiredServicesTrait;

/**
 * Class AbstractController
 */
abstract class AbstractController implements JsonApiEnabledInterface
{
    use SymfonyAutowiredServicesTrait;

    /**
     * @var SchemaClassMapProviderInterface
     */
    private $schemaClassMapProvider;

    /**
     * @required
     */
    public function setSchemaClassMapProvider(SchemaClassMapProviderInterface $schemaClassMapProvider)
    {
        if (null !== $this->schemaClassMapProvider) {
            throw new \RuntimeException("Controller already has it's schema map defined. This action would override current value. If this is acceptable for this controller, you should override this method to set the value.");
        }
        $this->schemaClassMapProvider = $schemaClassMapProvider;
    }

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
        return $this->schemaClassMapProvider;
    }

    /**
     * @param ConfigInterface $config
     */
    public function setJsonApiConfig(ConfigInterface $config)
    {
        if (null !== $this->config) {
            throw new \LogicException('Config for controller is already set');
        } else {
            $this->config = $config;
        }
    }

    /**
     * Returns config
     *
     * @return ConfigInterface
     *
     * @throws Exception
     */
    public function getJsonApiConfig()
    {
        return $this->config;
    }
}
