<?php

namespace Trikoder\JsonApiBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('trikoder_json_api');

        // TODO - add info for config items
        // TODO - add examples for variables

        $rootNode
            ->children()
                ->scalarNode('model_class')
                    ->defaultValue('\stdClass')
                    ->end()
                ->scalarNode('repository')
                    ->defaultValue('trikoder.jsonapi.doctrine_repository_factory')
                    ->end()
                ->scalarNode('request_body_decoder')
                    ->defaultValue('trikoder.jsonapi.request_body_decoder')
                    ->end()
                ->variableNode('fixed_filtering')->defaultValue([])->end()
                ->variableNode('allowed_include_paths')->defaultNull()->end()
                ->booleanNode('allow_extra_params')->defaultFalse()->end()
                ->arrayNode('index')
                    ->children()
                        ->variableNode('allowed_sort_fields')->defaultNull()->end()
                        ->variableNode('allowed_filtering_parameters')->defaultNull()->end()
                        ->variableNode('default_sort')->defaultValue([])->end()
                        ->variableNode('default_pagination')->defaultValue([])->end()
                        ->variableNode('allowed_fields')->defaultNull()->end()
                        ->variableNode('required_roles')->defaultNull()->end()
                    ->end()
                ->addDefaultsIfNotSet()
                ->end()
                ->arrayNode('create')
                    ->children()
                        ->scalarNode('factory')->defaultValue('trikoder.jsonapi.simple_model_factory')->end()
                        ->variableNode('allowed_fields')->defaultNull()->end()
                        ->variableNode('required_roles')->defaultNull()->end()
                    ->end()
                ->addDefaultsIfNotSet()
                ->end()
                ->arrayNode('update')
                    ->children()
                        ->variableNode('allowed_fields')->defaultNull()->end()
                        ->variableNode('required_roles')->defaultNull()->end()
                    ->end()
                ->addDefaultsIfNotSet()
                ->end()
                ->arrayNode('delete')
                    ->children()
                        ->variableNode('required_roles')->defaultNull()->end()
                    ->end()
                ->addDefaultsIfNotSet()
                ->end()
                ->arrayNode('schema_automap_scan_patterns')
                    ->scalarPrototype()->end()->defaultValue([])
                ->end()
            ->end();
        $rootNode->addDefaultsIfNotSet();

        return $treeBuilder;
    }
}
