<?php

namespace Trikoder\JsonApiBundle\Tests\Functional\Controller;

use Symfony\Component\HttpFoundation\Response;
use Trikoder\JsonApiBundle\Tests\Functional\JsonapiWebTestCase;

/**
 * Class PaginatedIndexActionTest
 */
class PaginatedIndexActionTest extends JsonapiWebTestCase
{
    /**
     * test simple listing
     */
    public function testUserIndexAction()
    {
        $client = static::createClient();

        $client->request('GET', '/api/user-paginated/');

        $response = $client->getResponse();

        $this->assertIsJsonapiResponse($response);

        $content = $this->getResponseContentJson($response);
        $this->assertCount(2, $content['data']);

        $this->assertArrayHasKey('meta', $content);
        $this->assertEquals([
            // TODO refactor this to count from database
            'total' => 7,
        ], $content['meta']);

        $this->assertArrayHasKey('links', $content);
        $this->assertArrayHasKey('self', $content['links']);
        $this->assertArrayHasKey('prev', $content['links']);
        $this->assertArrayHasKey('next', $content['links']);
        $this->assertArrayHasKey('first', $content['links']);
        $this->assertArrayHasKey('last', $content['links']);

        // check if urls are correct, eg. /api/user-paginated/?page%5Blimit%5D=2&page%5Boffset%5D=0
        $this->assertStringEndsWith('/api/user-paginated/?page%5Blimit%5D=2&page%5Boffset%5D=0', $content['links']['self']);
        $this->assertStringEndsWith('/api/user-paginated/?page%5Blimit%5D=2&page%5Boffset%5D=2', $content['links']['next']);
        $this->assertStringEndsWith('/api/user-paginated/?page%5Blimit%5D=2&page%5Boffset%5D=0', $content['links']['first']);
        $this->assertEmpty($content['links']['prev']);
    }

    public function testFiltersPaginatedLink()
    {
        $client = static::createClient();

        $client->request('GET', '/api/user-paginated/', ['filter' => ['id' => '1,2,3,4,99']]);

        $response = $client->getResponse();

        $this->assertIsJsonapiResponse($response);
        $content = $this->getResponseContentJson($response);

        // check if urls are correct, eg. /api/user-paginated/?page%5Blimit%5D=2&page%5Boffset%5D=0
        $this->assertStringEndsWith('/api/user-paginated/?filter%5Bid%5D=1%2C2%2C3%2C4%2C99&page%5Blimit%5D=2&page%5Boffset%5D=0', $content['links']['self']);
        $this->assertStringEndsWith('/api/user-paginated/?filter%5Bid%5D=1%2C2%2C3%2C4%2C99&page%5Blimit%5D=2&page%5Boffset%5D=2', $content['links']['next']);
        $this->assertStringEndsWith('/api/user-paginated/?filter%5Bid%5D=1%2C2%2C3%2C4%2C99&page%5Blimit%5D=2&page%5Boffset%5D=0', $content['links']['first']);
        $this->assertEmpty($content['links']['prev']);
    }

    public function testFieldsPaginatedLink()
    {
        $client = static::createClient();

        $client->request('GET', '/api/user-paginated/', ['fields' => ['user' => 'email,id,active,invalid']]);

        $response = $client->getResponse();

        $this->assertIsJsonapiResponse($response);
        $content = $this->getResponseContentJson($response);

        // check if urls are correct, eg. /api/user-paginated/?page%5Blimit%5D=2&page%5Boffset%5D=0
        $this->assertStringEndsWith('/api/user-paginated/?fields%5Buser%5D=email%2Cid%2Cactive%2Cinvalid&page%5Blimit%5D=2&page%5Boffset%5D=0', $content['links']['self']);
        $this->assertStringEndsWith('/api/user-paginated/?fields%5Buser%5D=email%2Cid%2Cactive%2Cinvalid&page%5Blimit%5D=2&page%5Boffset%5D=2', $content['links']['next']);
        $this->assertStringEndsWith('/api/user-paginated/?fields%5Buser%5D=email%2Cid%2Cactive%2Cinvalid&page%5Blimit%5D=2&page%5Boffset%5D=0', $content['links']['first']);
        $this->assertEmpty($content['links']['prev']);
    }

    public function testIncludePaginatedLink()
    {
        $client = static::createClient();

        $client->request('GET', '/api/user-paginated/', ['include' => 'user,post']);

        $response = $client->getResponse();

        $this->assertIsJsonapiResponse($response);
        $content = $this->getResponseContentJson($response);

        // check if urls are correct, eg. /api/user-paginated/?page%5Blimit%5D=2&page%5Boffset%5D=0
        $this->assertStringEndsWith('/api/user-paginated/?include=user%2Cpost&page%5Blimit%5D=2&page%5Boffset%5D=0', $content['links']['self']);
        $this->assertStringEndsWith('/api/user-paginated/?include=user%2Cpost&page%5Blimit%5D=2&page%5Boffset%5D=2', $content['links']['next']);
        $this->assertStringEndsWith('/api/user-paginated/?include=user%2Cpost&page%5Blimit%5D=2&page%5Boffset%5D=0', $content['links']['first']);
        $this->assertEmpty($content['links']['prev']);
    }

    public function testActionWithoutTrailingSlash()
    {
        $client = static::createClient();
        $client->request('GET', '/api/user-paginated');
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertIsJsonapiResponse($response);
    }
}
