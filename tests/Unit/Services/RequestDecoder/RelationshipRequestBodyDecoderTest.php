<?php

declare(strict_types=1);

namespace Trikoder\JsonApiBundle\Tests\Unit\Services\RequestDecoder;

use PHPUnit\Framework\TestCase;
use Trikoder\JsonApiBundle\Services\RequestDecoder\RelationshipRequestBodyDecoder;

final class RelationshipRequestBodyDecoderTest extends TestCase
{
    public function dataProvider()
    {
        return
        [
            [
                [
                    'nodata',
                ],
                [],
            ],
            [
                [
                    'data' => null,
                ],
                [],
            ],
            [
                [
                    'data' => [
                        [
                            'type' => 'author',
                            'id' => '123',
                        ],
                    ],
                ],
                [
                    [
                        'type' => 'author',
                        'id' => '123',
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testRelationships(array $body, $expected)
    {
        $bodyDecoder = new RelationshipRequestBodyDecoder();

        $this->assertEquals($expected, $bodyDecoder->decode('', $body));
    }
}
