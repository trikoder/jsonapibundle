<?php

namespace Trikoder\JsonApiBundle\Tests\Functional\Controller;

use Trikoder\JsonApiBundle\Tests\Functional\JsonapiWebTestCase;
use Trikoder\JsonApiBundle\Tests\Resources\Entity\User;

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
            'total' => 5,
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
}
