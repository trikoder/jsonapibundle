<?php

namespace Trikoder\JsonApiBundle\Services\RequestDecoder\Exception;

use Exception;
use Throwable;

class InvalidBodyForMethodException extends Exception
{
    /**
     * @var string
     */
    private $requestMethod;
    /**
     * @var array
     */
    private $requestBody;

    public function __construct(string $requestMethod, array $requestBody = [], Throwable $previous = null)
    {
        $message = sprintf('Passed body is not valid for request method %s', $requestMethod);
        parent::__construct($message, 0, $previous);

        $this->requestMethod = $requestMethod;
        $this->requestBody = $requestBody;
    }

    /**
     */
    public function getRequestMethod(): string
    {
        return $this->requestMethod;
    }

    /**
     */
    public function getRequestBody(): array
    {
        return $this->requestBody;
    }
}
