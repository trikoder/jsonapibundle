<?php

namespace Trikoder\JsonApiBundle\Tests\Functional\Controller;

use Trikoder\JsonApiBundle\Tests\Functional\JsonapiWebTestCase;

class CustomResponseActionTest extends JsonapiWebTestCase
{
    /**
     * Test simple show action
     */
    public function testFromArrayAction()
    {
        $client = static::createClient();

        $client->request('GET', '/api/custom-response/from-array');

        $response = $client->getResponse();

        $this->assertIsJsonapiResponse($response);

        $data = $this->getResponseContentJson($response);

        $this->assertEquals([
            'type' => 'object',
            'attributes' => [
                'attributeX' => 'valueY',
            ],
            'links' => [
                'self' => '/object/', // TODO - this should same url sa request?
            ],
        ], $data['data']);
    }
}
