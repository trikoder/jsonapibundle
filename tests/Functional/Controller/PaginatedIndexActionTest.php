<?php

namespace Trikoder\JsonApiBundle\Tests\Functional\Controller;

use Symfony\Component\HttpFoundation\Response;
use Trikoder\JsonApiBundle\Tests\Functional\JsonapiWebTestCase;

/**
 * Class PaginatedIndexActionTest
 * @package Trikoder\JsonApiBundle\Tests\Functional\Controller
 */
class PaginatedIndexActionTest extends JsonapiWebTestCase
{
    /**
     * test simple listing
     */
    public function testUserIndexAction()
    {
        $client = static::createClient();

        $client->request('GET', '/api/user-paginated/');

        $response = $client->getResponse();

        $this->assertIsJsonapiResponse($response);

        $content = $this->getResponseContentJson($response);
        $this->assertCount(2, $content['data']);

        $this->assertArrayHasKey('meta', $content);
        $this->assertEquals([
            'total' => 5
        ], $content['meta']);

        $this->assertArrayHasKey('links', $content);
        $this->assertArrayHasKey('self', $content['links']);
        $this->assertArrayHasKey('prev', $content['links']);
        $this->assertArrayHasKey('next', $content['links']);
        $this->assertArrayHasKey('first', $content['links']);
        $this->assertArrayHasKey('last', $content['links']);
    }

    public function testActionWithoutTrailingSlash()
    {
        $client = static::createClient();
        $client->request('GET', '/api/user-paginated');
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertIsJsonapiResponse($response);
    }
}
