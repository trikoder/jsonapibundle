<?php
namespace Trikoder\JsonApiBundle\Tests\Unit;

use Exception;
use Neomerx\JsonApi\Contracts\Document\ErrorInterface;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Trikoder\JsonApiBundle\Contracts\ErrorFactoryInterface;
use Trikoder\JsonApiBundle\Services\Neomerx\EncoderService;
use Trikoder\JsonApiBundle\Services\ResponseFactoryService;

/**
 * @package Trikoder\JsonApiBundle\Tests\Unit
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
            $this->createErrorFactoryMock()
        );

        $response = $responseFactoryService->createErrorFromException(
            new Exception('Normal exception')
        );

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('Content-type'));
        $this->assertSame('Normal exception', $response->getContent());
    }

    /**
     *
     */
    public function testcreateErrorFromExceptionCustomResponse()
    {
        $responseFactoryService = new ResponseFactoryService(
            $this->createEncoderServiceMock(),
            $this->createErrorFactoryMock()
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
        $this->assertSame('application/json', $customResponse->headers->get('Content-type'));
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
            $this->createErrorFactoryMock()
        );

        $response = $responseFactoryService->createErrorFromException(
            new UnauthorizedHttpException('Bearer', 'Access denied')
        );

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('Content-type'));
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
            $this->createErrorFactoryMock()
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
        $this->assertSame('application/json', $customResponse->headers->get('Content-type'));
        $this->assertSame('Access denied', $customResponse->getContent());
        $this->assertSame('Bearer', $customResponse->headers->get('WWW-Authenticate'));
        $this->assertSame('Bar', $customResponse->headers->get('X-Foo'));
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject
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
                    return reset($errors)->getId() === 'error';
                })
            )
            ->will(
                $this->returnCallback(function (array $errors) {
                    return reset($errors)->getTitle();
                })
            );

        return $encoderService;
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function createErrorFactoryMock()
    {
        $errorFactory = $this->getMock(ErrorFactoryInterface::class);

        $errorFactory
            ->method('fromException')
            ->with(
                $this->callback(function ($exception) {
                    return $exception instanceof Exception;
                })
            )
            ->will(
                $this->returnCallback(function (Exception $exception) {
                    $error = $this->getMock(ErrorInterface::class);

                    $error
                        ->method('getId')
                        ->willReturn('error');

                    $error
                        ->method('getTitle')
                        ->willReturn($exception->getMessage());

                    return $error;
                })
            );

        return $errorFactory;
    }
}
