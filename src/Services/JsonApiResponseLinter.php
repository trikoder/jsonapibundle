<?php

namespace Trikoder\JsonApiBundle\Services;

use Symfony\Component\HttpFoundation\Response;

final class JsonApiResponseLinter implements ResponseLinterInterface
{
    const CONTENT_TYPE = 'application/vnd.api+json';

    public function lint(Response $response): Response
    {
        $response->headers->set('Content-type', self::CONTENT_TYPE);

        return $response;
    }
}
