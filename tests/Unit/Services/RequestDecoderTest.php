<?php

namespace Trikoder\JsonApiBundle\Tests\Unit\Services;

use Doctrine\ORM\EntityManager;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Trikoder\JsonApiBundle\Config\ApiConfig;
use Trikoder\JsonApiBundle\Config\Config;
use Trikoder\JsonApiBundle\Config\CreateConfig;
use Trikoder\JsonApiBundle\Config\DeleteConfig;
use Trikoder\JsonApiBundle\Config\IndexConfig;
use Trikoder\JsonApiBundle\Config\UpdateConfig;
use Trikoder\JsonApiBundle\Contracts\RequestBodyDecoderInterface;
use Trikoder\JsonApiBundle\Controller\JsonApiEnabledInterface;
use Trikoder\JsonApiBundle\Model\ModelFactoryInterface;
use Trikoder\JsonApiBundle\Services\Neomerx\Container;
use Trikoder\JsonApiBundle\Services\Neomerx\FactoryService;
use Trikoder\JsonApiBundle\Services\RequestBodyDecoderService;
use Trikoder\JsonApiBundle\Services\RequestDecoder;

/**
 * Class RequestDecoderTest
 * @package Trikoder\JsonApiBundle\Tests\Unit\Services
 */
class RequestDecoderTest extends \PHPUnit_Framework_TestCase
{

    public function testMultipleValueFilter()
    {
        // prepare mocked config and controller
        $controller = $this->getMockBuilder(JsonApiEnabledInterface::class)->disableOriginalConstructor()->getMock();
        $apiConfig = new Config(
            new ApiConfig("\stdClass", null, null, null, $this->getRequestBodyDecoderMock(), false),
            new CreateConfig($this->getModelFactoryMock()),
            new IndexConfig(),
            new UpdateConfig(),
            new DeleteConfig()
        );
        $controller->method('getJsonApiConfig')->willReturn($apiConfig);

        $requestDecoder = $this->getRequestDecoder($controller);


        // test single value
        $request = new Request(['filter' => ['myfield' => 'value12']]);
        $result = $requestDecoder->decode($request);
        $this->assertEquals([
            'myfield' => 'value12'
        ], $result->query->get('filter'));

        // test simple case
        $request = new Request(['filter' => ['myfield' => 'value1,value2']]);
        $result = $requestDecoder->decode($request);
        $this->assertEquals([
            'myfield' => [
                'value1',
                'value2',
            ]
        ], $result->query->get('filter'));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|FactoryService
     */
    private function getFactoryServiceMock()
    {
        $container = $this->getMockBuilder(ContainerInterface::class)->disableOriginalConstructor()->getMock();
        $container->method('has')->willReturn(true);
        $container->method('get')->will($this->returnCallback(function (...$args) {
            switch ($args[0]) {
                case 'logger':
                    return $this->getMockBuilder(LoggerInterface::class)->disableOriginalConstructor()->getMock();
                    break;
            }
            return null;
        }));
        $factory = new FactoryService($container);
        return $factory;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ModelFactoryInterface
     */
    private function getModelFactoryMock()
    {
        return $this->getMockBuilder(ModelFactoryInterface::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RequestBodyDecoderInterface
     */
    private function getRequestBodyDecoderMock()
    {
        return $this->getMockBuilder(RequestBodyDecoderInterface::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * @param JsonApiEnabledInterface $controller
     * @return RequestDecoder
     */
    private function getRequestDecoder(JsonApiEnabledInterface $controller)
    {
        return new RequestDecoder($this->getFactoryServiceMock(), $controller);
    }
}
