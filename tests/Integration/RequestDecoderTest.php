<?php

namespace Trikoder\JsonApiBundle\Tests\Integration;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Trikoder\JsonApiBundle\Config\ApiConfig;
use Trikoder\JsonApiBundle\Config\Config;
use Trikoder\JsonApiBundle\Config\CreateConfig;
use Trikoder\JsonApiBundle\Config\DeleteConfig;
use Trikoder\JsonApiBundle\Config\IndexConfig;
use Trikoder\JsonApiBundle\Config\UpdateConfig;
use Trikoder\JsonApiBundle\Contracts\RequestBodyValidatorInterface;
use Trikoder\JsonApiBundle\Controller\JsonApiEnabledInterface;
use Trikoder\JsonApiBundle\Model\ModelFactoryInterface;
use Trikoder\JsonApiBundle\Services\Neomerx\FactoryService;
use Trikoder\JsonApiBundle\Services\Neomerx\ServiceContainer;
use Trikoder\JsonApiBundle\Services\RequestDecoder\RequestBodyDecoderService;
use Trikoder\JsonApiBundle\Services\RequestDecoder\RequestDecoder;

class RequestDecoderTest extends KernelTestCase
{
    public function testJsonBodyPayload()
    {
        $requestDecoder = $this->getRequestDecoder();

        $request = new Request([], [], [], [], [], [], json_encode([
            'data' => [
                'type' => 'test',
                'attributes' => [
                    'name' => 'value',
                ],
            ],
        ]));
        $result = $requestDecoder->decode($request);
        $this->assertEquals([
            'name' => 'value',
        ], $result->request->all());
    }

    public function testJsonPostPayload()
    {
        $requestDecoder = $this->getRequestDecoder();

        $request = new Request([], [
            'data' => json_encode([
                'type' => 'test',
                'attributes' => [
                    'name' => 'value',
                ],
            ]),
        ]);
        $result = $requestDecoder->decode($request);
        $this->assertEquals([
            'name' => 'value',
        ], $result->request->all());
    }

    public function testPlainPostPayload()
    {
        $requestDecoder = $this->getRequestDecoder();

        $request = new Request([], [
            'data' => [
                'type' => 'test',
                'attributes' => [
                    'name' => 'value',
                ],
            ],
        ]);
        $result = $requestDecoder->decode($request);
        $this->assertEquals([
            'name' => 'value',
        ], $result->request->all());
    }

    public function testInvalidPostDataPayload()
    {
        $requestDecoder = $this->getRequestDecoder();

        // number
        $request = new Request([], [
            'data' => 123,
        ]);
        $this->expectException(BadRequestHttpException::class);
        $result = $requestDecoder->decode($request);

        // non json string
        $request = new Request([], [
            'data' => 'invalid',
        ]);
        $this->expectException(BadRequestHttpException::class);
        $result = $requestDecoder->decode($request);
    }

    private function getRequestDecoder($controller = null)
    {
        if (null === $controller) {
            // prepare mocked config and controller
            $controller = $this->getMockBuilder(JsonApiEnabledInterface::class)->disableOriginalConstructor()->getMock();
            $apiConfig = new Config(
                new ApiConfig("\stdClass", null, null, null, $this->getRequestBodyDecoder(), false),
                new CreateConfig($this->getModelFactoryMock()),
                new IndexConfig(),
                new UpdateConfig(),
                new DeleteConfig()
            );
            $controller->method('getJsonApiConfig')->willReturn($apiConfig);
        }

        return new RequestDecoder($this->getFactoryServiceMock(), $controller);
    }

    private function getRequestBodyDecoder()
    {
        $validator = $this->getMockBuilder(RequestBodyValidatorInterface::class)->disableOriginalConstructor()->getMock();

        return new RequestBodyDecoderService($validator);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ModelFactoryInterface
     */
    private function getModelFactoryMock()
    {
        return $this->getMockBuilder(ModelFactoryInterface::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|FactoryService
     */
    private function getFactoryServiceMock()
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)->disableOriginalConstructor()->getMock();
        $container = $this->getMockBuilder(ServiceContainer::class)->disableOriginalConstructor()->getMock();
        $container->method('has')->willReturn(true);
        $container->method('get')->will($this->returnCallback(function (...$args) {
            switch ($args[0]) {
                case 'logger':
                    return $this->getMockBuilder(LoggerInterface::class)->disableOriginalConstructor()->getMock();
                    break;
            }

            return null;
        }));
        $factory = new FactoryService($container, $logger);

        return $factory;
    }
}
