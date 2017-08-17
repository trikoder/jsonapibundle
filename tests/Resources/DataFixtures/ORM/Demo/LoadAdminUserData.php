<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\DataFixtures\ORM\Demo;

use Doctrine\Common\Persistence\ObjectManager;
use Trikoder\JsonApiBundle\Tests\Resources\DataFixtures\ORM\AbstractBaseFixture;
use Trikoder\JsonApiBundle\Tests\Resources\Entity\User;

class LoadAdminUserData extends AbstractBaseFixture
{

    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i < 3; $i++) {
            // cms users
            $userAdmin = new User();
            $userAdmin->setEmail('demo.admin' . $i . '@ghosap.com');

            $manager->persist($userAdmin);
            $this->addReference('admin-user-' . $i, $userAdmin);
        }
        for ($i = 1; $i < 3; $i++) {
            // customer user
            $userAdmin = new User();
            $userAdmin->setEmail('demo.customer' . $i . '@ghosap.com');
            $userAdmin->setCustomer(true);

            $manager->persist($userAdmin);
            $this->addReference('customer-user-' . $i, $userAdmin);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 100;
    }
}
