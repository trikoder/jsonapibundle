<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\Controller\Api\User;

use Symfony\Component\Routing\Annotation\Route;
use Trikoder\JsonApiBundle\Config\Annotation as JsonApiConfig;
use Trikoder\JsonApiBundle\Controller\AbstractController as JsonApiController;
use Trikoder\JsonApiBundle\Controller\Traits\CreateActionTrait;
use Trikoder\JsonApiBundle\Controller\Traits\IndexActionTrait;
use Trikoder\JsonApiBundle\Controller\Traits\ShowActionTrait;
use Trikoder\JsonApiBundle\Controller\Traits\UpdateActionTrait;

/**
 * @Route("/products")
 *
 * @JsonApiConfig\Config(
 *     modelClass="Trikoder\JsonApiBundle\Tests\Resources\Entity\Product",
 * )
 */
class ProductController extends JsonApiController
{
    use CreateActionTrait;
    use IndexActionTrait;
    use ShowActionTrait;
    use UpdateActionTrait;
}
