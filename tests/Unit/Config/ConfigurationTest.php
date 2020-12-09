<?php

namespace Trikoder\JsonApiBundle\Tests\Unit\Services;

use Trikoder\JsonApiBundle\DependencyInjection\Configuration;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testConfig()
    {
        $configuration = new Configuration();

        $node = $configuration->getConfigTreeBuilder()->buildTree();
        $actualConfig = $node->getDefaultValue();

        $this->assertEquals($this->getExpectedConfig(), $actualConfig);
    }

    protected function getExpectedConfig()
    {
        return [
            'model_class' => '\stdClass',
            'repository' => 'trikoder.jsonapi.doctrine_repository_factory',
            'request_body_decoder' => 'trikoder.jsonapi.request_body_decoder',
            'relationship_request_body_decoder' => 'trikoder.jsonapi.relationship_request_body_decoder',
            'fixed_filtering' => [],
            'allowed_include_paths' => null,
            'allow_extra_params' => false,
            'index' => [
                'allowed_sort_fields' => null,
                'allowed_filtering_parameters' => null,
                'default_sort' => [],
                'default_pagination' => [],
                'allowed_fields' => null,
                'required_roles' => null,
            ],
            'create' => [
                'factory' => 'trikoder.jsonapi.simple_model_factory',
                'allowed_fields' => null,
                'required_roles' => null,
            ],
            'update' => [
                'allowed_fields' => null,
                'required_roles' => null,
            ],
            'delete' => [
                'required_roles' => null,
            ],
            'schema_automap_scan_patterns' => [],
            'kernel_listener_on_kernel_view_priority' => 0,
            'kernel_listener_on_kernel_exception_priority' => 0,
            'request_body_validator' => 'trikoder.jsonapi.request_body_validator',
            'relationship_request_body_validator' => 'trikoder.jsonapi.relationship_request_body_validator',
        ];
    }
}
