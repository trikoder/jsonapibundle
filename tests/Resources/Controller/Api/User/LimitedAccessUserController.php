<?php

declare(strict_types=1);

namespace Trikoder\JsonApiBundle\Tests\Resources\Controller\Api\User;

use Symfony\Component\Routing\Annotation\Route;
use Trikoder\JsonApiBundle\Config\Annotation as JsonApiConfig;
use Trikoder\JsonApiBundle\Controller\AbstractController as JsonApiController;
use Trikoder\JsonApiBundle\Controller\Traits\CreateActionTrait;
use Trikoder\JsonApiBundle\Controller\Traits\DeleteActionTrait;
use Trikoder\JsonApiBundle\Controller\Traits\IndexActionTrait;
use Trikoder\JsonApiBundle\Controller\Traits\ShowActionTrait;
use Trikoder\JsonApiBundle\Controller\Traits\UpdateActionTrait;

/**
 * @Route("/user-limited-access")
 *
 * @JsonApiConfig\Config(
 *     modelClass="Trikoder\JsonApiBundle\Tests\Resources\Entity\User",
 *     index=@JsonApiConfig\IndexConfig(
 *         allowedSortFields={"email", "id"},
 *         allowedFilteringParameters={"email", "id"},
 *         requiredRoles={"ROLE_ADMIN"}
 *     ),
 *     create=@JsonApiConfig\CreateConfig(
 *         requiredRoles={"ROLE_ADMIN", "ROLE_USER"}
 *     ),
 *     delete=@JsonApiConfig\DeleteConfig(
 *         requiredRoles={"ROLE_USER"}
 *     ),
 *     update=@JsonApiConfig\UpdateConfig(
 *         requiredRoles={"ROLE_USER"}
 *     )
 * )
 */
final class LimitedAccessUserController extends JsonApiController
{
    use CreateActionTrait;
    use DeleteActionTrait;
    use IndexActionTrait;
    use ShowActionTrait;
    use UpdateActionTrait;
}
