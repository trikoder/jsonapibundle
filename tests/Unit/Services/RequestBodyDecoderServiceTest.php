<?php

namespace Trikoder\JsonApiBundle\Tests\Unit\Services;

use Trikoder\JsonApiBundle\Services\RequestDecoder\RequestBodyDecoderService;
use Trikoder\JsonApiBundle\Services\RequestDecoder\RequestBodyValidator;

class RequestBodyDecoderServiceTest extends \PHPUnit_Framework_TestCase
{
    protected function createService()
    {
        return new RequestBodyDecoderService(new RequestBodyValidator());
    }

    /**
     * Test service with full valid request
     */
    public function testFullBody()
    {
        $testData = [
            'data' => [
                'type' => 'photos',
                'id' => 123,
                'attributes' => [
                    'title' => 'test title',
                    'complex' => [1, 2, 'test'],
                ],
                'relationships' => [
                    'someRelated' => [
                        'data' => [
                            'type' => 'related',
                            'id' => 321,
                        ],
                    ],
                    'someOtherRelateds' => [
                        'data' => [
                            ['type' => 'someOther', 'id' => 51],
                            ['type' => 'someOther', 'id' => 52],
                            ['type' => 'someOther', 'id' => 53],
                        ],
                    ],
                ],
            ],
        ];

        $expectedData = [
            'id' => 123,
            'title' => 'test title',
            'complex' => [1, 2, 'test'],
            'someRelated' => 321,
            'someOtherRelateds' => [51, 52, 53],
        ];

        $result = $this->createService()->decode('POST', $testData);

        $this->assertEquals($expectedData, $result);
    }

    /**
     * test simple empty body
     */
    public function testEmptyBody()
    {
        $result = $this->createService()->decode('GET', []);
        $this->assertEquals([], $result);
    }

    /**
     * Test item that is being created
     */
    public function testCreateLikeItem()
    {
        $testData = [
            'data' => [
                'type' => 'photos',
                'attributes' => [
                    'title' => 'test title',
                ],
            ],
        ];

        $expectedData = [
            'title' => 'test title',
        ];

        $result = $this->createService()->decode('GET', $testData);

        $this->assertEquals($expectedData, $result);
    }

    public function testNullDataBody()
    {
        $testData = [
            'data' => null,
        ];

        $result = $this->createService()->decode('POST', $testData);

        $this->assertEquals([], $result);
    }
}
