<?php

namespace Trikoder\JsonApiBundle\Contracts;

use Trikoder\JsonApiBundle\Services\RequestDecoder\Exception\InvalidBodyForMethodException;

interface RequestBodyDecoderInterface
{
    /**
     * @return array
     *
     * @throws InvalidBodyForMethodException
     */
    public function decode(string $requestMethod, array $body = []);
}
