<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\Security;

use RuntimeException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthenticatedUserProvider implements AuthenticatedUserProviderInterface
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Returns true if there is UserInterface object in the token storage
     */
    public function hasUser(): bool
    {
        return ($this->tokenStorage->getToken()->getUser() instanceof UserInterface) ? true : false;
    }

    /**
     */
    public function getUser(): UserInterface
    {
        if (false === $this->hasUser()) {
            throw new RuntimeException('No user to provide. Use hasUser to check if there is User object to provide!');
        }

        return $this->tokenStorage->getToken()->getUser();
    }
}
