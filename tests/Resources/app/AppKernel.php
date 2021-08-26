<?php

use Doctrine\Common\Persistence\ObjectManager as LegacyObjectManager;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            // symfony
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            // dev
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\DebugBundle\DebugBundle(),
            new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\WebServerBundle\WebServerBundle(),

            // the bundle
            new Trikoder\JsonApiBundle\TrikoderJsonApiBundle(),

            // dummy bundle to enable cleaner loading of test controllers etc
            new Trikoder\JsonApiBundle\Tests\Resources\TrikoderJsonApiTestsBundle(),
        ];

        return $bundles;
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config/config.yml');
    }

    public function boot()
    {
        if (!$this->booted && !interface_exists(LegacyObjectManager::class)) {
            class_alias(ObjectManager::class, LegacyObjectManager::class);
        }
        parent::boot();
    }
}
