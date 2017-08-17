<?php

namespace Trikoder\JsonApiBundle\Listener;

use Doctrine\Common\Annotations\Reader;
use ReflectionClass;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Trikoder\JsonApiBundle\Config\Annotation;
use Trikoder\JsonApiBundle\Controller\AbstractController;
use Trikoder\JsonApiBundle\Services\ConfigBuilder;

/**
 * Class ControllerConfigListener
 * @package Trikoder\JsonApiBundle\Listener
 */
class ControllerConfigListener
{
    use JsonApiEnabledControllerDetectorTrait;

    /**
     * @var Reader
     */
    private $annotationReader;
    /**
     * @var ConfigBuilder
     */
    private $configBuilder;

    public function __construct(Reader $annotationReader, ConfigBuilder $configBuilder)
    {
        $this->annotationReader = $annotationReader;
        $this->configBuilder = $configBuilder;
    }

    /**
     *
     *
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        /** @var AbstractController $controller */
        $controller = $event->getController()[0];

        // if api enabled controller, inject config
        if (true === $this->isJsonApiEnabledController($controller)) {
            // prepare class annotations in variable for later use
            /** @var Annotation\Config $configAnnotation */
            $configAnnotation = $this->annotationReader->getClassAnnotation(new ReflectionClass($controller),
                Annotation\Config::class);

            $config = $this->configBuilder->fromAnnotation($configAnnotation);

            $controller->setJsonApiConfig($config);
        }
    }
}
