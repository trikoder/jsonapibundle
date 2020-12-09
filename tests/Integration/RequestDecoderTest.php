<?php

namespace Trikoder\JsonApiBundle\Tests\Integration;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validation;
use Trikoder\JsonApiBundle\Config\ApiConfig;
use Trikoder\JsonApiBundle\Config\Config;
use Trikoder\JsonApiBundle\Config\CreateConfig;
use Trikoder\JsonApiBundle\Config\DeleteConfig;
use Trikoder\JsonApiBundle\Config\IndexConfig;
use Trikoder\JsonApiBundle\Config\UpdateConfig;
use Trikoder\JsonApiBundle\Config\UpdateRelationshipConfig;
use Trikoder\JsonApiBundle\Controller\JsonApiEnabledInterface;
use Trikoder\JsonApiBundle\Model\ModelFactoryInterface;
use Trikoder\JsonApiBundle\Services\Neomerx\FactoryService;
use Trikoder\JsonApiBundle\Services\Neomerx\ServiceContainer;
use Trikoder\JsonApiBundle\Services\RequestDecoder\RelationshipRequestBodyDecoder;
use Trikoder\JsonApiBundle\Services\RequestDecoder\RelationshipValidatorAdapter;
use Trikoder\JsonApiBundle\Services\RequestDecoder\RequestBodyDecoderService;
use Trikoder\JsonApiBundle\Services\RequestDecoder\RequestDecoder;
use Trikoder\JsonApiBundle\Services\RequestDecoder\SymfonyValidatorAdapter;

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
        $request->setMethod(Request::METHOD_POST);
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
        $request->setMethod(Request::METHOD_POST);

        $result = $requestDecoder->decode($request);
        $this->assertEquals([
            'name' => 'value',
        ], $result->request->all());
    }

    public function testJsonPostPayloadWithDefaultsAttributeAndInvalidStructure()
    {
        $this->expectException(BadRequestHttpException::class);
        $requestDecoder = $this->getRequestDecoder(null);

        $request = new Request([], [
            'data' => [
                'type' => 'test',
                'attributes' => [
                    'name' => 'value',
                ],
            ],
        ]);

        $request->setMethod(Request::METHOD_POST);
        $request->attributes->set('_jsonapibundle_relationship_endpoint', true);

        $requestDecoder->decode($request);
    }

    public function testJsonPostPayloadWithDefaultsAttributeAndValidStructure()
    {
        $requestDecoder = $this->getRequestDecoder(null);

        $request = new Request([], [
            'data' => [
                [
                    'type' => 'some type',
                    'id' => '2222',
                ],
                [
                    'type' => 'some type 3',
                    'id' => '2222',
                ],
                [
                    'type' => 'some x type',
                    'id' => '2222',
                ],
            ],
        ]);
        $request->setMethod(Request::METHOD_POST);
        $request->attributes->set('_jsonapibundle_relationship_endpoint', true);

        $result = $requestDecoder->decode($request);

        $this->assertEquals(
            [
                [
                    'type' => 'some type',
                    'id' => '2222',
                ],
                [
                    'type' => 'some type 3',
                    'id' => '2222',
                ],
                [
                    'type' => 'some x type',
                    'id' => '2222',
                ],
            ],
            $result->request->all());
    }

    public function testJsonPostPayloadWithDefaultsAttributeAndValidStructureButOnlyOneInvalidElement()
    {
        $this->expectException(BadRequestHttpException::class);
        $requestDecoder = $this->getRequestDecoder(null);

        $request = new Request([], [
            'data' => [
                [
                    'type' => 'some type',
                    'id' => '2222',
                ],
                [
                    'type' => 'some type 3',
                    'id' => '2222',
                ],
                [
                    'invalid_here' => 'some x type',
                    'id' => '2222',
                ],
            ],
        ]);
        $request->setMethod(Request::METHOD_POST);
        $request->attributes->set('_jsonapibundle_relationship_endpoint', true);

        $result = $requestDecoder->decode($request);

        $this->assertEquals([], $result->request->all());
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
        $request->setMethod(Request::METHOD_POST);

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

    /**
     * @dataProvider provideHttpVerbsThatShouldNotContainBody
     */
    public function testReturnsEmptyArrayForHttpVerbsThatShouldNotContainBody($method)
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
        $request->setMethod($method);

        $result = $requestDecoder->decode($request);
        $this->assertSame([], $result->request->all());
    }

    public function provideHttpVerbsThatShouldNotContainBody()
    {
        return [
            [Request::METHOD_GET],
            [Request::METHOD_DELETE],
        ];
    }

    private function getRequestDecoder($controller = null)
    {
        if (null === $controller) {
            // prepare mocked config and controller
            $controller = $this->getMockBuilder(JsonApiEnabledInterface::class)->disableOriginalConstructor()->getMock();
            $apiConfig = new Config(
                new ApiConfig(
                    "\stdClass",
                    null,
                    null,
                    null,
                    $this->getRequestBodyDecoder(),
                    false,
                    $this->getRequestBodyValidator(),
                    $this->getRelationshipsRequestBodyValidator(),
                    $this->getRelationshipRequestBodyDecoder()
                    ),
                new CreateConfig($this->getModelFactoryMock()),
                new IndexConfig(),
                new UpdateConfig(),
                new DeleteConfig(),
                new UpdateRelationshipConfig()
            );
            $controller->method('getJsonApiConfig')->willReturn($apiConfig);
        }

        return new RequestDecoder($this->getFactoryServiceMock(), $controller);
    }

    private function getRequestBodyDecoder()
    {
        return new RequestBodyDecoderService();
    }

    private function getRelationshipRequestBodyDecoder()
    {
        return new RelationshipRequestBodyDecoder();
    }

    private function getRequestBodyValidator()
    {
        return new SymfonyValidatorAdapter(Validation::createValidator());
    }

    private function getRelationshipsRequestBodyValidator()
    {
        return new RelationshipValidatorAdapter(Validation::createValidator());
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
        $container->method('get')->willReturnCallback(function (...$args) {
            switch ($args[0]) {
                case 'logger':
                    return $this->getMockBuilder(LoggerInterface::class)->disableOriginalConstructor()->getMock();
                    break;
            }

            return null;
        });
        $factory = new FactoryService($container, $logger);

        return $factory;
    }
}
