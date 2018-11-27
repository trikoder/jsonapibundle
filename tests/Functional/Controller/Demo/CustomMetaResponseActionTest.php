<?php

namespace Trikoder\JsonApiBundle\Tests\Functional\Controller;

use Trikoder\JsonApiBundle\Tests\Functional\JsonapiWebTestCase;

class CustomMetaResponseActionTest extends JsonapiWebTestCase
{
    /**
     * Test simple show action
     */
    public function testIndexAction()
    {
        $client = static::createClient();

        $client->request('GET', '/api/custom-meta-response');

        $response = $client->getResponse();

        $this->assertIsJsonapiResponse($response);

        $data = $this->getResponseContentJson($response);

        $this->assertEquals([
            'customInfo' => 'valid',
            // TODO refactor this to count from database
            'total' => 6,
        ], $data['meta']);

        $this->assertNotEmpty($data['data']);
    }

    /**
     * Test simple show action
     */
    public function testEmptyAction()
    {
        $client = static::createClient();

        $client->request('GET', '/api/custom-meta-response/empty');

        $response = $client->getResponse();

        $data = $this->getResponseContentJson($response);

        $this->assertEquals([
            'customInfo' => 'valid',
        ], $data['meta']);

        $this->assertNull($data['data']);
    }

    /**
     * Test simple show action
     */
    public function testEmptyAllAction()
    {
        $client = static::createClient();

        $client->request('GET', '/api/custom-meta-response/empty-all');

        $response = $client->getResponse();

        $data = $this->getResponseContentJson($response);

        $this->assertArrayNotHasKey('meta', $data);

        $this->assertNull($data['data']);
    }
}
