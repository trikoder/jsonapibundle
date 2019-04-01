<?php

declare(strict_types=1);

namespace Trikoder\JsonApiBundle\Services\RequestDecoder;

use Symfony\Component\HttpFoundation\Request;
use Trikoder\JsonApiBundle\Contracts\RequestBodyValidatorInterface;
use Trikoder\JsonApiBundle\Services\RequestDecoder\Exception\InvalidBodyForMethodException;

final class RequestBodyValidator implements RequestBodyValidatorInterface
{
    const METHODS_WITH_BODY = [
        Request::METHOD_PATCH,
        Request::METHOD_POST,
        Request::METHOD_PUT,
    ];

    /**
     * @throws InvalidBodyForMethodException
     */
    public function validate(string $requestMethod, array $body)
    {
        if (!\in_array($requestMethod, self::METHODS_WITH_BODY)) {
            return;
        }

        if (!array_key_exists('data', $body)) {
            throw new InvalidBodyForMethodException($requestMethod, $body);
        }

        if (null === $body['data']) {
            return;
        }

        if (!array_key_exists('type', $body['data'])) {
            throw new InvalidBodyForMethodException($requestMethod, $body);
        }
    }
}
