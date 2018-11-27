<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\Security;

use Symfony\Component\Security\Core\User\UserInterface;

interface AuthenticatedUserProviderInterface
{
    /**
     * Returns true if there is UserInterface object in the token storage
     */
    public function hasUser(): bool;

    /**
     */
    public function getUser(): UserInterface;
}
