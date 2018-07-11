<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\Controller\Api\User;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Trikoder\JsonApiBundle\Config\Annotation as JsonApiConfig;
use Trikoder\JsonApiBundle\Controller\AbstractController as JsonApiController;
use Trikoder\JsonApiBundle\Controller\Traits\IndexActionTrait;
use Trikoder\JsonApiBundle\Tests\Resources\Entity\Post;
use Trikoder\JsonApiBundle\Tests\Resources\JsonApi\Schema\Test\CrazySchema;

/**
 * @Route("/crazy-posts")
 *
 * @JsonApiConfig\Config(
 *     modelClass="Trikoder\JsonApiBundle\Tests\Resources\Entity\Post",
 * )
 */
class CrazyPostController extends JsonApiController
{
    use IndexActionTrait;

    /**
     * {@inheritdoc}
     */
    public function getSchemaClassMapProvider()
    {
        // replace one property
        $mapService = parent::getSchemaClassMapProvider();
        $mapService->add(Post::class, CrazySchema::class);

        return $mapService;
    }
}
