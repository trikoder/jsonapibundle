<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\Controller\Api\User;

use Symfony\Component\Routing\Annotation\Route;
use Trikoder\JsonApiBundle\Config\Annotation as JsonApiConfig;
use Trikoder\JsonApiBundle\Controller\AbstractController as JsonApiController;
use Trikoder\JsonApiBundle\Controller\Traits\IndexActionTrait;
use Trikoder\JsonApiBundle\Controller\Traits\ShowActionTrait;
use Trikoder\JsonApiBundle\Schema\Builtin\GenericSchema;
use Trikoder\JsonApiBundle\Tests\Resources\Entity\Post;
use Trikoder\JsonApiBundle\Tests\Resources\Entity\User;

/**
 * @Route("/generic-schema")
 *
 * @JsonApiConfig\Config(
 *     modelClass="Trikoder\JsonApiBundle\Tests\Resources\Entity\Post"
 * )
 */
class GenericSchemaController extends JsonApiController
{
    use IndexActionTrait;
    use ShowActionTrait;

    /**
     * {@inheritdoc}
     */
    public function getSchemaClassMapProvider()
    {
        // replace one property
        $mapService = parent::getSchemaClassMapProvider();
        $mapService->add(Post::class, GenericSchema::class);
        $mapService->add(User::class, GenericSchema::class);

        return $mapService;
    }
}
