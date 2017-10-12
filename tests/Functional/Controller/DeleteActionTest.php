<?php

namespace Trikoder\JsonApiBundle\Tests\Functional\Controller;

use Symfony\Component\HttpFoundation\Response;
use Trikoder\JsonApiBundle\Tests\Functional\JsonapiWebTestCase;
use Trikoder\JsonApiBundle\Tests\Resources\Entity\User;

class DeleteActionTest extends JsonapiWebTestCase
{
    /**
     * test simple delete
     */
    public function testUserDeleteAction()
    {
        $client = static::createClient();

        // load user
        /** @var User $user */
        $user = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class)->find(5);
        $this->assertNotNull($user);

        $client->request(
            'DELETE',
            '/api/user/5',
            [],
            [],
            [],
            ''
        );

        $response = $client->getResponse();

        $this->assertIsJsonapiResponse($response);

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->assertEmpty($response->getContent());

        // clear em
        $client->getContainer()->get('doctrine.orm.entity_manager')->clear();
        // verify the database has the same data
        $user = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class)->find(5);
        $this->assertNull($user);
    }

    public function testActionWithTrailingSlash()
    {
        $client = static::createClient();

        $user = new User();
        $user->setEmail('testActionWithTrailingSlash@test.com');
        $client->getContainer()->get('doctrine.orm.entity_manager')->persist($user);

        $client->request(
            'DELETE',
            '/api/user/'.$user->getId().'/',
            [],
            [],
            [],
            ''
        );
        $response = $client->getResponse();
        $this->assertNotEquals(Response::HTTP_MOVED_PERMANENTLY, $response->getStatusCode());
    }

    public function testNotFound()
    {
        $client = static::createClient();

        $client->request(
            'DELETE',
            '/api/user/99999',
            [],
            [],
            [],
            ''
        );
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }
}
