<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\Controller\Api\User;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Trikoder\JsonApiBundle\Controller\AbstractController as JsonApiController;
use Trikoder\JsonApiBundle\Controller\Traits\CreateActionTrait;
use Trikoder\JsonApiBundle\Controller\Traits\DeleteActionTrait;
use Trikoder\JsonApiBundle\Controller\Traits\IndexActionTrait;
use Trikoder\JsonApiBundle\Controller\Traits\ShowActionTrait;
use Trikoder\JsonApiBundle\Controller\Traits\UpdateActionTrait;
use Trikoder\JsonApiBundle\Config\Annotation as JsonApiConfig;

/**
 * @Route("/user")
 *
 * @JsonApiConfig\Config(
 *     modelClass="Trikoder\JsonApiBundle\Tests\Resources\Entity\User",
 *     index=@JsonApiConfig\IndexConfig(
 *         allowedSortFields={"email", "id"},
 *         allowedFilteringParameters={"email", "id"}
 *     )
 * )
 *
 */
class UserController extends JsonApiController
{
    use IndexActionTrait;
    use ShowActionTrait;
    use CreateActionTrait;
    use UpdateActionTrait;
    use DeleteActionTrait;
}
