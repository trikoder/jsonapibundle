<?php

declare(strict_types=1);

namespace Trikoder\JsonApiBundle\Tests\Unit\Response;

use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\HttpFoundation\Response;
use Trikoder\JsonApiBundle\Response\CreatedResponse;
use Trikoder\JsonApiBundle\Response\Header;

final class CreatedResponseTest extends TestCase
{
    public function testCreatedResponseCanBeConstructed()
    {
        $response = new CreatedResponse(new stdClass(), [], [], null);

        $this->assertInstanceOf(CreatedResponse::class, $response);
    }

    public function testCreatingCreatedResponseWithLocation()
    {
        $location = 'foobar';

        $response = new CreatedResponse(new stdClass(), [], [], $location);

        $headers = $response->getHeaders();

        $this->assertCount(1, $headers);

        $this->assertSame($response->getStatusCode(), Response::HTTP_CREATED);
        $this->assertSame('Location', $headers[0]->getKey());
        $this->assertSame($location, $headers[0]->getValue());
    }

    public function testCreatingCreatedResponseWithHeader()
    {
        $response = new CreatedResponse(new stdClass(), [], [], null, [new Header('x-test', 'bar')]);

        $headers = $response->getHeaders();

        $this->assertCount(1, $headers);

        $this->assertSame($response->getStatusCode(), Response::HTTP_CREATED);
        $this->assertSame('x-test', $headers[0]->getKey());
        $this->assertSame('bar', $headers[0]->getValue());
    }
}
