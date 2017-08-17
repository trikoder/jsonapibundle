<?php

namespace Trikoder\JsonApiBundle\Services;

use Neomerx\JsonApi\Contracts\Document\ErrorInterface;
use Symfony\Component\HttpFoundation\Response;
use Trikoder\JsonApiBundle\Contracts\ResponseFactoryInterface;

class ResponseFactoryService implements ResponseFactoryInterface
{
    /**
     * @inheritdoc
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
     * @inheritdoc
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
     * @inheritdoc
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
     * @inheritdoc
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
     * @inheritdoc
     */
    public function createError(string $data, Response $response = null)
    {
        if (null === $response) {
            $response = new Response(); // TODO move to JsonResponse
        }

        $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);

        return $this->createResponse($data, $response);
    }

    // TODO - implement shorthands for frequenty used responses , eg ok, notFound, noContent, created, etc
}
