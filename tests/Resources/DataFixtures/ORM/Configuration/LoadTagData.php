<?php

declare(strict_types=1);

namespace Trikoder\JsonApiBundle\Tests\Resources\DataFixtures\ORM\Configuration;

use Doctrine\Common\Persistence\ObjectManager;
use Trikoder\JsonApiBundle\Tests\Resources\DataFixtures\ORM\AbstractBaseFixture;
use Trikoder\JsonApiBundle\Tests\Resources\Entity\Tag;

final class LoadTagData extends AbstractBaseFixture
{
    public function load(ObjectManager $manager)
    {
        $tag = new Tag();
        $this->addReference('tag-1', $tag);
        $manager->persist($tag);

        $tag = new Tag();
        $this->addReference('tag-2', $tag);
        $manager->persist($tag);

        $tag = new Tag();
        $this->addReference('tag-3', $tag);
        $manager->persist($tag);

        $manager->flush();
    }

    public function getOrder()
    {
        return 13;
    }
}
