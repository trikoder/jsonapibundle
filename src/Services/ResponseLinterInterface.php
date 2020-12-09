<?php

namespace Trikoder\JsonApiBundle\Services;

use Symfony\Component\HttpFoundation\Response;

interface ResponseLinterInterface
{
    public function lint(Response $response): Response;
}
