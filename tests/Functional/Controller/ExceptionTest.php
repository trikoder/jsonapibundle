<?php

namespace Trikoder\JsonApiBundle\Tests\Functional\Controller;

use Symfony\Component\HttpFoundation\Response;
use Trikoder\JsonApiBundle\Tests\Functional\JsonapiWebTestCase;
use Trikoder\JsonApiBundle\Tests\Resources\Entity\User;

class ExceptionTest extends JsonapiWebTestCase
{
    /**
     * test exception response
     */
    public function testException()
    {
        $client = static::createClient();

        $client->request(
            'PUT',
            '/api/exception'
        );

        $response = $client->getResponse();
        $this->assertIsJsonapiResponse($response);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());

        // TODO - verify the errors

        $data = $this->getResponseContentJson($response);


    }
}