<?php

namespace Trikoder\JsonApiBundle\Tests\Functional\Controller;

use Trikoder\JsonApiBundle\Tests\Functional\JsonapiWebTestCase;

class IndexActionTest extends JsonapiWebTestCase
{
    /**
     * test simple listing
     */
    public function testUserIndexAction()
    {
        $client = static::createClient();

        $client->request('GET', '/api/user/');

        $response = $client->getResponse();

        $this->assertIsJsonapiResponse($response);

        // TODO add count test
    }

    /**
     * test custom map listing
     */
    public function testCustomerIndexAction()
    {
        $client = static::createClient();

        $client->request('GET', '/api/customer/');

        $response = $client->getResponse();

        $this->assertIsJsonapiResponse($response);

        // TODO add count test
    }

    /**
     * test pagination
     */
    public function testUserIndexPaginatedOffsetLimitAction()
    {
        $client = static::createClient();

        // test first page
        $client->request('GET', '/api/user/', ['page' => ['limit' => 1, 'offset' => 0]]);
        $response = $client->getResponse();
        $this->assertIsJsonapiResponse($response);
        $content = $this->getResponseContentJson($response);
        $this->assertCount(1, $content['data']);
        $this->assertEquals(1, $content['data'][0]['id']);

        // test second page
        $client->request('GET', '/api/user/', ['page' => ['limit' => 1, 'offset' => 1]]);
        $response = $client->getResponse();
        $this->assertIsJsonapiResponse($response);
        $content = $this->getResponseContentJson($response);
        $this->assertCount(1, $content['data']);
        $this->assertEquals(2, $content['data'][0]['id']);
    }


    /**
     * test pagination
     */
    public function testUserIndexPaginatedPageSizeAction()
    {
        $client = static::createClient();

        // test first page
        $client->request('GET', '/api/user/', ['page' => ['size' => 1, 'page' => 1]]);
        $response = $client->getResponse();
        $this->assertIsJsonapiResponse($response);
        $content = $this->getResponseContentJson($response);
        $this->assertCount(1, $content['data']);
        $this->assertEquals(1, $content['data'][0]['id']);

        // test second page
        $client->request('GET', '/api/user/', ['page' => ['size' => 1, 'page' => 2]]);
        $response = $client->getResponse();
        $this->assertIsJsonapiResponse($response);
        $content = $this->getResponseContentJson($response);
        $this->assertCount(1, $content['data']);
        $this->assertEquals(2, $content['data'][0]['id']);
    }

    /**
     * test pagination
     */
    public function testSorting()
    {
        $client = static::createClient();

        // test asc sorting
        $client->request('GET', '/api/user/', ['sort' => 'id']);
        $response = $client->getResponse();
        $this->assertIsJsonapiResponse($response);
        $content = $this->getResponseContentJson($response);
        $this->assertEquals(1, $content['data'][0]['id']);

        $client->request('GET', '/api/user/', ['sort' => '-id']);
        $response = $client->getResponse();
        $this->assertIsJsonapiResponse($response);
        $content = $this->getResponseContentJson($response);
        $this->assertEquals(6, $content['data'][0]['id']);
    }

    /**
     * test pagination
     */
    public function testFiltering()
    {
        $client = static::createClient();

        // test id filtering
        $client->request('GET', '/api/user/', ['filter' => ['id' => 1]]);
        $response = $client->getResponse();
        $this->assertIsJsonapiResponse($response);
        $content = $this->getResponseContentJson($response);
        $this->assertCount(1, $content['data']);
        $this->assertEquals(1, $content['data'][0]['id']);

        // test attribute filtering
        $client->request('GET', '/api/user/', ['filter' => ['email' => 'admin@ghosap.com']]);
        $response = $client->getResponse();
        $this->assertIsJsonapiResponse($response);
        $content = $this->getResponseContentJson($response);
        $this->assertCount(1, $content['data']);
        $this->assertEquals(1, $content['data'][0]['id']);

        // test empty response
        $client->request('GET', '/api/user/', ['filter' => ['email' => 'nonexistant']]);
        $response = $client->getResponse();
        $this->assertIsJsonapiResponse($response);
        $content = $this->getResponseContentJson($response);
        $this->assertCount(0, $content['data']);
    }
}
