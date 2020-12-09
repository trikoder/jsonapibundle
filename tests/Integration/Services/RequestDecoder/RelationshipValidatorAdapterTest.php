<?php

declare(strict_types=1);

namespace Trikoder\JsonApiBundle\Tests\Integration\Services\RequestDecoder;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class RelationshipValidatorAdapterTest extends KernelTestCase
{
    public function relationshipsProvider()
    {
        return
        [
            [
                [
                    'data' => [
                        [
                            'type' => 'author',
                            'id' => '123',
                        ],
                    ],
                ],
                0,
            ],
            [
                [
                    'data' => [
                        [
                            'type' => 'author',
                            'id' => '123',
                        ],
                        [
                            'type' => 'author x',
                            'id' => '456',
                        ],
                    ],
                ],
                0,
            ],
            [
                [
                    'data' => [
                        [
                            'type' => 'author',
                            'id x' => '123',
                        ],
                    ],
                ],
                2,
            ],
            [
                [
                    'data' => [
                        [
                            'type' => 'author',
                            'id' => '123',
                        ],
                        [
                            'type_invalid' => 'author x',
                            'id' => '456',
                        ],
                    ],
                ],
                2,
            ],
            [
                [
                    [
                        [
                            'type' => 'author',
                            'id' => '123',
                        ],
                        [
                            'type' => 'author x',
                            'id' => '456',
                        ],
                    ],
                ],
                2,
            ],
            [
                [
                    'data' => [
                        [
                            'type' => 'idIsNotString',
                            'id' => 123,
                        ],
                    ],
                ],
                1,
            ],
        ];
    }

    /**
     * @dataProvider relationshipsProvider
     */
    public function testRelationships(array $relationshipData, $expectedViolationsCount)
    {
        $kernel = static::bootKernel();
        $validator = $kernel->getContainer()->get('trikoder.jsonapi.relationship_request_body_validator');

        $violationList = $validator->validate($relationshipData);

        $this->assertSame($expectedViolationsCount, $violationList->count());
    }
}
