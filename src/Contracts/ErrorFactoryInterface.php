<?php

namespace Trikoder\JsonApiBundle\Contracts;

use Exception;
use Neomerx\JsonApi\Contracts\Document\ErrorInterface;

/**
 * Interface ErrorFactoryInterface
 */
interface ErrorFactoryInterface
{
    /**
     *
     */
    public function fromString(string $error): ErrorInterface;

    /**
     *
     */
    public function fromException(Exception $exception): ErrorInterface;
}
