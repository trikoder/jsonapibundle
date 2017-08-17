<?php

namespace Trikoder\JsonApiBundle\Tests\Functional\Controller;

use Symfony\Component\HttpFoundation\Response;
use Trikoder\JsonApiBundle\Tests\Functional\JsonapiWebTestCase;
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
                'self' => '/user/1'
            ],
        ], $data['data']);
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
                'profile' => $client->getContainer()->get('router')->generate('customer_dummy_action', ['id' => 4])
            ],
            'links' => [
                'self' => '/customer/4'
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
                'self' => '/user/1'
            ],
        ], $data['data']);
    }

    // TODO add test for include
}
