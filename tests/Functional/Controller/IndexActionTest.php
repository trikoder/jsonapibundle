<?php

namespace Trikoder\JsonApiBundle\Tests\Functional\Controller;

use Symfony\Component\HttpFoundation\Response;
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

    public function testIndexActionWithoutTrailingSlash()
    {
        $client = static::createClient();
        $client->request('GET', '/api/user');
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertIsJsonapiResponse($response);
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
        $client->request('GET', '/api/user/', ['page' => ['size' => 1, 'number' => 1]]);
        $response = $client->getResponse();
        $this->assertIsJsonapiResponse($response);
        $content = $this->getResponseContentJson($response);
        $this->assertCount(1, $content['data']);
        $this->assertEquals(1, $content['data'][0]['id']);

        // test second page
        $client->request('GET', '/api/user/', ['page' => ['size' => 1, 'number' => 2]]);
        $response = $client->getResponse();
        $this->assertIsJsonapiResponse($response);
        $content = $this->getResponseContentJson($response);
        $this->assertCount(1, $content['data']);
        $this->assertEquals(2, $content['data'][0]['id']);
    }

    /**
     * test pagination
     */
    public function testUserIndexPaginatedPageOnlyAction()
    {
        $client = static::createClient();

        // test first page
        $client->request('GET', '/api/user-paginated/', ['page' => ['number' => 1]]);
        $response = $client->getResponse();
        $this->assertIsJsonapiResponse($response);
        $content = $this->getResponseContentJson($response);
        $this->assertCount(2, $content['data']);
        $this->assertEquals(1, $content['data'][0]['id']);

        // test second page
        $client->request('GET', '/api/user-paginated/', ['page' => ['number' => 2]]);
        $response = $client->getResponse();
        $this->assertIsJsonapiResponse($response);
        $content = $this->getResponseContentJson($response);
        $this->assertCount(2, $content['data']);
        $this->assertEquals(3, $content['data'][0]['id']);
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
        // TODO refactor total of 7 to count from database
        $this->assertEquals(7, $content['data'][0]['id']);
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

    /**
     * test simple listing
     */
    public function testCrazyPostIndexAction()
    {
        $client = static::createClient();
        $client->request('GET', '/api/crazy-posts');
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        /*$this->assertIsJsonapiResponse($response);
        $content = $this->getResponseContentJson($response);
        $this->assertEquals([
            'type' => 'crazy',
            'id' => 1,
            'attributes' => [
                'title' => 'Post 1'
            ],
            'links' => [
                'self' => '/crazy/1'
            ]
        ], $content['data'][0]);*/
    }
}
