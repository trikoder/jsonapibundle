<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\JsonApi\Schema\Test;

use Neomerx\JsonApi\Contracts\Schema\SchemaFactoryInterface;
use Trikoder\JsonApiBundle\Schema\AbstractSchema;
use Trikoder\JsonApiBundle\Tests\Resources\Security\AuthenticatedUserProvider;

class PrivateServiceSchema extends AbstractSchema
{
    protected $resourceType = 'invalid';

    /**
     * @var AuthenticatedUserProvider
     */
    private $userProvider;

    public function __construct(SchemaFactoryInterface $factory, AuthenticatedUserProvider $userProvider)
    {
        parent::__construct($factory);
        $this->userProvider = $userProvider;
    }

    /**
     * Get resource identity.
     *
     * @param object $resource
     *
     * @return string
     */
    public function getId($resource)
    {
        return $resource->getId();
    }

    /**
     * Get resource attributes.
     *
     * @param object $resource
     *
     * @return array
     */
    public function getAttributes($resource)
    {
        return [
            'username' => $this->userProvider->getUser()->getUsername(),
        ];
    }
}
