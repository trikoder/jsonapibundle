<?php

namespace Trikoder\JsonApiBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Trikoder\JsonApiBundle\CompilerPass\SchemaAutoMapCompilerPass;
use Trikoder\JsonApiBundle\Controller\AbstractController;

class TrikoderJsonApiBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new SchemaAutoMapCompilerPass());
        $container->registerForAutoconfiguration(AbstractController::class)
            ->addTag('controller.service_arguments')
        ;
    }
}
