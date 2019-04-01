<?php

namespace Trikoder\JsonApiBundle\Tests\Functional\Controller;

use Symfony\Component\HttpFoundation\Response;
use Trikoder\JsonApiBundle\Tests\Functional\JsonapiWebTestCase;
use Trikoder\JsonApiBundle\Tests\Resources\Entity\Post;
use Trikoder\JsonApiBundle\Tests\Resources\Entity\User;

class ShowActionTest extends JsonapiWebTestCase
{
    /**
     * Test simple show action
     */
    public function testUserShowAction()
    {
        $client = static::createClient();

        // load user
        /** @var User $user */
        $user = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class)->find(1);

        $client->request('GET', '/api/user/1');

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertIsJsonapiResponse($response);

        $data = $this->getResponseContentJson($response);

        $this->assertEquals([
            'type' => 'user',
            'id' => 1,
            'attributes' => [
                'email' => $user->getEmail(),
                'active' => $user->isActive(),
            ],
            'links' => [
                'self' => '/user/1',
            ],
        ], $data['data']);
    }

    public function testActionTrailingSlash()
    {
        $client = static::createClient();
        $client->request('GET', '/api/user/1/');
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertIsJsonapiResponse($response);
    }

    /**
     * Test a bit more complex show action
     */
    public function testCustomerShowAction()
    {
        $client = static::createClient();

        // load user
        /** @var User $user */
        $user = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class)->find(4);

        $client->request('GET', '/api/customer/4');

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertIsJsonapiResponse($response);

        $data = $this->getResponseContentJson($response);

        $this->assertEquals([
            'type' => 'customer',
            'id' => 4,
            'attributes' => [
                'email' => $user->getEmail(),
                'profile' => $client->getContainer()->get('router')->generate('customer_dummy_action', ['id' => 4]),
            ],
            'links' => [
                'self' => '/customer/4',
            ],
        ], $data['data']);
    }

    public function testReducedUserShowAction()
    {
        $client = static::createClient();

        // load user
        /** @var User $user */
        $user = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class)->find(1);

        $client->request('GET', '/api/user/1?fields[user]=email');

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertIsJsonapiResponse($response);

        $data = $this->getResponseContentJson($response);

        $this->assertEquals([
            'type' => 'user',
            'id' => 1,
            'attributes' => [
                'email' => $user->getEmail(),
            ],
            'links' => [
                'self' => '/user/1',
            ],
        ], $data['data']);
    }

    /**
     *
     */
    public function testReducedResponseUserController()
    {
        $client = static::createClient();

        // load user
        /** @var User $user */
        $user = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class)->find(1);

        $client->request('GET', '/api/reduced-user-response/1');

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertIsJsonapiResponse($response);

        $data = $this->getResponseContentJson($response);

        $this->assertEquals([
            'type' => 'user',
            'id' => '1',
            'attributes' => [
                'email' => $user->getEmail(),
            ],
            'links' => [
                'self' => '/user/1',
            ],
        ], $data['data']);
    }

    public function testNotFound()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/user/9999',
            [],
            [],
            [],
            ''
        );
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertIsJsonapiResponse($response);
    }

    public function testRelationshipsDefault()
    {
        $client = static::createClient();

        $client->request('GET', '/api/posts/1');

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertIsJsonapiResponse($response);

        $data = $this->getResponseContentJson($response);

        $this->assertEquals([
            'type' => 'post',
            'id' => '1',
            'attributes' => [
                'title' => 'Post 1',
            ],
            'relationships' => [
                'author' => [
                    'data' => [
                        'type' => 'user',
                        'id' => 3,
                    ],
                ],
            ],
            'links' => [
                'self' => '/post/1',
            ],
        ], $data['data']);

        $this->assertArrayNotHasKey('include', $data);
    }

    public function testRelationshipsDefaultRequested()
    {
        $client = static::createClient();

        // load post
        /** @var Post post */
        $post = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(Post::class)->find(1);

        $client->request('GET', '/api/posts/1', ['include' => 'author']);

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertIsJsonapiResponse($response);

        $data = $this->getResponseContentJson($response);

        $this->assertEquals([
            'type' => 'post',
            'id' => '1',
            'attributes' => [
                'title' => 'Post 1',
            ],
            'relationships' => [
                'author' => [
                    'data' => [
                        'type' => 'user',
                        'id' => 3,
                    ],
                ],
            ],
            'links' => [
                'self' => '/post/1',
            ],
        ], $data['data']);

        $this->assertEquals([
            [
                'type' => 'user',
                'id' => '3',
                'attributes' => [
                    'email' => $post->getAuthor()->getEmail(),
                    'active' => $post->getAuthor()->isActive(),
                ],
            ],
        ], $data['included']);

        $this->assertArrayHasKey('included', $data);
        $this->assertNotEmpty($data['included']);
        $this->assertEquals('user', $data['included'][0]['type']);
        $this->assertEquals(3, $data['included'][0]['id']);
    }

    public function testIAmAllowedToFetchOnlyFieldsConfiguredInAllowedFields()
    {
        $client = static::createClient();

        $client->request(
            'GET',
            '/api/user-config-restrictions/3?fields[user]=i_should_not_be_able_to_fetch_this_field'
        );

        $response = $client->getResponse();
        $this->assertIsJsonapiResponse($response);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    // TODO - If a server is unable to identify a relationship path or does not support inclusion of resources from a path, it MUST respond with 400 Bad Request.
}
