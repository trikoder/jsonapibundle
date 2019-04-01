<?php

declare(strict_types=1);

namespace Trikoder\JsonApiBundle\Tests\Unit\Services;

use Trikoder\JsonApiBundle\Services\RequestDecoder\Exception\InvalidBodyForMethodException;
use Trikoder\JsonApiBundle\Services\RequestDecoder\RequestBodyValidator;

final class RequestBodyValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testExceptionForEmptyPostBody()
    {
        $this->expectException(InvalidBodyForMethodException::class);
        $this->expectExceptionMessage('Passed body is not valid for request method POST');
        $this->createService()->validate('POST', []);
    }

    protected function createService()
    {
        return new RequestBodyValidator();
    }

    public function testNullDataBody()
    {
        $testData = [
            'data' => null,
        ];

        $result = $this->createService()->validate('POST', $testData);

        $this->assertNull($result);
    }

    public function testExceptionForDataWithoutTypePostBody()
    {
        $testData = [
            'data' => [
                'id' => 1,
            ],
        ];

        $this->expectException(InvalidBodyForMethodException::class);
        $this->expectExceptionMessage('Passed body is not valid for request method POST');
        $this->createService()->validate('POST', $testData);
    }

    public function testValidPostBody()
    {
        $testData = [
            'data' => [
                'type' => 'ok',
            ],
        ];

        $this->assertNull($this->createService()->validate('POST', $testData));
    }
}
