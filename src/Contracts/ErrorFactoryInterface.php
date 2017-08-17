<?php

namespace Trikoder\JsonApiBundle\Contracts;

use Exception;
use Neomerx\JsonApi\Contracts\Document\ErrorInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;

/**
 * Interface ErrorFactoryInterface
 * @package Trikoder\JsonApiBundle\Contracts
 */
interface ErrorFactoryInterface
{
    /**
     * @param string $error
     * @return ErrorInterface
     */
    public function fromString(string $error): ErrorInterface;

    /**
     * @param Exception $exception
     * @return ErrorInterface
     */
    public function fromException(Exception $exception): ErrorInterface;
}