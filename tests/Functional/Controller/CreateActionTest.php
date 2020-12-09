<?php

namespace Trikoder\JsonApiBundle\Tests\Functional\Controller;

use Symfony\Component\HttpFoundation\Response;
use Trikoder\JsonApiBundle\Tests\Functional\JsonapiWebTestCase;
use Trikoder\JsonApiBundle\Tests\Resources\Entity\User;

class CreateActionTest extends JsonapiWebTestCase
{
    /**
     * test simple create
     */
    public function testUserCreateAction()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/user/',
            [],
            [],
            [],
            json_encode([
                'data' => [
                    'type' => 'user',
                    'attributes' => [
                        'email' => 'mytest@domain.com',
                        'active' => true,
                    ],
                ],
            ])
        );

        $response = $client->getResponse();

        $this->assertIsJsonapiResponse($response);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $data = $this->getResponseContentJson($response);

        $this->assertEquals([
            'type' => 'user',
            'id' => (int) $data['data']['id'], // get take id from response - if none, this will cause error
            'attributes' => [
                'email' => 'mytest@domain.com',
                'active' => true,
            ],
            'links' => [
                'self' => '/user/' . $data['data']['id'],
            ],
        ], $data['data']);

        // TODO - verify the database has the same data
    }

    /**
     * test simple create
     */
    public function testUserCreateInvalid()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/user/',
            [],
            [],
            [],
            json_encode([
                'data' => [
                    'type' => 'user',
                    'attributes' => [
                        'email' => 'invalid',
                    ],
                ],
            ])
        );

        $response = $client->getResponse();

        $this->assertIsJsonapiResponse($response);

        $this->assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());

        // TODO - verify there is no new records in the database
        // TODO - verify the errors
    }

    public function testActionWithoutTrailingSlash()
    {
        $client = static::createClient();
        $client->request('POST', '/api/user');
        $response = $client->getResponse();
        $this->assertNotEquals(Response::HTTP_MOVED_PERMANENTLY, $response->getStatusCode());
        $this->assertIsJsonapiResponse($response);
    }

    /**
     * test simple create
     */
    public function testCreateOnlyController()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/create-only/',
            [],
            [],
            [],
            json_encode([
                'data' => [
                    'type' => 'user',
                    'attributes' => [
                        'email' => 'createonly@domain.com',
                        'active' => true,
                    ],
                ],
            ])
        );

        $response = $client->getResponse();

        $this->assertIsJsonapiResponse($response);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $data = $this->getResponseContentJson($response);

        $this->assertEquals([
            'type' => 'user',
            'id' => (int) $data['data']['id'], // get take id from response - if none, this will cause error
            'attributes' => [
                'email' => 'createonly@domain.com',
                'active' => true,
            ],
            'links' => [
                'self' => '/user/' . $data['data']['id'],
            ],
        ], $data['data']);
    }

    /**
     * test simple create
     */
    public function testProductCreateFailAction()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/products/',
            [],
            [],
            [],
            json_encode([
                'data' => [
                    'type' => 'product',
                    'attributes' => [
                        'title' => 'product',
                        'price' => 'invalid',
                    ],
                ],
            ])
        );

        $response = $client->getResponse();

        $this->assertIsJsonapiResponse($response);

        $this->assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());
    }

    /**
     * test simple create
     */
    public function testProductCreateSuccessAction()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/products/',
            [],
            [],
            [],
            json_encode([
                'data' => [
                    'type' => 'product',
                    'attributes' => [
                        'title' => 'product',
                        'price' => 100,
                    ],
                ],
            ])
        );

        $response = $client->getResponse();

        $this->assertIsJsonapiResponse($response);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    public function testIAmAllowedToCreateUserOnlyWithFieldsConfiguredInAllowedFields()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/user-config-restrictions',
            [],
            [],
            [],
            json_encode([
                'data' => [
                    'type' => 'user',
                    'attributes' => [
                        'i_should_not_be_able_to_send_this_field_during_creation' => 'some malicious value',
                    ],
                ],
            ])
        );

        $response = $client->getResponse();
        $this->assertIsJsonapiResponse($response);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode(), $response->getContent());
    }

    /**
     * test simple create
     */
    public function testUserCreateActionUsingAlternativePost()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/user/',
            [
                'data' => json_encode([
                    'type' => 'user',
                    'attributes' => [
                        'email' => 'mytestspecial@domain.com',
                        'active' => true,
                    ],
                ]),
            ]
        );

        $response = $client->getResponse();

        $this->assertIsJsonapiResponse($response);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $data = $this->getResponseContentJson($response);

        $this->assertEquals([
            'type' => 'user',
            'id' => (int) $data['data']['id'], // get take id from response - if none, this will cause error
            'attributes' => [
                'email' => 'mytestspecial@domain.com',
                'active' => true,
            ],
            'links' => [
                'self' => '/user/' . $data['data']['id'],
            ],
        ], $data['data']);

        // TODO - verify the database has the same data
    }

    public function testCreateWithRelationshipReturnsBadRequestIfInvalidInputIsProvided()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/posts',
            [
                'data' => [
                    'type' => 'post',
                    'attributes' => [
                    ],
                    'relationships' => [
                        'author' => [
                            'data' => [
                                'type' => 'user',
                                // purposefully left out id
                            ],
                        ],
                    ],
                ],
            ]
        );

        $response = $client->getResponse();

        $this->assertIsJsonapiResponse($response);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * test versioned create
     */
    public function testVersionedUserCreateAction()
    {
        $client = static::createClient();

        $email = sprintf('mytest%s@domain.com', time());

        $client->request(
            'POST',
            '/api/v2/user/',
            [],
            [],
            [],
            json_encode([
                'data' => [
                    'type' => 'user',
                    'attributes' => [
                        'email' => $email,
                        'active' => true,
                    ],
                ],
            ])
        );

        $response = $client->getResponse();

        $this->assertIsJsonapiResponse($response);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $data = $this->getResponseContentJson($response);

        $this->assertEquals([
            'type' => 'user',
            'id' => $data['data']['id'], // get take id from response - if none, this will cause error
            'attributes' => [
                'email' => $email,
                'active' => true,
            ],
            'links' => [
                'self' => '/user/' . $data['data']['id'],
            ],
        ], $data['data']);

        // TODO - verify the database has the same data
    }

    public function testSpecifiedIncludesGetIncludedOnCreateRequests()
    {
        $client = static::createClient();

        /** @var User $user */
        $user = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class)->find(1);

        $client->request(
            'POST',
            '/api/posts?include=author',
            [],
            [],
            [],
            json_encode([
                'data' => [
                    'type' => 'post',
                    'attributes' => [
                        'title' => bin2hex(random_bytes(16)),
                        'active' => true,
                    ],
                    'relationships' => [
                        'author' => [
                            'data' => [
                                'type' => 'user',
                                'id' => (string) $user->getId(),
                            ],
                        ],
                    ],
                ],
            ])
        );

        $response = $client->getResponse();

        $this->assertIsJsonapiResponse($response);

        $this->assertSame(Response::HTTP_CREATED, $response->getStatusCode());

        $data = $this->getResponseContentJson($response);

        $this->assertArrayHasKey('included', $data);

        $this->assertSame([
            [
                'type' => 'user',
                'id' => (string) $user->getId(),
                'attributes' => [
                    'email' => $user->getEmail(),
                    'active' => $user->isActive(),
                ],
            ],
        ], $data['included']);
    }
}
