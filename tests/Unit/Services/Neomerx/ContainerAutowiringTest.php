<?php

namespace Trikoder\JsonApiBundle\Tests\Unit\Services;

use Neomerx\JsonApi\Contracts\Schema\SchemaFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\RememberMe\RememberMeServicesInterface;
use Trikoder\JsonApiBundle\Schema\Autowire\Exception\UnresolvedDependencyException;
use Trikoder\JsonApiBundle\Services\Neomerx\Container;
use Trikoder\JsonApiBundle\Tests\Resources\JsonApi\Schema\CustomerSchema;
use Trikoder\JsonApiBundle\Tests\Resources\JsonApi\Schema\Test\CrazySchema;
use Trikoder\JsonApiBundle\Tests\Resources\JsonApi\Schema\Test\InvalidSchema;
use Trikoder\JsonApiBundle\Tests\Resources\JsonApi\Schema\UserSchema;

class ContainerAutowiringTest extends \PHPUnit_Framework_TestCase
{
    private function getServiceContainer()
    {
        $serviceContainer = $this->getMockBuilder(ContainerInterface::class)->disableOriginalConstructor()->getMock();
        $serviceContainer->method('has')->will($this->returnCallback(function ($service) {
            switch ($service) {
                case RouterInterface::class:
                    return true;
                    break;
            }
        }));
        $serviceContainer->method('get')->will($this->returnCallback(function ($service) {
            switch ($service) {
                case RouterInterface::class:
                    return $this->createMock(RouterInterface::class);
                    break;
            }
        }));

        return $serviceContainer;
    }

    private function getSchemaFactory()
    {
        $schemaFactory = $this->getMockBuilder(SchemaFactoryInterface::class)->disableOriginalConstructor()->getMock();

        return $schemaFactory;
    }

    public function testInterfaceDependancy()
    {
        $container = new TestContainer($this->getServiceContainer(), $this->getSchemaFactory(), [
        ]);
        $customerSchema = $container->createSchemaFromClassNameForTest(CustomerSchema::class);

        $this->assertNotNull($customerSchema);
        $this->assertInstanceOf(CustomerSchema::class, $customerSchema);
    }

    public function testINoDependancy()
    {
        $container = new TestContainer($this->getServiceContainer(), $this->getSchemaFactory(), []);
        $schema = $container->createSchemaFromClassNameForTest(UserSchema::class);

        $this->assertNotNull($schema);
        $this->assertInstanceOf(UserSchema::class, $schema);
    }

    public function testInvalidTypeHint()
    {
        $container = new TestContainer($this->getServiceContainer(), $this->getSchemaFactory(), []);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(sprintf('Argument %s for schema %s is not type hinted and cannot be autowired!',
            1, InvalidSchema::class));
        $schema = $container->createSchemaFromClassNameForTest(InvalidSchema::class);
    }

    public function testFailedResolving()
    {
        $container = new TestContainer($this->getServiceContainer(), $this->getSchemaFactory(), []);

        $this->expectException(UnresolvedDependencyException::class);
        $this->expectExceptionMessage(sprintf('Cannot resolve argument %s for schema %s with hint %s. Did you forget to register service or alias?',
            1, CrazySchema::class, RememberMeServicesInterface::class));
        $schema = $container->createSchemaFromClassNameForTest(CrazySchema::class);
    }
}

class TestContainer extends Container
{
    public function createSchemaFromClassNameForTest($className)
    {
        return $this->createSchemaFromClassName($className);
    }
}
