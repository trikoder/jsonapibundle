<?php

namespace Trikoder\JsonApiBundle\Services\Neomerx;


use Exception;
use Neomerx\JsonApi\Contracts\Document\ErrorInterface;
use Neomerx\JsonApi\Document\Error;
use Symfony\Component\HttpFoundation\Response;
use Trikoder\JsonApiBundle\Contracts\ErrorFactoryInterface;

class ErrorFactory implements ErrorFactoryInterface
{
    /**
     * @inheritdoc
     */
    public function fromString(string $error): ErrorInterface
    {
        return new Error(null, null, null, null, $error);
    }

    /**
     * @inheritdoc
     */
    public function fromException(Exception $exception): ErrorInterface
    {
        return new Error(
            null,
            null,
            Response::HTTP_INTERNAL_SERVER_ERROR,
            $exception->getCode(),
            get_class($exception),
            $exception->getMessage()
        );
    }
}