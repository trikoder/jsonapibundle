<?php

namespace Trikoder\JsonApiBundle\Tests\Unit;

use Exception;
use Neomerx\JsonApi\Contracts\Document\ErrorInterface;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Trikoder\JsonApiBundle\Contracts\ErrorFactoryInterface;
use Trikoder\JsonApiBundle\Services\JsonApiResponseLinter;
use Trikoder\JsonApiBundle\Services\Neomerx\EncoderService;
use Trikoder\JsonApiBundle\Services\ResponseFactoryService;
use Trikoder\JsonApiBundle\Services\ResponseLinterInterface;

/**
 */
final class ResponseFactoryServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     *
     */
    public function testcreateErrorFromException()
    {
        $responseFactoryService = new ResponseFactoryService(
            $this->createEncoderServiceMock(),
            $this->createErrorFactoryMock(),
            $this->createResponseLinterMock()
        );

        $response = $responseFactoryService->createErrorFromException(
            new Exception('Normal exception')
        );

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertSame(JsonApiResponseLinter::CONTENT_TYPE, $response->headers->get('Content-type'));
        $this->assertSame('Normal exception', $response->getContent());
    }

    /**
     *
     */
    public function testcreateErrorFromExceptionCustomResponse()
    {
        $responseFactoryService = new ResponseFactoryService(
            $this->createEncoderServiceMock(),
            $this->createErrorFactoryMock(),
            $this->createResponseLinterMock()
        );

        $customResponse = new Response();
        $customResponse->headers->set('X-Foo', 'Bar');

        $response = $responseFactoryService->createErrorFromException(
            new Exception('Normal exception'),
            $customResponse
        );

        $this->assertSame($customResponse, $response);
        $this->assertInstanceOf(Response::class, $customResponse);
        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $customResponse->getStatusCode());
        $this->assertSame(JsonApiResponseLinter::CONTENT_TYPE, $customResponse->headers->get('Content-type'));
        $this->assertSame('Normal exception', $customResponse->getContent());
        $this->assertSame('Bar', $customResponse->headers->get('X-Foo'));
    }

    /**
     *
     */
    public function testcreateErrorFromHttpException()
    {
        $responseFactoryService = new ResponseFactoryService(
            $this->createEncoderServiceMock(),
            $this->createErrorFactoryMock(),
            $this->createResponseLinterMock()
        );

        $response = $responseFactoryService->createErrorFromException(
            new UnauthorizedHttpException('Bearer', 'Access denied')
        );

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertSame(JsonApiResponseLinter::CONTENT_TYPE, $response->headers->get('Content-type'));
        $this->assertSame('Access denied', $response->getContent());
        $this->assertSame('Bearer', $response->headers->get('WWW-Authenticate'));
    }

    /**
     *
     */
    public function testcreateErrorFromHttpExceptionCustomResponse()
    {
        $responseFactoryService = new ResponseFactoryService(
            $this->createEncoderServiceMock(),
            $this->createErrorFactoryMock(),
            $this->createResponseLinterMock()
        );

        $customResponse = new Response();
        $customResponse->headers->set('X-Foo', 'Bar');

        $response = $responseFactoryService->createErrorFromException(
            new UnauthorizedHttpException('Bearer', 'Access denied'),
            $customResponse
        );

        $this->assertSame($customResponse, $response);
        $this->assertInstanceOf(Response::class, $customResponse);
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $customResponse->getStatusCode());
        $this->assertSame(JsonApiResponseLinter::CONTENT_TYPE, $customResponse->headers->get('Content-type'));
        $this->assertSame('Access denied', $customResponse->getContent());
        $this->assertSame('Bearer', $customResponse->headers->get('WWW-Authenticate'));
        $this->assertSame('Bar', $customResponse->headers->get('X-Foo'));
    }

    /**
     * @return EncoderService
     */
    private function createEncoderServiceMock()
    {
        $encoderService = $this
            ->getMockBuilder(EncoderService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $encoderService
            ->method('encodeErrors')
            ->with(
                $this->callback(function (array $errors) {
                    return 'error' === reset($errors)->getId();
                })
            )
            ->willReturnCallback(
                function (array $errors) {
                    return reset($errors)->getTitle();
                }
            );

        return $encoderService;
    }

    /**
     * @return ErrorFactoryInterface
     */
    private function createErrorFactoryMock()
    {
        $errorFactory = $this->createMock(ErrorFactoryInterface::class);

        $errorFactory
            ->method('fromException')
            ->with(
                $this->callback(function ($exception) {
                    return $exception instanceof Exception;
                })
            )
            ->willReturnCallback(
                function (Exception $exception) {
                    $error = $this->createMock(ErrorInterface::class);

                    $error
                        ->method('getId')
                        ->willReturn('error');

                    $error
                        ->method('getTitle')
                        ->willReturn($exception->getMessage());

                    return $error;
                }
            );

        return $errorFactory;
    }

    /**
     * @return ResponseLinterInterface
     */
    private function createResponseLinterMock()
    {
        $service = new JsonApiResponseLinter();

        return $service;
    }

    /**
     *
     */
    public function testCreateCreated()
    {
        $responseFactoryService = new ResponseFactoryService(
            $this->createEncoderServiceMock(),
            $this->createErrorFactoryMock(),
            $this->createResponseLinterMock()
        );

        $response = $responseFactoryService->createCreated(
            json_encode(['data' => []]),
            'custom.url/test'
        );

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertSame(JsonApiResponseLinter::CONTENT_TYPE, $response->headers->get('Content-type'));
        $this->assertSame('custom.url/test', $response->headers->get('location'));
    }
}
