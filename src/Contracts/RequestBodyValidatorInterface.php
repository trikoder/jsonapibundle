<?php

declare(strict_types=1);

namespace Trikoder\JsonApiBundle\Contracts;

use Trikoder\JsonApiBundle\Services\RequestDecoder\Exception\InvalidBodyForMethodException;

interface RequestBodyValidatorInterface
{
    /**
     * @throws InvalidBodyForMethodException
     */
    public function validate(string $requestMethod, array $body);
}
