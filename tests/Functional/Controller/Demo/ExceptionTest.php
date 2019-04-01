<?php

namespace Trikoder\JsonApiBundle\Tests\Functional\Controller\Demo;

use Symfony\Component\HttpFoundation\Response;
use Trikoder\JsonApiBundle\Tests\Functional\JsonapiWebTestCase;

class ExceptionTest extends JsonapiWebTestCase
{
    /**
     * test exception response
     */
    public function testException()
    {
        $client = static::createClient();

        $client->request(
            'GET',
            '/api/exception'
        );

        $response = $client->getResponse();
        $this->assertIsJsonapiResponse($response);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());

        $content = $this->getResponseContentJson($response);
        $this->assertArrayHasKey('errors', $content);
        $this->assertCount(1, $content['errors']);
        $this->assertEquals([
            'status' => '500',
            'code' => '44',
            'title' => 'Test exception',
            'detail' => 'Exception of type: Exception',
        ], $content['errors'][0]);
    }
}
