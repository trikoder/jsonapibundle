<?php

namespace Trikoder\JsonApiBundle\Services\ModelInput;

use Exception;
use Throwable;

class UnhandleableModelInputException extends Exception
{
    /**
     * @var string
     */
    private $inputValueKeys;

    public function __construct(array $invalidInputKeys, Throwable $previous = null)
    {
        parent::__construct(sprintf('Input for a attribute(s) %s cannot be handeled (invalid value type or constraint violation).', implode(', ', $invalidInputKeys)), 0, $previous);
    }

    /**
     */
    public function getInputValueKeys(): string
    {
        return $this->inputValueKeys;
    }
}
