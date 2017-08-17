<?php

namespace Trikoder\JsonApiBundle\Contracts;

use Neomerx\JsonApi\Contracts\Document\ErrorInterface;
use Symfony\Component\HttpFoundation\Response;

interface ResponseFactoryInterface
{
    /**
     * @param string $data body of the response
     * @param Response|null $response any response that should be used as base
     * @return Response created response
     *
     * TODO - rename createResponse to create, response sufix is redundant as it is already response factory
     */
    public function createResponse(string $data, Response $response = null);

    /**
     * @param string $data
     * @param Response|null $response
     * @return Response
     */
    public function createConflict(string $data, Response $response = null);

    public function createCreated($data, $location, Response $response = null);

    public function createNoContent(Response $response = null);

    /**
     * @param string $data
     * @param Response|null $response
     * @return Response
     */
    public function createError(string $data, Response $response = null);
}