<?php

namespace Trikoder\JsonApiBundle\Tests\Unit\Services;

use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\ConstraintViolationList;
use Trikoder\JsonApiBundle\Config\ApiConfig;
use Trikoder\JsonApiBundle\Config\Config;
use Trikoder\JsonApiBundle\Config\CreateConfig;
use Trikoder\JsonApiBundle\Config\DeleteConfig;
use Trikoder\JsonApiBundle\Config\IndexConfig;
use Trikoder\JsonApiBundle\Config\UpdateConfig;
use Trikoder\JsonApiBundle\Config\UpdateRelationshipConfig;
use Trikoder\JsonApiBundle\Contracts\RequestBodyDecoderInterface;
use Trikoder\JsonApiBundle\Contracts\RequestBodyValidatorInterface;
use Trikoder\JsonApiBundle\Controller\JsonApiEnabledInterface;
use Trikoder\JsonApiBundle\Model\ModelFactoryInterface;
use Trikoder\JsonApiBundle\Services\Neomerx\FactoryService;
use Trikoder\JsonApiBundle\Services\Neomerx\ServiceContainer;
use Trikoder\JsonApiBundle\Services\RequestDecoder\RequestDecoder;

/**
 * Class RequestDecoderTest
 */
class RequestDecoderTest extends \PHPUnit_Framework_TestCase
{
    public function testMultipleValueFilter()
    {
        // prepare mocked config and controller
        $controller = $this->getMockBuilder(JsonApiEnabledInterface::class)->disableOriginalConstructor()->getMock();
        $requestBodyValidator = $this->getRequestBodyValidatorMock();
        $requestBodyValidator->method('validate')->willReturn(new ConstraintViolationList());
        $relationshipBodyValidator = $this->getRelationshipRequestBodyValidatorMock();
        $relationshipBodyDecoder = $this->getRelationshipRequestBodyDecoderMock();
        $apiConfig = new Config(
            new ApiConfig(
                "\stdClass",
                null,
                null,
                null, $this->getRequestBodyDecoderMock(),
                false,
                $requestBodyValidator,
                $relationshipBodyValidator->reveal(),
                $relationshipBodyDecoder->reveal()
            ),
            new CreateConfig($this->getModelFactoryMock()),
            new IndexConfig(),
            new UpdateConfig(),
            new DeleteConfig(),
            new UpdateRelationshipConfig()
        );
        $controller->method('getJsonApiConfig')->willReturn($apiConfig);

        $requestDecoder = $this->getRequestDecoder($controller);

        // test single value
        $request = new Request(['filter' => ['myfield' => 'value12']]);
        $result = $requestDecoder->decode($request);
        $this->assertEquals([
            'myfield' => 'value12',
        ], $result->query->get('filter'));

        // test simple case
        $request = new Request(['filter' => ['myfield' => 'value1,value2']]);
        $result = $requestDecoder->decode($request);
        $this->assertEquals([
            'myfield' => [
                'value1',
                'value2',
            ],
        ], $result->query->get('filter'));
    }

    public function testMultipleValueFields()
    {
        // prepare mocked config and controller
        $controller = $this->getMockBuilder(JsonApiEnabledInterface::class)->disableOriginalConstructor()->getMock();
        $requestBodyValidator = $this->getRequestBodyValidatorMock();
        $requestBodyValidator->method('validate')->willReturn(new ConstraintViolationList());
        $relationshipBodyValidator = $this->getRelationshipRequestBodyValidatorMock();
        $relationshipBodyDecoder = $this->getRelationshipRequestBodyDecoderMock();

        $apiConfig = new Config(
            new ApiConfig(
                "\stdClass",
                null,
                null,
                null, $this->getRequestBodyDecoderMock(),
                false,
                $requestBodyValidator,
                $relationshipBodyValidator->reveal(),
                $relationshipBodyDecoder->reveal()
            ),
            new CreateConfig($this->getModelFactoryMock()),
            new IndexConfig(),
            new UpdateConfig(),
            new DeleteConfig(),
            new UpdateRelationshipConfig()
        );
        $controller->method('getJsonApiConfig')->willReturn($apiConfig);

        $requestDecoder = $this->getRequestDecoder($controller);

        // test single value
        $request = new Request(['fields' => ['myresource' => 'field']]);
        $result = $requestDecoder->decode($request);
        $this->assertEquals([
            'myresource' => ['field'],
        ], $result->query->get('fields'));

        // test simple case
        $request = new Request(['fields' => ['myresource' => 'field1,field2']]);
        $result = $requestDecoder->decode($request);
        $this->assertEquals([
            'myresource' => [
                'field1',
                'field2',
            ],
        ], $result->query->get('fields'));
    }

    public function testLimitedFields()
    {
        // prepare mocked config and controller
        $controller = $this->getMockBuilder(JsonApiEnabledInterface::class)->disableOriginalConstructor()->getMock();
        $requestBodyValidator = $this->getRequestBodyValidatorMock();
        $requestBodyValidator->method('validate')->willReturn(new ConstraintViolationList());
        $relationshipBodyValidator = $this->getRelationshipRequestBodyValidatorMock();
        $relationshipBodyDecoder = $this->getRelationshipRequestBodyDecoderMock();

        $apiConfig = new Config(
            new ApiConfig(
                "\stdClass",
                null,
                null,
                null, $this->getRequestBodyDecoderMock(),
                false,
                $requestBodyValidator,
                $relationshipBodyValidator->reveal(),
                $relationshipBodyDecoder->reveal()
            ),
            new CreateConfig($this->getModelFactoryMock()),
            new IndexConfig(null, ['allowed']),
            new UpdateConfig(),
            new DeleteConfig(),
            new UpdateRelationshipConfig()
        );
        $controller->method('getJsonApiConfig')->willReturn($apiConfig);

        $requestDecoder = $this->getRequestDecoder($controller);

        // test single value
        $requestParameters = [];
        parse_str('filter[allowed]=yes', $requestParameters);
        $request = new Request($requestParameters);
        $result = $requestDecoder->decode($request);
        $this->assertEquals([
            'allowed' => 'yes',
        ], $result->query->get('filter'));

        // test invalid because there is limitation to fields
        $requestParameters = [];
        parse_str('filter[forbidden]=no', $requestParameters);
        $request = new Request($requestParameters);
        // TODO expect exception
        $this->expectException(BadRequestHttpException::class);
        $requestDecoder->decode($request);
    }

    public function testMultipleLevelFilterWithEmptyConfig()
    {
        // prepare mocked config and controller
        $controller = $this->getMockBuilder(JsonApiEnabledInterface::class)->disableOriginalConstructor()->getMock();
        $requestBodyValidator = $this->getRequestBodyValidatorMock();
        $requestBodyValidator->method('validate')->willReturn(new ConstraintViolationList());
        $relationshipBodyValidator = $this->getRelationshipRequestBodyValidatorMock();
        $relationshipBodyDecoder = $this->getRelationshipRequestBodyDecoderMock();

        $apiConfig = new Config(
            new ApiConfig(
                "\stdClass",
                null,
                null,
                null, $this->getRequestBodyDecoderMock(),
                false,
                $requestBodyValidator,
                $relationshipBodyValidator->reveal(),
                $relationshipBodyDecoder->reveal()
            ),
            new CreateConfig($this->getModelFactoryMock()),
            new IndexConfig(),
            new UpdateConfig(),
            new DeleteConfig(),
            new UpdateRelationshipConfig()
        );
        $controller->method('getJsonApiConfig')->willReturn($apiConfig);

        $requestDecoder = $this->getRequestDecoder($controller);

        // test single value
        $requestParameters = [];
        parse_str('filter[image][variation]=12', $requestParameters);
        $request = new Request($requestParameters);
        $result = $requestDecoder->decode($request);
        $this->assertEquals([
            'image' => [
                'variation' => 12,
            ],
        ], $result->query->get('filter'));

        // test simple case
        $requestParameters = [];
        parse_str('filter[image][variation]=12,13&filter[image][gallery]=13&filter[active]=1', $requestParameters);
        $request = new Request($requestParameters);
        $result = $requestDecoder->decode($request);
        $this->assertEquals([
            'image' => [
                'variation' => [12, 13],
                'gallery' => 13,
            ],
            'active' => 1,
        ], $result->query->get('filter'));
    }

    public function testMultipleLevelFilterWithLimitedFields()
    {
        // prepare mocked config and controller
        $controller = $this->getMockBuilder(JsonApiEnabledInterface::class)->disableOriginalConstructor()->getMock();
        $requestBodyValidator = $this->getRequestBodyValidatorMock();
        $requestBodyValidator->method('validate')->willReturn(new ConstraintViolationList());
        $relationshipBodyValidator = $this->getRelationshipRequestBodyValidatorMock();
        $relationshipBodyDecoder = $this->getRelationshipRequestBodyDecoderMock();

        $apiConfig = new Config(
            new ApiConfig(
                "\stdClass",
                null,
                null,
                null, $this->getRequestBodyDecoderMock(),
                false,
                $requestBodyValidator,
                $relationshipBodyValidator->reveal(),
                $relationshipBodyDecoder->reveal()
            ),
            new CreateConfig($this->getModelFactoryMock()),
            /* TODO limited fields options is currently limited to root level, should add support for sub level i.e. image.gallery */
            new IndexConfig(null, ['image', 'active']),
            new UpdateConfig(),
            new DeleteConfig(),
            new UpdateRelationshipConfig()
        );
        $controller->method('getJsonApiConfig')->willReturn($apiConfig);

        $requestDecoder = $this->getRequestDecoder($controller);

        // test single value
        $requestParameters = [];
        parse_str('filter[image][gallery]=12', $requestParameters);
        $request = new Request($requestParameters);
        $result = $requestDecoder->decode($request);
        $this->assertEquals([
            'image' => [
                'gallery' => 12,
            ],
        ], $result->query->get('filter'));

        // test invalid because there is limitation to fields
        $requestParameters = [];
        parse_str('filter[image][variation]=12,13&filter[photo][gallery]=13&filter[active]=1', $requestParameters);
        $request = new Request($requestParameters);
        // TODO expect exception
        $this->expectException(BadRequestHttpException::class);
        $requestDecoder->decode($request);
    }

    public function testPerservationOfProperties()
    {
        // prepare mocked config and controller
        $controller = $this->getMockBuilder(JsonApiEnabledInterface::class)->disableOriginalConstructor()->getMock();
        $requestBodyValidator = $this->getRequestBodyValidatorMock();
        $requestBodyValidator->method('validate')->willReturn(new ConstraintViolationList());
        $relationshipBodyValidator = $this->getRelationshipRequestBodyValidatorMock();
        $relationshipBodyDecoder = $this->getRelationshipRequestBodyDecoderMock();
        $apiConfig = new Config(
            new ApiConfig(
                "\stdClass",
                null,
                null,
                null, $this->getRequestBodyDecoderMock(),
                false,
                $requestBodyValidator,
                $relationshipBodyValidator->reveal(),
                $relationshipBodyDecoder->reveal()
            ),
            new CreateConfig($this->getModelFactoryMock()),
            new IndexConfig(),
            new UpdateConfig(),
            new DeleteConfig(),
            new UpdateRelationshipConfig()
        );
        $controller->method('getJsonApiConfig')->willReturn($apiConfig);

        $mockedSession = $this->getMockBuilder(SessionInterface::class)->getMock();
        $mockedSession->method('getId')->willReturn('mysession');

        $requestDecoder = $this->getRequestDecoder($controller);

        // test special properties
        $request = new Request();
        $request->setDefaultLocale('defaultlocale');
        $request->setLocale('locale');
        $request->setSession($mockedSession);
        $result = $requestDecoder->decode($request);

        $this->assertSame($request->getDefaultLocale(), $result->getDefaultLocale());
        $this->assertSame($request->getLocale(), $result->getLocale());
        $this->assertSame($request->getSession()->getId(), $result->getSession()->getId());
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

    private function getRequestBodyValidatorMock()
    {
        return $this->getMockBuilder(RequestBodyValidatorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getRelationshipRequestBodyValidatorMock()
    {
        return $this->prophesize(RequestBodyValidatorInterface::class);
    }

    private function getRelationshipRequestBodyDecoderMock()
    {
        return $this->prophesize(RequestBodyDecoderInterface::class);
    }

    /**
     * @return RequestDecoder
     */
    private function getRequestDecoder(JsonApiEnabledInterface $controller)
    {
        return new RequestDecoder($this->getFactoryServiceMock(), $controller);
    }
}
