<?php

namespace Trikoder\JsonApiBundle\Tests\Functional\Controller;

use Symfony\Component\HttpFoundation\Response;
use Trikoder\JsonApiBundle\Tests\Functional\JsonapiWebTestCase;
use Trikoder\JsonApiBundle\Tests\Resources\Entity\User;

class UpdateActionTest extends JsonapiWebTestCase
{
    /**
     * test simple update
     */
    public function testUserUpdateAction()
    {
        $client = static::createClient();

        // load user
        /** @var User $user */
        $user = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class)->find(3);
        $this->assertNotEquals($user->getEmail(), 'myupdatetest@domain.com');

        $client->request(
            'PUT',
            '/api/user/3',
            [],
            [],
            [],
            json_encode([
                'data' => [
                    'type' => 'user',
                    'id' => 3,
                    'attributes' => [
                        'email' => 'myupdatetest@domain.com',
                        'active' => true,
                    ],
                ],
            ])
        );

        $response = $client->getResponse();

        $this->assertIsJsonapiResponse($response);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $data = $this->getResponseContentJson($response);

        $this->assertEquals([
            'type' => 'user',
            'id' => 3,
            'attributes' => [
                'email' => 'myupdatetest@domain.com',
                'active' => true,
            ],
            'links' => [
                'self' => '/user/3'
            ],
        ], $data['data']);

        // TODO - verify the database has the same data
    }

    /**
     * test simple update
     */
    public function testUserUpdateInvalid()
    {
        $client = static::createClient();

        // load user
        /** @var User $user */
        $user = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class)->find(3);

        $client->request(
            'PUT',
            '/api/user/3',
            [],
            [],
            [],
            json_encode([
                'data' => [
                    'type' => 'user',
                    'id' => 3,
                    'attributes' => [
                        'email' => 'invalid',
                    ],
                ],
            ])
        );

        $response = $client->getResponse();

        $this->assertIsJsonapiResponse($response);

        $this->assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());

        // TODO - verify there is no change in the database
        // TODO - verify the errors

        $data = $this->getResponseContentJson($response);


    }
}