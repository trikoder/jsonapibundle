<?php

namespace Trikoder\JsonApiBundle\Services\Neomerx;

use Neomerx\JsonApi\Contracts\Schema\SchemaProviderInterface;
use Neomerx\JsonApi\Encoder\Encoder;
use Neomerx\JsonApi\Encoder\EncoderOptions;
use Neomerx\JsonApi\Factories\Factory;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class FactoryService
 */
class FactoryService extends Factory
{
    /**
     * @var ContainerInterface
     */
    private $serviceContainer;

    /**
     * FactoryService constructor.
     *
     * @param ContainerInterface $serviceContainer
     */
    public function __construct(ServiceContainer $serviceContainer, LoggerInterface $logger)
    {
        // respect parent implementation
        parent::__construct();

        // save container
        $this->serviceContainer = $serviceContainer;

        // set logger by default to avoid duplication of calls from service definition
        $this->setLogger($logger);
    }

    /**
     * Creates new encoder instance for use
     * This is symfony way for creating with factory as service,
     * neomerx way is @see \Neomerx\JsonApi\Encoder\Encoder::instance
     *
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
     * {@inheritdoc}
     */
    public function createContainer(array $providers = [])
    {
        $container = new Container($this->serviceContainer, $this, $providers);
        $container->setLogger($this->logger);

        return $container;
    }

    /**
     * {@inheritdoc}
     */
    public function createResourceObject(
        SchemaProviderInterface $schema,
        $resource,
        $isInArray,
        $attributeKeysFilter = null
    ) {
        return new ResourceObject($schema, $resource, $isInArray, $attributeKeysFilter);
    }
}
