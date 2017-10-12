<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\JsonApi;

use Trikoder\JsonApiBundle\Tests\Resources\Entity\Post;
use Trikoder\JsonApiBundle\Tests\Resources\JsonApi\Schema\PostSchema;
use Trikoder\JsonApiBundle\Tests\Resources\JsonApi\Schema\SimpleFileSchema;
use Trikoder\JsonApiBundle\Tests\Resources\JsonApi\Schema\UserSchema;
use Trikoder\JsonApiBundle\Tests\Resources\Entity\User;
use Trikoder\JsonApiBundle\Contracts\SchemaClassMapProviderInterface;
use Trikoder\JsonApiBundle\Services\AbstractSchemaClassMapService;
use Trikoder\JsonApiBundle\Tests\Resources\Model\SimpleFileModel;

/**
 * Class SchemaClassMapService
 * @package Trikoder\JsonApiBundle\Tests\Resources\JsonApi
 */
class SchemaClassMapService extends AbstractSchemaClassMapService implements SchemaClassMapProviderInterface
{
    public function __construct()
    {
        $this->add(User::class, UserSchema::class);
        $this->add(SimpleFileModel::class, SimpleFileSchema::class);
        $this->add(Post::class, PostSchema::class);
    }
}
