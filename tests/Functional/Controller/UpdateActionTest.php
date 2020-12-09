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
            [
                'data' => [
                    'type' => 'user',
                    'id' => '3',
                    'attributes' => [
                        'email' => 'myupdatetest@domain.com',
                        'active' => true,
                    ],
                ],
            ]
        );

        $response = $client->getResponse();

        $this->assertIsJsonapiResponse($response);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $data = $this->getResponseContentJson($response);

        $this->assertEquals([
            'type' => 'user',
            'id' => '3',
            'attributes' => [
                'email' => 'myupdatetest@domain.com',
                'active' => true,
            ],
            'links' => [
                'self' => '/user/3',
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
            [
                'data' => [
                    'type' => 'user',
                    'id' => '3',
                    'attributes' => [
                        'email' => 'invalid',
                    ],
                ],
            ]
        );

        $response = $client->getResponse();

        $this->assertIsJsonapiResponse($response);

        $this->assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());

        // TODO - verify there is no change in the database
        // TODO - verify the errors

        $data = $this->getResponseContentJson($response);
    }

    public function testActionWithTrailingSlash()
    {
        $client = static::createClient();
        $client->request('PUT', '/api/user/999/');
        $response = $client->getResponse();
        $this->assertNotEquals(Response::HTTP_MOVED_PERMANENTLY, $response->getStatusCode());
    }

    public function testNotFound()
    {
        $client = static::createClient();
        $client->request(
            'PUT',
            '/api/user/999',
            [
                'data' => [
                    'type' => 'user',
                    'id' => '3',
                    'attributes' => [
                        'email' => 'invalid',
                    ],
                ],
            ]
        );
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testErrorReturnedForEmptyRequestBody()
    {
        $client = static::createClient();

        $client->request(
            'PUT',
            '/api/user/3',
            [],
            [],
            []
        );

        $response = $client->getResponse();

        $this->assertIsJsonapiResponse($response);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testErrorReturnedForInvalidJsonRequestBody()
    {
        $client = static::createClient();

        $client->request(
            'PUT',
            '/api/user/3',
            [],
            [],
            [],
            'asdads'
        );

        $response = $client->getResponse();

        $this->assertIsJsonapiResponse($response);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testIAmAllowedToUpdateOnlyFieldsConfiguredInAllowedFields()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/user-config-restrictions/3',
            [
                'data' => [
                    'type' => 'user',
                    'id' => '3',
                    'attributes' => [
                        'i_should_not_be_able_to_update_this_field' => 'some malicious value',
                    ],
                ],
            ]
        );

        $response = $client->getResponse();
        $this->assertIsJsonapiResponse($response);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode(), $response->getContent());
    }

    /**
     * test simple update
     */
    public function testVersionedUserUpdateAction()
    {
        $client = static::createClient();

        // load user
        /** @var User $user */
        $user = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class)->find(3);

        $client->request(
            'PUT',
            '/api/v2/user/3',
            [
                'data' => [
                    'type' => 'user',
                    'id' => '3',
                    'attributes' => [
                        'active' => true,
                    ],
                ],
            ]
        );

        $response = $client->getResponse();

        $this->assertIsJsonapiResponse($response);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }
}
