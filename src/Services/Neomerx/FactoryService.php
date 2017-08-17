<?php

namespace Trikoder\JsonApiBundle\Services\Neomerx;

use Neomerx\JsonApi\Encoder\Encoder;
use Neomerx\JsonApi\Encoder\EncoderOptions;
use Neomerx\JsonApi\Factories\Factory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class FactoryService
 * @package Trikoder\JsonApiBundle\Services\Neomerx
 */
class FactoryService extends Factory
{
    /**
     * @var ContainerInterface
     */
    private $serviceContainer;

    /**
     * FactoryService constructor.
     * @param ContainerInterface $serviceContainer
     */
    public function __construct(ContainerInterface $serviceContainer)
    {
        // respect parent implementation
        parent::__construct();

        // save container
        $this->serviceContainer = $serviceContainer;

        // set logger by default to avoid duplication of calls from service definition
        $this->setLogger($this->serviceContainer->get('logger'));
    }

    /**
     * Creates new encoder instance for use
     * This is symfony way for creating with factory as service,
     * neomerx way is @see \Neomerx\JsonApi\Encoder\Encoder::instance
     *
     * @param array $schemas
     * @param EncoderOptions|null $encoderOptions
     * @return Encoder
     */
    public function createEncoderInstance(array $schemas = [], EncoderOptions $encoderOptions = null)
    {
        $container = $this->createContainer($schemas);
        $encoder = new Encoder($this, $container, $encoderOptions);
        $encoder->setLogger($this->logger);
        return $encoder;
    }

    /**
     * @inheritdoc
     */
    public function createContainer(array $providers = [])
    {
        $container = new Container($this->serviceContainer, $this, $providers);
        $container->setLogger($this->logger);
        return $container;
    }

}