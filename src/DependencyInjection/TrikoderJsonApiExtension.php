<?php

namespace Trikoder\JsonApiBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @see http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class TrikoderJsonApiExtension extends ConfigurableExtension
{
    /**
     * Configures the passed container according to the merged configuration.
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        // load services
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.yml');

        // inject defaults to config builder
        $configBuilderDefinition = $container->getDefinition('trikoder.jsonapi.config_builder');
        $configBuilderDefinition->replaceArgument(0, $mergedConfig);

        $listenerDefinition = new Definition();
        $listenerDefinition->setClass('%trikoder.jsonapi.request_listener.class%')->setArguments([
            new Reference('trikoder.jsonapi.factory'),
            new Reference('trikoder.jsonapi.request_body_decoder'),
            new Reference('trikoder.jsonapi.response_factory'),
            new Reference('trikoder.jsonapi.encoder'),
            new Reference('logger'),
        ])->addTag('kernel.event_listener', [
            'event' => KernelEvents::CONTROLLER,
            'priority' => 16,
        ])->addTag('kernel.event_listener', [
            'event' => KernelEvents::CONTROLLER_ARGUMENTS,
            'priority' => -10,
        ])->addTag('kernel.event_listener', [
            'event' => KernelEvents::VIEW,
            'priority' => $mergedConfig['kernel_listener_on_kernel_view_priority'],
        ])->addTag('kernel.event_listener', [
            'event' => KernelEvents::RESPONSE,
        ])->addTag('kernel.event_listener', [
            'event' => KernelEvents::EXCEPTION,
            'priority' => $mergedConfig['kernel_listener_on_kernel_exception_priority'],
        ]);

        $container->setDefinition('trikoder.jsonapi.request_listener', $listenerDefinition);
    }
}
