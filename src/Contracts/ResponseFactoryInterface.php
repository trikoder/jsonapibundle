<?php

namespace Trikoder\JsonApiBundle\Contracts;

use Exception;
use Symfony\Component\HttpFoundation\Response;

interface ResponseFactoryInterface
{
    /**
     * @param string $data body of the response
     * @param Response $response any response that should be used as base
     *
     * @return Response created response
     */
    public function createResponse(string $data, Response $response = null): Response;

    /**
     * @param Response $response
     */
    public function createConflict(string $data, Response $response = null): Response;

    /**
     * @param string $location
     * @param Response $response
     */
    public function createCreated(string $data, string $location = null, Response $response = null): Response;

    /**
     *
     */
    public function createNoContent(Response $response = null): Response;

    /**
     * @param Response $response
     */
    public function createError(string $data, Response $response = null): Response;

    /**
     * @param Response $response
     */
    public function createErrorFromException(Exception $exception, Response $response = null): Response;

    /**
     * @param Response $response
     */
    public function createBadRequest(string $data, Response $response = null): Response;
}
