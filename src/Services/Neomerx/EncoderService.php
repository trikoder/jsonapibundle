<?php

namespace Trikoder\JsonApiBundle\Services\Neomerx;

use Iterator;
use Neomerx\JsonApi\Contracts\Document\ErrorInterface;
use Neomerx\JsonApi\Contracts\Encoder\Parameters\EncodingParametersInterface;
use Neomerx\JsonApi\Document\Link;
use Neomerx\JsonApi\Encoder\EncoderOptions;
use Trikoder\JsonApiBundle\Contracts\SchemaClassMapProviderInterface;

/**
 * Class EncoderService
 */
class EncoderService
{
    /**
     * @var FactoryService
     */
    private $jsonApiFactory;

    /**
     * @var EncoderOptions
     */
    private $encoderOptions;

    /**
     * @var bool
     */
    private $configurationEncodingPrettyPrint = true;

    /**
     * EncoderService constructor.
     *
     * @param FactoryService $factoryService
     */
    public function __construct(FactoryService $factoryService)
    {
        $this->jsonApiFactory = $factoryService;
        $this->buildEncoderOptions();
    }

    /**
     * @param SchemaClassMapProviderInterface $classMapProvider
     * @param array|Iterator|null|object|string $data
     * @param EncodingParametersInterface|null $encodingParameters
     * @param array|null $meta
     * @param array $links
     *
     * @return string
     */
    public function encode(
        SchemaClassMapProviderInterface $classMapProvider,
        $data = '',
        EncodingParametersInterface $encodingParameters = null,
        array $meta = null,
        array $links = []
    ) {
        // encode links if any
        if (false === empty($links)) {
            foreach ($links as &$linksItem) {
                if (false === ($linksItem instanceof Link) && null !== $linksItem) {
                    $linksItem = new Link($linksItem);
                }
            }
        }

        // TODO - this should be service
        $encoder = $this->jsonApiFactory->createEncoderInstance(
            $classMapProvider->getMap(),
            $this->encoderOptions
        );

        return $encoder->withMeta($meta)->withLinks($links)->withJsonApiVersion()->encodeData($data,
            $encodingParameters);
    }

    /**
     * @param ErrorInterface[] $errors
     * @param array|null $meta
     * @param array $links
     *
     * @return string
     */
    public function encodeErrors(
        $errors = [],
        array $meta = null,
        array $links = []
    ) {
        // encode links if any
        if (false === empty($links)) {
            foreach ($links as &$linksItem) {
                if (false === ($linksItem instanceof Link)) {
                    $linksItem = new Link($linksItem);
                }
            }
        }

        // TODO - this should be service
        $encoder = $this->jsonApiFactory->createEncoderInstance(
            [],
            $this->encoderOptions
        );

        return $encoder->withMeta($meta)->withLinks($links)->withJsonApiVersion()->encodeErrors($errors);
    }

    /**
     * @param bool $prettyPrint
     */
    public function configureSetPrettyPrint(bool $prettyPrint = true)
    {
        $this->configurationEncodingPrettyPrint = $prettyPrint;
        $this->buildEncoderOptions();
    }

    /**
     * @return EncoderOptions
     */
    private function buildEncoderOptions()
    {
        $options = 0;
        if (true === $this->configurationEncodingPrettyPrint) {
            $options += JSON_PRETTY_PRINT;
        }
        $this->encoderOptions = new EncoderOptions($options, '');
    }
}
