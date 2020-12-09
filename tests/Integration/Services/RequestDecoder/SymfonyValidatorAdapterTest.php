<?php

declare(strict_types=1);

namespace Trikoder\JsonApiBundle\Tests\Integration\Services\RequestDecoder;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class SymfonyValidatorAdapterTest extends KernelTestCase
{
    public function relationshipsProvider()
    {
        return [
            [
                // single resource
                [
                    'author' => [
                        'data' => [
                            'type' => 'author',
                            'id' => '123',
                        ],
                    ],
                ],
                0,
            ],
            [
                [
                    'author' => [
                        'data' => [
                            // missing type on purpose
                            'id' => '123',
                        ],
                    ],
                ],
                1,
            ],
            [
                // null
                [
                    'images' => [
                        'data' => null,
                    ],
                ],
                0,
            ],
            [
                // empty collection
                [
                    'images' => [
                        'data' => [],
                    ],
                ],
                0,
            ],
            [
                // collection of resources
                [
                    'comments' => [
                        'data' => [
                            [
                                'type' => 'comment',
                                'id' => '1',
                            ],
                            [
                                'type' => 'comment',
                                'id' => '2',
                            ],
                        ],
                    ],
                ],
                0,
            ],
            [
                [
                    'comments' => [
                        'data' => [
                            [
                                'type' => 'comment',
                                // missing id on purpose
                            ],
                            [
                                'type' => 'comment',
                                'id' => '',
                            ],
                        ],
                    ],
                ],
                2,
            ],
        ];
    }

    /**
     * @dataProvider relationshipsProvider
     */
    public function testRelationships(array $relationshipData, $expectedViolationsCount)
    {
        $kernel = static::bootKernel();
        $validator = $kernel->getContainer()->get('trikoder.jsonapi.request_body_validator');

        $violationList = $validator->validate([
            'data' => [
                'type' => 'foobar',
                'relationships' => $relationshipData,
            ],
        ]);

        $this->assertSame($expectedViolationsCount, $violationList->count());
    }
}
