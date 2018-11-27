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
        $this->assertEquals(['fixed_filtering'], $config->getApi()->getFixedFiltering());
        $this->assertEquals(['allowed_include_paths'], $config->getApi()->getAllowedIncludePaths());
        $this->assertTrue($config->getApi()->getAllowExtraParams());

        $this->assertEquals(['allowed_sort_fields'], $config->getIndex()->getIndexAllowedSortFields());
        $this->assertEquals(['allowed_filtering_parameters'], $config->getIndex()->getIndexAllowedFilteringParameters());
        $this->assertEquals(['default_sort'], $config->getIndex()->getIndexDefaultSort());
        $this->assertEquals(['default_pagination'], $config->getIndex()->getIndexDefaultPagination());
        $this->assertEquals(['index_allowed_fields'], $config->getIndex()->getIndexAllowedFields());
        $this->assertEquals(['index_required_roles'], $config->getIndex()->getIndexRequiredRoles());

        $this->assertEquals(['create_allowed_fields'], $config->getCreate()->getCreateAllowedFields());
        $this->assertEquals(['create_required_roles'], $config->getCreate()->getCreateRequiredRoles());
        $this->assertInstanceOf(ModelFactoryInterface::class, $config->getCreate()->getCreateFactory());

        $this->assertEquals(['update_allowed_fields'], $config->getUpdate()->getUpdateAllowedFields());
        $this->assertEquals(['update_required_roles'], $config->getUpdate()->getUpdateRequiredRoles());

        $this->assertEquals(['delete_required_roles'], $config->getDelete()->getDeleteRequiredRoles());
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
        $annotationConfig->fixedFiltering = ['fixed_filtering'];
        $annotationConfig->allowedIncludePaths = ['allowed_include_paths'];
        $annotationConfig->allowExtraParams = true;

        $annotationConfig->index->allowedSortFields = ['allowed_sort_fields'];
        $annotationConfig->index->allowedFilteringParameters = ['allowed_filtering_parameters'];
        $annotationConfig->index->defaultSort = ['default_sort'];
        $annotationConfig->index->defaultPagination = ['default_pagination'];
        $annotationConfig->index->allowedFields = ['index_allowed_fields'];
        $annotationConfig->index->requiredRoles = ['index_required_roles'];

        $annotationConfig->create->factory = $this->getModelFactoryMock();
        $annotationConfig->create->allowedFields = ['create_allowed_fields'];
        $annotationConfig->create->requiredRoles = ['create_required_roles'];

        $annotationConfig->update->allowedFields = ['update_allowed_fields'];
        $annotationConfig->update->requiredRoles = ['update_required_roles'];

        $annotationConfig->delete->requiredRoles = ['delete_required_roles'];

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
                'required_roles' => '',
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
