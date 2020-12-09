<?php

declare(strict_types=1);

namespace Trikoder\JsonApiBundle\Tests\Functional\Controller;

use Symfony\Component\HttpFoundation\Response;
use Trikoder\JsonApiBundle\Tests\Functional\JsonapiWebTestCase;
use Trikoder\JsonApiBundle\Tests\Resources\Entity\Tag;
use Trikoder\JsonApiBundle\Tests\Resources\Entity\User;

final class UpdateRelationshipActionTest extends JsonapiWebTestCase
{
    private $client;
    private $user;

    protected function setUp()
    {
        parent::setUp();
        $this->client = static::createClient();
    }

    protected function tearDown()
    {
        $this->client = null;
        $this->user = null;
        parent::tearDown();
    }

    public function testConflictIsReturnedWhenReferencedResourceDoesNotExist()
    {
        $user = $this->getUser();
        [$tagOne, $tagTwo, $tagThree] = $this->getTags();

        $this->assertEquals(1, $user->getTags()->count());

        $this->client->request(
            'POST',
            sprintf('/api/user/%d/relationships/tags', $user->getId()),
            [
                'data' => [
                    [
                        'type' => 'tag',
                        'id' => '666',
                    ],
                ],
            ]
        );

        $response = $this->client->getResponse();

        $this->assertIsJsonapiResponse($response);
        $this->assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());
        $this->assertEquals(1, $user->getTags()->count());
    }

    public function testPostAddsResourceToRelationship()
    {
        $user = $this->getUser();
        [$tagOne, $tagTwo, $tagThree] = $this->getTags();

        $missingTags = [
            $tagOne->getId() => $tagOne,
            $tagTwo->getId() => $tagTwo,
            $tagThree->getId() => $tagThree,
        ];

        foreach ($this->getUser()->getTags() as $tag) {
            unset($missingTags[$tag->getId()]);
        }

        $this->assertEquals(2, \count($missingTags));
        $this->assertEquals(1, $user->getTags()->count());

        [$tagOne, $tagTwo] = array_values($missingTags);

        $this->client->request(
            'POST',
            sprintf('/api/user/%d/relationships/tags', $user->getId()),
            [
                'data' => [
                    [
                        'type' => 'tag',
                        'id' => (string) $tagOne->getId(),
                    ],
                    [
                        'type' => 'tag',
                        'id' => (string) $tagTwo->getId(),
                    ],
                ],
            ]
        );

        $response = $this->client->getResponse();

        $this->assertIsJsonapiResponse($response);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode(), $response->getContent());

        $this->assertEquals(3, $user->getTags()->count());
    }

    /**
     * @depends testPostAddsResourceToRelationship
     */
    public function testPostReturnsNoContentWhenResourcesAreAlreadyAdded()
    {
        $user = $this->getUser();
        [$tagOne, $tagTwo, $tagThree] = $this->getTags();

        $this->client->request(
            'POST',
            sprintf('/api/user/%d/relationships/tags', $user->getId()),
            [
                'data' => [
                    [
                        'type' => 'tag',
                        'id' => (string) $tagOne->getId(),
                    ],
                    [
                        'type' => 'tag',
                        'id' => (string) $tagTwo->getId(),
                    ],
                ],
            ]
        );

        $response = $this->client->getResponse();

        $this->assertIsJsonapiResponse($response);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode(), $response->getContent());

        $this->assertEquals(3, $user->getTags()->count());
    }

    /**
     * @depends testPostAddsResourceToRelationship
     */
    public function testDeleteRemovesResourceFromRelationship()
    {
        $user = $this->getUser();
        [$tagOne, $tagTwo, $tagThree] = $this->getTags();

        $this->assertEquals(3, $user->getTags()->count());

        $this->client->request(
            'DELETE',
            sprintf('/api/user/%d/relationships/tags', $user->getId()),
            [
                'data' => [
                    [
                        'type' => 'tag',
                        'id' => (string) $tagOne->getId(),
                    ],
                    [
                        'type' => 'tag',
                        'id' => (string) $tagTwo->getId(),
                    ],
                ],
            ]
        );

        $response = $this->client->getResponse();

        $this->assertIsJsonapiResponse($response);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());

        $this->assertEquals(1, $user->getTags()->count());
    }

    /**
     * @depends testDeleteRemovesResourceFromRelationship
     */
    public function testDeleteReturnsNoContentWhenResourcesAreAlreadyRemoved()
    {
        $user = $this->getUser();
        [$tagOne, $tagTwo, $tagThree] = $this->getTags();

        $this->assertEquals(1, $user->getTags()->count());

        $this->client->request(
            'DELETE',
            sprintf('/api/user/%d/relationships/tags', $user->getId()),
            [
                'data' => [
                    [
                        'type' => 'tag',
                        'id' => (string) $tagOne->getId(),
                    ],
                    [
                        'type' => 'tag',
                        'id' => (string) $tagTwo->getId(),
                    ],
                ],
            ]
        );

        $response = $this->client->getResponse();

        $this->assertIsJsonapiResponse($response);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode(), $response->getContent());

        $this->assertEquals(1, $user->getTags()->count());
    }

    public function testPostReturnsForbiddenForForbiddenRelationshipAction()
    {
        $this->client->request(
            'POST',
            sprintf('/api/user/%d/relationships/carts', $this->getUser()->getId()),
            [
                'data' => [
                    [
                        'type' => 'cart',
                        'id' => '1',
                    ],
                ],
            ]
        );

        $response = $this->client->getResponse();

        $this->assertIsJsonapiResponse($response);
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testPostReturnsForbiddenForAllowedButNonExistentRelationshipAction()
    {
        $this->client->request(
            'POST',
            sprintf('/api/user/%d/relationships/relationshipWhichDoesNotExistOnResource', $this->getUser()->getId()),
            [
                'data' => [
                    [
                        'type' => 'relationshipWhichDoesNotExistOnResource',
                        'id' => '1',
                    ],
                ],
            ]
        );

        $response = $this->client->getResponse();

        $this->assertIsJsonapiResponse($response);
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode(), $response->getContent());
    }

    public function testPostReturnsBadRequestForEmptyRequestBody()
    {
        $this->client->request(
            'POST',
            sprintf('/api/user/%d/relationships/tags', $this->getUser()->getId()),
            [
                'data' => [],
            ]
        );

        $response = $this->client->getResponse();

        $this->assertIsJsonapiResponse($response);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function getInvalidData(): array
    {
        return [
            [
                [
                    'type' => 'tag',
                ],
            ],
            [
                [
                    'id' => '1',
                ],
            ],
        ];
    }

    /**
     * @dataProvider getInvalidData
     */
    public function testPostReturnsBadRequestForInvalidJsonRequestBody(array $data)
    {
        $this->client->request(
            'POST',
            sprintf('/api/user/%d/relationships/tags', $this->getUser()->getId()),
            ['data' => [$data]]
        );

        $response = $this->client->getResponse();

        $this->assertIsJsonapiResponse($response);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testDeleteReturnsForbiddenForForbiddenRelationshipAction()
    {
        $this->client->request(
            'DELETE',
            sprintf('/api/user/%d/relationships/carts', $this->getUser()->getId()),
            [
                'data' => [
                    [
                        'type' => 'cart',
                        'id' => '1',
                    ],
                ],
            ]
        );

        $response = $this->client->getResponse();

        $this->assertIsJsonapiResponse($response);
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testDeleteReturnsForbiddenForAllowedButNonExistentRelationshipAction()
    {
        $this->client->request(
            'DELETE',
            sprintf('/api/user/%d/relationships/relationshipWhichDoesNotExistOnResource', $this->getUser()->getId()),
            [
                'data' => [
                    [
                        'type' => 'relationshipWhichDoesNotExistOnResource',
                        'id' => '1',
                    ],
                ],
            ]
        );

        $response = $this->client->getResponse();

        $this->assertIsJsonapiResponse($response);
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode(), $response->getContent());
    }

    public function testDeleteReturnsBadRequestForEmptyRequestBody()
    {
        $this->client->request(
            'DELETE',
            sprintf('/api/user/%d/relationships/tags', $this->getUser()->getId()),
            [
                'data' => [],
            ]
        );

        $response = $this->client->getResponse();

        $this->assertIsJsonapiResponse($response);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @dataProvider getInvalidData
     */
    public function testDeleteReturnsBadRequestForInvalidJsonRequestBody(array $data)
    {
        $this->client->request(
            'DELETE',
            sprintf('/api/user/%d/relationships/tags', $this->getUser()->getId()),
            ['data' => [$data]]
        );

        $response = $this->client->getResponse();

        $this->assertIsJsonapiResponse($response);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    private function getUser(): User
    {
        if (null !== $this->user) {
            return $this->user;
        }

        $this->user = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class)->findOneBy(
            ['email' => 'user-with-tag@ghosap.com']
        );

        return $this->getUser();
    }

    private function getTags(): array
    {
        $tagOne = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(Tag::class)->find(1);
        $this->assertNotNull($tagOne);
        $tagTwo = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(Tag::class)->find(2);
        $this->assertNotNull($tagTwo);
        $tagThree = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(Tag::class)->find(3);
        $this->assertNotNull($tagThree);

        return [$tagOne, $tagTwo, $tagThree];
    }
}
