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
 * @Route("/user-config-restrictions")
 *
 * @JsonApiConfig\Config(
 *     modelClass="Trikoder\JsonApiBundle\Tests\Resources\Entity\User",
 *     index=@JsonApiConfig\IndexConfig(
 *         allowedSortFields={"email", "id"},
 *         allowedFilteringParameters={"email", "id"},
 *         allowedFields={"user": {"email"}}
 *     ),
 *     update=@JsonApiConfig\UpdateConfig(
 *         allowedFields={"email"}
 *     ),
 *     create=@JsonApiConfig\CreateConfig(
 *         allowedFields={"email"}
 *     )
 * )
 */
class ConfigRestrictionsUserController extends JsonApiController
{
    use CreateActionTrait;
    use IndexActionTrait;
    use ShowActionTrait;
    use UpdateActionTrait;
}
