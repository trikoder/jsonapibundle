<?php

namespace Trikoder\JsonApiBundle\Tests\Functional\Controller;

use Trikoder\JsonApiBundle\Tests\Functional\JsonapiWebTestCase;

/**
 * Class PaginatedIndexActionTest
 * @package Trikoder\JsonApiBundle\Tests\Functional\Controller
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

        // TODO add testing for valid meta info and links
    }
}
