<?php

namespace Trikoder\JsonApiBundle\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Trikoder\JsonApiBundle\Services\Client\ResponseBodyDecoder;
use Trikoder\JsonApiBundle\Services\SchemaClassMapService;
use Trikoder\JsonApiBundle\Tests\Resources\Entity\Cart;
use Trikoder\JsonApiBundle\Tests\Resources\Entity\Post;
use Trikoder\JsonApiBundle\Tests\Resources\Entity\Product;
use Trikoder\JsonApiBundle\Tests\Resources\Entity\User;
use Trikoder\JsonApiBundle\Tests\Resources\JsonApi\Schema\CartSchema;
use Trikoder\JsonApiBundle\Tests\Resources\JsonApi\Schema\PostSchema;
use Trikoder\JsonApiBundle\Tests\Resources\JsonApi\Schema\ProductSchema;
use Trikoder\JsonApiBundle\Tests\Resources\JsonApi\Schema\UserSchema;

class ResponseBodyDecoderTest extends KernelTestCase
{
    public function testPostJsonPayload()
    {
        $kernel = static::bootKernel();

        $payload = json_encode([
            'data' => [
                'type' => 'post',
                'id' => '1',
                'attributes' => [
                    'title' => 'Post 1',
                    'active' => true,
                ],
                'relationships' => [
                    'author' => [
                        'data' => [
                            'type' => 'user',
                            'id' => 3,
                        ],
                    ],
                ],
                'links' => [
                    'self' => '/post/1',
                ],
            ],
            'included' => [
                [
                    'type' => 'user',
                    'id' => '3',
                    'attributes' => [
                        'email' => 'author@example.com',
                        'active' => true,
                    ],
                ],
            ],
        ]);

        /** @var Post $post */
        $post = $this->getResponseBodyDecoder()->decode($payload);

        $this->assertInstanceOf(Post::class, $post);
        $this->assertEquals(1, $post->getId());
        $this->assertEquals('Post 1', $post->getTitle());
        $this->assertTrue($post->isActive());

        $this->assertInstanceOf(User::class, $post->getAuthor());
        $this->assertEquals(3, $post->getAuthor()->getId());
        $this->assertEquals('author@example.com', $post->getAuthor()->getEmail());
        $this->assertTrue($post->getAuthor()->isActive());
        $this->assertFalse($post->getAuthor()->isCustomer());
    }

    public function testCartJsonPayload()
    {
        $kernel = static::bootKernel();

        $payload = json_encode([
            'data' => [
                'type' => 'cart',
                'id' => '1306',
                'relationships' => [
                    'user' => [
                        'data' => [
                            'type' => 'user',
                            'id' => 1,
                        ],
                    ],
                    'products' => [
                        'data' => [
                            [
                                'type' => 'product',
                                'id' => 1,
                            ],
                            [
                                'type' => 'product',
                                'id' => 56,
                            ],
                            [
                                'type' => 'product',
                                'id' => 7,
                            ],
                        ],
                    ],
                ],
                'links' => [
                    'self' => '/cart/1',
                ],
            ],
            'included' => [
                [
                    'type' => 'user',
                    'id' => '3',
                    'attributes' => [
                        'email' => 'author3@example.com',
                        'active' => true,
                    ],
                ],
                [
                    'type' => 'user',
                    'id' => '1',
                    'attributes' => [
                        'email' => 'author1@example.com',
                        'active' => false,
                    ],
                ],
                [
                    'type' => 'product',
                    'id' => '1',
                    'attributes' => [
                        'title' => 'Product 1',
                    ],
                ],
                [
                    'type' => 'product',
                    'id' => '2',
                    'attributes' => [
                        'title' => 'Product 2',
                    ],
                ],
                [
                    'type' => 'product',
                    'id' => '56',
                    'attributes' => [
                        'title' => 'Product 56',
                    ],
                ],
            ],
        ]);

        /** @var Cart $cart */
        $cart = $this->getResponseBodyDecoder()->decode($payload);

        $this->assertInstanceOf(Cart::class, $cart);
        $this->assertEquals(1306, $cart->getId());

        $this->assertInstanceOf(User::class, $cart->getUser());
        $this->assertEquals(1, $cart->getUser()->getId());
        $this->assertEquals('author1@example.com', $cart->getUser()->getEmail());
        $this->assertFalse($cart->getUser()->isActive());
        $this->assertFalse($cart->getUser()->isCustomer());

        $products = $cart->getProducts();
        $this->assertCount(3, $products);

        /** @var Product $product */
        $product = $products[0];
        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals('Product 1', $product->getTitle());

        /** @var Product $product */
        $product = $products[1];
        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals('Product 56', $product->getTitle());

        /** @var Product $product */
        $product = $products[2];
        $this->assertInstanceOf(Product::class, $product);
        $this->assertEmpty($product->getTitle());
    }

    public function testMultipleResourceJsonPayload()
    {
        $kernel = static::bootKernel();

        $payload = json_encode([
            'data' => [
                [
                    'type' => 'post',
                    'id' => '1',
                    'attributes' => [
                        'title' => 'Post 1',
                        'active' => true,
                    ],
                    'relationships' => [
                        'author' => [
                            'data' => [
                                'type' => 'user',
                                'id' => 3,
                            ],
                        ],
                    ],
                ],
                [
                    'type' => 'post',
                    'id' => '6',
                    'attributes' => [
                        'title' => 'Post 6',
                    ],
                ],
                [
                    'type' => 'cart',
                    'id' => '1306',
                    'relationships' => [
                        'user' => [
                            'data' => [
                                'type' => 'user',
                                'id' => 1,
                            ],
                        ],
                        'products' => [
                            'data' => [
                                [
                                    'type' => 'product',
                                    'id' => 1,
                                ],
                                [
                                    'type' => 'product',
                                    'id' => 56,
                                ],
                                [
                                    'type' => 'product',
                                    'id' => 7,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'included' => [
                [
                    'type' => 'user',
                    'id' => '3',
                    'attributes' => [
                        'email' => 'author@example.com',
                        'active' => true,
                    ],
                ],
            ],
        ]);

        $result = $this->getResponseBodyDecoder()->decode($payload);

        /** @var Post $post */
        $post = $result[0];
        $this->assertInstanceOf(Post::class, $post);
        $this->assertEquals(1, $post->getId());
        $this->assertEquals('Post 1', $post->getTitle());
        $this->assertTrue($post->isActive());
        $this->assertInstanceOf(User::class, $post->getAuthor());
        $this->assertEquals(3, $post->getAuthor()->getId());
        $this->assertEquals('author@example.com', $post->getAuthor()->getEmail());
        $this->assertTrue($post->getAuthor()->isActive());
        $this->assertFalse($post->getAuthor()->isCustomer());

        /** @var Post $post */
        $post = $result[1];
        $this->assertInstanceOf(Post::class, $post);
        $this->assertEquals(6, $post->getId());
        $this->assertEquals('Post 6', $post->getTitle());
        $this->assertFalse($post->isActive());
        $this->assertNull($post->getAuthor());

        /** @var Cart $cart */
        $cart = $result[2];

        $this->assertInstanceOf(Cart::class, $cart);
        $this->assertEquals(1306, $cart->getId());

        $this->assertInstanceOf(User::class, $cart->getUser());
        $this->assertEquals(1, $cart->getUser()->getId());

        $products = $cart->getProducts();
        $this->assertCount(3, $products);

        /** @var Product $product */
        $product = $products[0];
        $this->assertInstanceOf(Product::class, $product);

        /** @var Product $product */
        $product = $products[1];
        $this->assertInstanceOf(Product::class, $product);

        /** @var Product $product */
        $product = $products[2];
        $this->assertInstanceOf(Product::class, $product);
    }

    public function testJsonPayloadWithEmptyData()
    {
        $kernel = static::bootKernel();

        $payload = json_encode([
            'data' => [],
        ]);

        $resources = $this->getResponseBodyDecoder()->decode($payload);

        $this->assertInternalType('array', $resources);
        $this->assertEmpty($resources);
    }

    public function testJsonPayloadWithNullData()
    {
        $kernel = static::bootKernel();

        $payload = json_encode([
            'data' => null,
        ]);

        $resource = $this->getResponseBodyDecoder()->decode($payload);

        $this->assertNull($resource);
    }

    private function getResponseBodyDecoder(): ResponseBodyDecoder
    {
        return new ResponseBodyDecoder(static::$kernel->getContainer()->get('trikoder.jsonapi.factory'), $this->getSchemaClassMap());
    }

    private function getSchemaClassMap()
    {
        $classMap = new SchemaClassMapService();

        $classMap->add(User::class, UserSchema::class);
        $classMap->add(Post::class, PostSchema::class);
        $classMap->add(Product::class, ProductSchema::class);
        $classMap->add(Cart::class, CartSchema::class);

        return $classMap;
    }
}
