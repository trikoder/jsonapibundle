<?php

namespace Trikoder\JsonApiBundle\Tests\Controller\Api;

use Symfony\Component\Routing\Annotation\Route;
use Trikoder\JsonApiBundle\Config\Annotation as JsonApiConfig;
use Trikoder\JsonApiBundle\Controller\AbstractController as JsonApiController;
use Trikoder\JsonApiBundle\Controller\Traits\CreateActionTrait;
use Trikoder\JsonApiBundle\Controller\Traits\DeleteActionTrait;
use Trikoder\JsonApiBundle\Controller\Traits\IndexActionTrait;
use Trikoder\JsonApiBundle\Controller\Traits\ShowActionTrait;
use Trikoder\JsonApiBundle\Controller\Traits\UpdateActionTrait;

/**
 * @Route("/example")
 *
 * @JsonApiConfig\Config(
 *     modelClass="\modelClass"
 * )
 */
class ExampleController extends JsonApiController
{
    use IndexActionTrait;
    use ShowActionTrait;
    use CreateActionTrait;
    use UpdateActionTrait;
    use DeleteActionTrait;
}
