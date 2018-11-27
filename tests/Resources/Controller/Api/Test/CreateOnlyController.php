<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\Controller\Api\Test;

use Symfony\Component\Routing\Annotation\Route;
use Trikoder\JsonApiBundle\Config\Annotation as JsonApiConfig;
use Trikoder\JsonApiBundle\Controller\AbstractController as JsonApiController;
use Trikoder\JsonApiBundle\Controller\Traits\CreateActionTrait;

/**
 * @Route("/create-only")
 *
 * @JsonApiConfig\Config(
 *     modelClass="Trikoder\JsonApiBundle\Tests\Resources\Entity\User"
 * )
 */
class CreateOnlyController extends JsonApiController
{
    use CreateActionTrait;
}
