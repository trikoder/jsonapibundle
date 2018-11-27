<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\Controller\Api\User;

use Symfony\Component\Routing\Annotation\Route;
use Trikoder\JsonApiBundle\Config\Annotation as JsonApiConfig;
use Trikoder\JsonApiBundle\Controller\AbstractController as JsonApiController;
use Trikoder\JsonApiBundle\Controller\Traits\IndexActionTrait;
use Trikoder\JsonApiBundle\Controller\Traits\ShowActionTrait;

/**
 * @Route("/reduced-user-response")
 *
 * @JsonApiConfig\Config(
 *     modelClass="Trikoder\JsonApiBundle\Tests\Resources\Entity\User",
 *     index=@JsonApiConfig\IndexConfig(
 *         allowedSortFields={"email", "id"},
 *         allowedFilteringParameters={"email", "id"},
 *         allowedFields={"user": {"email"}}
 *     )
 * )
 */
class ReducedResponseUserController extends JsonApiController
{
    use IndexActionTrait;
    use ShowActionTrait;
}
