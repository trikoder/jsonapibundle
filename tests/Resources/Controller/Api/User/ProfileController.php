<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\Controller\Api\User;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Trikoder\JsonApiBundle\Config\Annotation as JsonApiConfig;
use Trikoder\JsonApiBundle\Controller\AbstractController as JsonApiController;
use Trikoder\JsonApiBundle\Controller\Traits\Actions\IndexTrait;
use Trikoder\JsonApiBundle\Controller\Traits\Actions\UpdateTrait;

/**
 * @Route("/profile")
 *
 * @JsonApiConfig\Config(
 *     modelClass="Trikoder\JsonApiBundle\Tests\Resources\Entity\User",
 *     @JsonApiConfig\UpdateConfig(
 *         allowedFields={"email"}
 *     )
 * )
 */
class ProfileController extends JsonApiController
{
    use IndexTrait;
    use UpdateTrait;

    /**
     * @Route(methods={"GET"})
     */
    public function showAction(Request $request)
    {
        return $this->getUser();
    }

    /**
     * @Route(methods={"POST", "PUT", "PATCH"})
     */
    public function updateAction(Request $request)
    {
        $user = $this->getUser();

        return $this->updateModelFromRequestUsingModel($request, $user);
    }
}
