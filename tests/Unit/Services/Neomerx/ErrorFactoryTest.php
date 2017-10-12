<?php

namespace Trikoder\JsonApiBundle\Tests\Unit\Services;

use Exception;
use Neomerx\JsonApi\Contracts\Document\ErrorInterface;
use Neomerx\JsonApi\Document\Error;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Trikoder\JsonApiBundle\Services\Neomerx\ErrorFactory;

class ErrorFactoryTest extends \PHPUnit_Framework_TestCase
{
    protected function createFactory()
    {
        return new ErrorFactory();
    }

    public function testFromString()
    {
        $testString = "Test error";
        $expected = new Error(null, null, null, null, $testString);

        $this->assertErrorEqual($expected, $this->createFactory()->fromString($testString));

    }

    public function testFromException()
    {
        $testString = "Test error";
        $testException = new Exception($testString, 123);
        $expected = new Error(null, null, 500, 123, Exception::class, $testString);

        $this->assertErrorEqual($expected, $this->createFactory()->fromException($testException));
    }

    public function testFromHttpException()
    {
        $testString = "Test HTTP error";
        $testException = new NotFoundHttpException($testString, null, 123);
        $expected = new Error(null, null, 404, 123, NotFoundHttpException::class, $testString);

        $this->assertErrorEqual($expected, $this->createFactory()->fromException($testException));
    }

    protected function assertErrorEqual(ErrorInterface $expected, ErrorInterface $error)
    {
        $this->assertEquals($expected->getCode(), $error->getCode());
        $this->assertEquals($expected->getId(), $error->getId());
        $this->assertEquals($expected->getDetail(), $error->getDetail());
        $this->assertEquals($expected->getLinks(), $error->getLinks());
        $this->assertEquals($expected->getMeta(), $error->getMeta());
        $this->assertEquals($expected->getSource(), $error->getSource());
        $this->assertEquals($expected->getTitle(), $error->getTitle());
        $this->assertEquals($expected->getStatus(), $error->getStatus());
    }
}
