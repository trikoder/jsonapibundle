<?php

declare(strict_types=1);

namespace Trikoder\JsonApiBundle\Tests\Resources\DataFixtures\ORM\Configuration;

use Doctrine\Common\Persistence\ObjectManager;
use Trikoder\JsonApiBundle\Tests\Resources\DataFixtures\ORM\AbstractBaseFixture;
use Trikoder\JsonApiBundle\Tests\Resources\Entity\User;

final class LoadUserWithTag extends AbstractBaseFixture
{
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail('user-with-tag@ghosap.com');
        $user->addTag($this->getReference('tag-3'));

        $this->addReference('user-with-tag', $user);

        $manager->persist($user);
        $manager->flush();
    }

    public function getOrder()
    {
        return 15;
    }
}
