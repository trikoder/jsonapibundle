<?php

namespace Trikoder\JsonApiBundle\Tests\Unit\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Trikoder\JsonApiBundle\Config\Annotation\Config;
use Trikoder\JsonApiBundle\Config\Annotation\CreateConfig;
use Trikoder\JsonApiBundle\Config\Annotation\DeleteConfig;
use Trikoder\JsonApiBundle\Config\Annotation\IndexConfig;
use Trikoder\JsonApiBundle\Config\Annotation\UpdateConfig;
use Trikoder\JsonApiBundle\Contracts\RepositoryInterface;
use Trikoder\JsonApiBundle\Contracts\RequestBodyDecoderInterface;
use Trikoder\JsonApiBundle\Model\ModelFactoryInterface;
use Trikoder\JsonApiBundle\Model\ModelFactoryResolver;
use Trikoder\JsonApiBundle\Model\ModelFactoryResolverInterface;
use Trikoder\JsonApiBundle\Repository\RepositoryFactoryInterface;
use Trikoder\JsonApiBundle\Repository\RepositoryResolver;
use Trikoder\JsonApiBundle\Repository\RepositoryResolverInterface;
use Trikoder\JsonApiBundle\Services\ConfigBuilder;

/**
 * Class ConfigBuilderTest
 * @package Trikoder\JsonApiBundle\Tests\Unit\Services
 */
class ConfigBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return ContainerInterface
     */
    protected function getContainerMock()
    {
        $mocker = $this->getMockBuilder(ContainerInterface::class)->disableOriginalConstructor()->getMock();
        $mocker->method('has')->willReturn(true);
        $mocker->method('get')->will($this->returnCallback(function (...$args) {
            switch ($args[0]) {
                case 'request_body_decoder':
                    return $this->getRequestBodyDecoderMock();
                    break;
                case 'repository':
                    return $this->getRepositoryMock();
                    break;
            }
            return null;
        }));
        return $mocker;
    }

    /**
     * @return RequestBodyDecoderInterface
     */
    protected function getRequestBodyDecoderMock()
    {
        return $this->getMockBuilder(RequestBodyDecoderInterface::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * @return RepositoryInterface
     */
    protected function getRepositoryMock()
    {
        return $this->getMockBuilder(RepositoryInterface::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * @return ModelFactoryInterface
     */
    protected function getModelFactoryMock()
    {
        return $this->getMockBuilder(ModelFactoryInterface::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * @return ModelFactoryResolver
     */
    protected function getModelFactoryResolverMock()
    {
        $mock = $this->getMockBuilder(ModelFactoryResolverInterface::class)->disableOriginalConstructor()->getMock();
        $mock->method('resolve')->willReturn($this->getModelFactoryMock());
        return $mock;
    }

    /**
     * @return RepositoryResolverInterface
     */
    protected function getRepositoryResolverMock()
    {
        $mock = $this->getMockBuilder(RepositoryResolverInterface::class)->disableOriginalConstructor()->getMock();
        $mock->method('resolve')->willReturn($this->getRepositoryMock());
        return $mock;
    }

    /**
     * @return RepositoryFactoryInterface
     */
    protected function getRepositoryFactoryMock()
    {
        $factory = $this->getMockBuilder(RepositoryFactoryInterface::class)->disableOriginalConstructor()->getMock();
        $factory->method('create')->willReturn($this->getRepositoryMock());
        return $factory;
    }

    public function testFromAnnotation()
    {
        $builder = new ConfigBuilder($this->getArrayConfig(), $this->getContainerMock());
        // create empty annotations
        $annotationConfig = $this->getAnnotationsConfig();
        // run build
        $config = $builder->fromAnnotation($annotationConfig);

        // check results
        $this->assertEquals('modelClass', $config->getApi()->getModelClass());
        $this->assertInstanceOf(RepositoryInterface::class, $config->getApi()->getRepository());
        $this->assertInstanceOf(RequestBodyDecoderInterface::class, $config->getApi()->getRequestBodyDecoder());
        $this->assertEquals([1], $config->getApi()->getFixedFiltering());
        $this->assertEquals([2], $config->getApi()->getAllowedIncludePaths());
        $this->assertTrue($config->getApi()->getAllowExtraParams());

        $this->assertEquals([3], $config->getIndex()->getIndexAllowedSortFields());
        $this->assertEquals([4], $config->getIndex()->getIndexAllowedFilteringParameters());
        $this->assertEquals([5], $config->getIndex()->getIndexDefaultSort());
        $this->assertEquals([6], $config->getIndex()->getIndexDefaultPagination());
        $this->assertEquals([7], $config->getIndex()->getIndexAllowedFields());

        $this->assertEquals([8], $config->getCreate()->getCreateAllowedFields());
        $this->assertEquals([9], $config->getCreate()->getCreateRequiredRoles());
        $this->assertInstanceOf(ModelFactoryInterface::class, $config->getCreate()->getCreateFactory());

        $this->assertEquals([10], $config->getUpdate()->getUpdateAllowedFields());
        $this->assertEquals([11], $config->getUpdate()->getUpdateRequiredRoles());

        $this->assertEquals([12], $config->getDelete()->getDeleteRequiredRoles());

    }

    public function testFromAnnotationWithModelResolver()
    {
        $builder = new ConfigBuilder($this->getArrayConfig(), $this->getContainerMock());
        // create empty annotations
        $annotationConfig = $this->getAnnotationsConfig();
        $annotationConfig->create->factory = $this->getModelFactoryResolverMock();
        // run build
        $config = $builder->fromAnnotation($annotationConfig);

        // make sure we got model factory
        $this->assertInstanceOf(ModelFactoryInterface::class, $config->getCreate()->getCreateFactory());
    }

    public function testFromAnnotationWithModelResolverDefaultFactory()
    {
        $resolver = new ModelFactoryResolver();
        $resolver->registerFactory($this->getModelFactoryMock());

        $builder = new ConfigBuilder($this->getArrayConfig(), $this->getContainerMock());
        // create empty annotations
        $annotationConfig = $this->getAnnotationsConfig();
        $annotationConfig->create->factory = $resolver;
        // run build
        $config = $builder->fromAnnotation($annotationConfig);

        // make sure we got model factory
        $this->assertInstanceOf(ModelFactoryInterface::class, $config->getCreate()->getCreateFactory());
    }

    public function testFromAnnotationWithRepositoryResolver()
    {
        $builder = new ConfigBuilder($this->getArrayConfig(), $this->getContainerMock());
        // create empty annotations
        $annotationConfig = $this->getAnnotationsConfig();
        $annotationConfig->repository = $this->getRepositoryResolverMock();
        // run build
        $config = $builder->fromAnnotation($annotationConfig);

        // make sure we got model factory
        $this->assertInstanceOf(RepositoryInterface::class, $config->getApi()->getRepository());
    }

    public function testFromAnnotationWithRepositoryFactory()
    {
        $resolver = new RepositoryResolver();
        $resolver->registerFactory($this->getRepositoryFactoryMock(), 'modelClass');

        $builder = new ConfigBuilder($this->getArrayConfig(), $this->getContainerMock());
        // create empty annotations
        $annotationConfig = $this->getAnnotationsConfig();
        $annotationConfig->repository = $resolver;
        // run build
        $config = $builder->fromAnnotation($annotationConfig);

        // make sure we got model factory
        $this->assertInstanceOf(RepositoryInterface::class, $config->getApi()->getRepository());
    }

    public function testFromAnnotationWithDefaultRepositoryFactory()
    {
        $resolver = new RepositoryResolver();
        $resolver->registerFactory($this->getRepositoryFactoryMock());

        $builder = new ConfigBuilder($this->getArrayConfig(), $this->getContainerMock());
        // create empty annotations
        $annotationConfig = $this->getAnnotationsConfig();
        $annotationConfig->repository = $resolver;
        // run build
        $config = $builder->fromAnnotation($annotationConfig);

        // make sure we got model factory
        $this->assertInstanceOf(RepositoryInterface::class, $config->getApi()->getRepository());
    }

    /**
     * @return Config
     */
    protected function getAnnotationsConfig()
    {
        $annotationConfig = new Config();
        $annotationConfig->index = new IndexConfig();
        $annotationConfig->create = new CreateConfig();
        $annotationConfig->update = new UpdateConfig();
        $annotationConfig->delete = new DeleteConfig();

        $annotationConfig->modelClass = 'modelClass';
        $annotationConfig->repository = 'repository';
        $annotationConfig->requestBodyDecoder = 'request_body_decoder';
        $annotationConfig->fixedFiltering = [1];
        $annotationConfig->allowedIncludePaths = [2];
        $annotationConfig->allowExtraParams = true;

        $annotationConfig->index->allowedSortFields = [3];
        $annotationConfig->index->allowedFilteringParameters = [4];
        $annotationConfig->index->defaultSort = [5];
        $annotationConfig->index->defaultPagination = [6];
        $annotationConfig->index->allowedFields = [7];

        $annotationConfig->create->factory = $this->getModelFactoryMock();
        $annotationConfig->create->allowedFields = [8];
        $annotationConfig->create->requiredRoles = [9];

        $annotationConfig->update->allowedFields = [10];
        $annotationConfig->update->requiredRoles = [11];

        $annotationConfig->delete->requiredRoles = [12];

        return $annotationConfig;
    }

    /**
     * @return array
     */
    protected function getArrayConfig()
    {
        return [
            'model_class' => '',
            'repository' => '',
            'request_body_decoder' => '',
            'fixed_filtering' => '',
            'allowed_include_paths' => '',
            'allow_extra_params' => '',
            'index' => [
                'allowed_sort_fields' => '',
                'allowed_filtering_parameters' => '',
                'default_sort' => '',
                'default_pagination' => '',
                'allowed_fields' => '',
            ],
            'create' => [
                'factory' => '',
                'allowed_fields' => '',
                'required_roles' => '',
            ],
            'update' => [
                'allowed_fields' => '',
                'required_roles' => '',
            ],
            'delete' => [
                'required_roles' => '',
            ],
        ];
    }
}
