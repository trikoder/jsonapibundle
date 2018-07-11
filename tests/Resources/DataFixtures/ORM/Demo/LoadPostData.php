<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\DataFixtures\ORM\Demo;

use Doctrine\Common\Persistence\ObjectManager;
use Trikoder\JsonApiBundle\Tests\Resources\DataFixtures\ORM\AbstractBaseFixture;
use Trikoder\JsonApiBundle\Tests\Resources\Entity\Post;
use Trikoder\JsonApiBundle\Tests\Resources\Entity\User;

class LoadPostData extends AbstractBaseFixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i < 10; ++$i) {
            // post
            $post = new Post();
            $post->setTitle('Post ' . $i);
            $post->setAuthor($this->getReference('admin-user-' . (($i % 2) + 1)));

            $manager->persist($post);
            $this->addReference('post-' . $i, $post);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 200;
    }
}
