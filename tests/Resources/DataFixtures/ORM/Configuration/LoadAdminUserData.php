<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\DataFixtures\ORM\Configuration;

use Doctrine\Common\Persistence\ObjectManager;
use Trikoder\JsonApiBundle\Tests\Resources\DataFixtures\ORM\AbstractBaseFixture;
use Trikoder\JsonApiBundle\Tests\Resources\Entity\User;

class LoadAdminUserData extends AbstractBaseFixture
{
    public function load(ObjectManager $manager)
    {
        // main user
        $userAdmin = new User();
        $userAdmin->setEmail('admin@ghosap.com');

        $manager->persist($userAdmin);
        $this->addReference('admin-user', $userAdmin);

        $manager->flush();
    }

    public function getOrder()
    {
        return 10;
    }
}
