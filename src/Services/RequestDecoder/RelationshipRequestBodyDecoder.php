<?php

namespace Trikoder\JsonApiBundle\Services\RequestDecoder;

use Trikoder\JsonApiBundle\Contracts\RequestBodyDecoderInterface;

final class RelationshipRequestBodyDecoder implements RequestBodyDecoderInterface
{
    /**
     * Takes array representation of jsonapi body payload and returnes flat array as would be expected by simple POST
     *
     * @return array
     */
    public function decode(string $requestMethod, array $body = [])
    {
        $decoded = [];

        if (!\array_key_exists('data', $body) || null === $body['data']) {
            return $decoded;
        }

        return $body['data'];
    }
}
