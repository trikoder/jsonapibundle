<?php

namespace Trikoder\JsonApiBundle\Services;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Trikoder\JsonApiBundle\Contracts\ErrorFactoryInterface;
use Trikoder\JsonApiBundle\Contracts\ResponseFactoryInterface;
use Trikoder\JsonApiBundle\Services\Neomerx\EncoderService;

class ResponseFactoryService implements ResponseFactoryInterface
{
    /**
     * @var EncoderService
     */
    private $encoderService;
    /**
     * @var ErrorFactoryInterface
     */
    private $errorFactory;

    /**
     * ResponseFactoryService constructor.
     *
     * @param EncoderService $encoderService
     * @param ErrorFactoryInterface $errorFactory
     */
    public function __construct(EncoderService $encoderService, ErrorFactoryInterface $errorFactory)
    {
        $this->encoderService = $encoderService;
        $this->errorFactory = $errorFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createResponse(string $data, Response $response = null)
    {
        if (null === $response) {
            $response = new Response(); // TODO move to JsonResponse
        }

        // set content
        $response->setContent($data);

        // set proper headers
        // TODO - this should be MediaTypeInterface::JSON_API_MEDIA_TYPE but nobody understands this :(
        $response->headers->set('Content-type', 'application/json');

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function createConflict(string $data, Response $response = null)
    {
        if (null === $response) {
            $response = new Response(); // TODO move to JsonResponse
        }

        $response->setStatusCode(Response::HTTP_CONFLICT);

        return $this->createResponse($data, $response);
    }

    /**
     * {@inheritdoc}
     */
    public function createCreated($data, $location, Response $response = null)
    {
        if (null === $response) {
            $response = new Response(); // TODO move to JsonResponse
        }

        $response->setStatusCode(Response::HTTP_CREATED);
        $response->headers->add(['Location', $location]);

        return $this->createResponse($data, $response);
    }

    /**
     * {@inheritdoc}
     */
    public function createNoContent(Response $response = null)
    {
        if (null === $response) {
            $response = new Response(); // TODO move to JsonResponse
        }
        $response->setStatusCode(Response::HTTP_NO_CONTENT);

        return $this->createResponse('', $response);
    }

    /**
     * {@inheritdoc}
     */
    public function createError(string $data, Response $response = null)
    {
        if (null === $response) {
            $response = new Response(); // TODO move to JsonResponse
        }

        $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);

        return $this->createResponse($data, $response);
    }

    /**
     * {@inheritdoc}
     */
    public function createErrorFromException(Exception $exception, Response $response = null)
    {
        $error = $this->errorFactory->fromException($exception);
        $encoded = $this->encoderService->encodeErrors([$error]);

        $response = $this->createError($encoded, $response);

        if ($exception instanceof HttpExceptionInterface) {
            $response->headers->add($exception->getHeaders());
            $response->setStatusCode($exception->getStatusCode());
        }

        return $response;
    }

    // TODO - implement shorthands for frequenty used responses , eg ok, notFound, noContent, created, etc
}
