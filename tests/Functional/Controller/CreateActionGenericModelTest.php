<?php

declare(strict_types=1);

namespace Trikoder\JsonApiBundle\Tests\Functional\Controller;

use Symfony\Component\HttpFoundation\Response;
use Trikoder\JsonApiBundle\Tests\Functional\JsonapiWebTestCase;

final class CreateActionGenericModelTest extends JsonapiWebTestCase
{
    /**
     * test simple create
     */
    public function testGenericCreateAction()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/generic/',
            [],
            [],
            [],
            json_encode([
                'data' => [
                    'type' => 'generic-model',
                    'attributes' => [
                        'title' => 'Title',
                        'isActive' => true,
                    ],
                ],
            ])
        );

        $response = $client->getResponse();

        $this->assertIsJsonapiResponse($response);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $data = $this->getResponseContentJson($response);

        $this->assertEquals([
            'type' => 'generic-model',
            'attributes' => [
                'title' => 'Title',
                'isActive' => true,
            ],
            'links' => [
                'self' => '/generic-model/',
            ],
        ], $data['data']);
    }
}
