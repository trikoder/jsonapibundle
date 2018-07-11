<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\Controller\Api\User;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Trikoder\JsonApiBundle\Config\Annotation as JsonApiConfig;
use Trikoder\JsonApiBundle\Controller\AbstractController as JsonApiController;
use Trikoder\JsonApiBundle\Controller\Traits\CreateActionTrait;
use Trikoder\JsonApiBundle\Controller\Traits\IndexActionTrait;
use Trikoder\JsonApiBundle\Controller\Traits\ShowActionTrait;
use Trikoder\JsonApiBundle\Controller\Traits\UpdateActionTrait;

/**
 * @Route("/posts")
 *
 * @JsonApiConfig\Config(
 *     modelClass="Trikoder\JsonApiBundle\Tests\Resources\Entity\Post",
 * )
 */
class PostController extends JsonApiController
{
    use IndexActionTrait;
    use ShowActionTrait;
    use CreateActionTrait;
    use UpdateActionTrait;
}
