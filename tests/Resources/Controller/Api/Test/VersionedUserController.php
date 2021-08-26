<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\Controller\Api\Test;

use Symfony\Component\Routing\Annotation\Route;
use Trikoder\JsonApiBundle\Config\Annotation as JsonApiConfig;
use Trikoder\JsonApiBundle\Controller\AbstractController as JsonApiController;
use Trikoder\JsonApiBundle\Controller\Traits\CreateActionTrait;
use Trikoder\JsonApiBundle\Controller\Traits\DeleteActionTrait;
use Trikoder\JsonApiBundle\Controller\Traits\IndexActionTrait;
use Trikoder\JsonApiBundle\Controller\Traits\ShowActionTrait;
use Trikoder\JsonApiBundle\Controller\Traits\UpdateActionTrait;

/**
 * @Route(path="/v{api_version}/user",
 *     requirements={"api_version": "2"}
 * )
 *
 * @JsonApiConfig\Config(
 *     modelClass="Trikoder\JsonApiBundle\Tests\Resources\Entity\User"
 * )
 */
class VersionedUserController extends JsonApiController
{
    use CreateActionTrait;
    use DeleteActionTrait;
    use IndexActionTrait;
    use ShowActionTrait;
    use UpdateActionTrait;
}
