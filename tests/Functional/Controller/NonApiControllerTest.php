<?php

namespace Trikoder\JsonApiBundle\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class NonApiControllerTest extends WebTestCase
{
    /**
     * test html response
     */
    public function testHTMLAction()
    {
        $client = static::createClient();

        $client->request(
            'PUT',
            '/non-api/test'
        );

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('Test', $response->getContent());
    }

    /**
     * test exception
     */
    public function testExceptionAction()
    {
        $client = static::createClient();

        $client->request(
            'PUT',
            '/non-api/exception'
        );

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $responseContent = $response->getContent();
        $this->assertContains('Test', $responseContent);
        // if jsondecode returns null on non-empty it is not json *puke*
        $this->assertTrue(null === json_decode($responseContent) && !empty($responseContent));
    }
}
