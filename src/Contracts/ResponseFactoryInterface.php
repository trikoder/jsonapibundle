<?php

namespace Trikoder\JsonApiBundle\Contracts;

use Exception;
use Symfony\Component\HttpFoundation\Response;

interface ResponseFactoryInterface
{
    /**
     * @param string $data body of the response
     * @param Response|null $response any response that should be used as base
     *
     * @return Response created response
     */
    public function createResponse(string $data, Response $response = null);

    /**
     * @param string $data
     * @param Response|null $response
     *
     * @return Response
     */
    public function createConflict(string $data, Response $response = null);

    public function createCreated($data, $location, Response $response = null);

    public function createNoContent(Response $response = null);

    /**
     * @param string $data
     * @param Response|null $response
     *
     * @return Response
     */
    public function createError(string $data, Response $response = null);

    /**
     * @param Exception $exception
     * @param Response|null $response
     *
     * @return Response
     */
    public function createErrorFromException(Exception $exception, Response $response = null);
}
