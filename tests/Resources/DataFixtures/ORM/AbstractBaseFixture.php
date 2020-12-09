<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture as BaseAbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractBaseFixture extends BaseAbstractFixture implements ContainerAwareInterface
{
    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @return ObjectManager
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @param ObjectManager $manager
     */
    public function setManager($manager)
    {
        $this->manager = $manager;
    }

    /**
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
