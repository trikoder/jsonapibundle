<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\JsonApi;

use Trikoder\JsonApiBundle\Tests\Resources\JsonApi\Schema\UserSchema;
use Trikoder\JsonApiBundle\Tests\Resources\Entity\User;
use Trikoder\JsonApiBundle\Contracts\SchemaClassMapProviderInterface;
use Trikoder\JsonApiBundle\Services\AbstractSchemaClassMapService;

/**
 * Class SchemaClassMapService
 * @package Trikoder\JsonApiBundle\Tests\Resources\JsonApi
 */
class SchemaClassMapService extends AbstractSchemaClassMapService implements SchemaClassMapProviderInterface
{
    public function __construct()
    {
        $this->add(User::class, UserSchema::class);
    }
}