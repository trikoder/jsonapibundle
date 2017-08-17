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
}