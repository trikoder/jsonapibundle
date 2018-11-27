<?php

namespace Trikoder\JsonApiBundle\Schema\Autowire\Exception;

use Exception;
use Throwable;

class UnresolvedDependencyException extends Exception
{
    public function __construct(string $argumentIndex, string $schemaClass, string $classHint, Throwable $previous = null)
    {
        $message = sprintf('Cannot resolve argument %s for schema %s with hint %s. Did you forget to register service or alias?', $argumentIndex, $schemaClass, $classHint);
        parent::__construct($message, 0, $previous);
    }
}
