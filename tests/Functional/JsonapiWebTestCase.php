<?php

namespace Trikoder\JsonApiBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

abstract class JsonapiWebTestCase extends WebTestCase
{
    protected function assertIsJsonapiResponse(Response $response)
    {


        // if non empty content, it must be json
        if ($response->getStatusCode() != Response::HTTP_NO_CONTENT) {
            $contentJson = json_decode($response->getContent(), true);
            $this->assertNotEmpty($response->getContent(), sprintf('Response is empty (only HTTP_NO_CONTENT is allowed to be empty, response code was %s)', $response->getStatusCode()));
            $this->assertNotNull($contentJson, sprintf('Response is not json (only HTTP_NO_CONTENT is allowed to be empty, response code was %s)', $response->getStatusCode()));

            // TODO - add additional checks here (mandatory keys etc ...)
        }

    }

    protected function getResponseContentJson(Response $response)
    {
        return json_decode($response->getContent(), true);
    }
}