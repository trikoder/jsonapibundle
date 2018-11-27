<?php

declare(strict_types=1);

namespace Trikoder\JsonApiBundle\Tests\Functional\Controller;

use Symfony\Component\HttpFoundation\Response;
use Trikoder\JsonApiBundle\Tests\Functional\JsonapiWebTestCase;

final class RequiredRolesTest extends JsonapiWebTestCase
{
    public function testIndexAuthorizationRequiredResponse()
    {
        $client = static::createClient();
        $client->request('GET', '/api/user-limited-access/');
        $response = $client->getResponse();

        $this->assertSame(Response::HTTP_FORBIDDEN, $response->getStatusCode());
        $this->assertIsJsonapiResponse($response);
    }

    public function testIndexAccessGrantedForCorrectRole()
    {
        $client = static::createClient([], ['PHP_AUTH_USER' => 'admin_tester',
                                            'PHP_AUTH_PW' => 'admin_tester', ]);
        $client->request('GET', '/api/user-limited-access/');
        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertIsJsonapiResponse($response);
    }

    public function testIndexForbiddenForWrongRole()
    {
        $client = static::createClient([], ['PHP_AUTH_USER' => 'tester',
                                            'PHP_AUTH_PW' => 'tester', ]);
        $client->request('GET', '/api/user-limited-access/');
        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
        $this->assertIsJsonapiResponse($response);
    }

    public function testCreateAuthorizationRequiredResponse()
    {
        $client = static::createClient();
        $client->request('POST', '/api/user-limited-access/');
        $response = $client->getResponse();

        $this->assertSame(Response::HTTP_FORBIDDEN, $response->getStatusCode());
        $this->assertIsJsonapiResponse($response);
    }

    public function testCreateAccessGrantedRequiredRolesDefinedAsArray()
    {
        $client = static::createClient([], ['PHP_AUTH_USER' => 'tester',
                                            'PHP_AUTH_PW' => 'tester', ]);
        $client->request('POST', '/api/user-limited-access/');
        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());
        $this->assertIsJsonapiResponse($response);
    }

    public function testDeleteAuthorizationRequiredResponse()
    {
        $client = static::createClient();
        $client->request('DELETE', '/api/user-limited-access/666');
        $response = $client->getResponse();

        $this->assertSame(Response::HTTP_FORBIDDEN, $response->getStatusCode());
        $this->assertIsJsonapiResponse($response);
    }

    public function testDeleteAccessGrantedForCorrectRole()
    {
        $client = static::createClient([], ['PHP_AUTH_USER' => 'tester',
                                            'PHP_AUTH_PW' => 'tester', ]);
        $client->request('DELETE', '/api/user-limited-access/666');
        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertIsJsonapiResponse($response);
    }

    public function testUpdateAuthorizationRequiredResponse()
    {
        $client = static::createClient();
        $client->request('PATCH', '/api/user-limited-access/666');
        $response = $client->getResponse();

        $this->assertSame(Response::HTTP_FORBIDDEN, $response->getStatusCode());
        $this->assertIsJsonapiResponse($response);
    }

    public function testUpdateAccessGrantedForCorrect()
    {
        $client = static::createClient([], ['PHP_AUTH_USER' => 'tester',
                                            'PHP_AUTH_PW' => 'tester', ]);
        $client->request('PATCH', '/api/user-limited-access/666');
        $response = $client->getResponse();

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertIsJsonapiResponse($response);
    }
}
