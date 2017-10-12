<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\Controller\Api\User;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Trikoder\JsonApiBundle\Controller\AbstractController as JsonApiController;
use Trikoder\JsonApiBundle\Controller\Traits\Actions\IndexTrait;
use Trikoder\JsonApiBundle\Controller\Traits\Actions\UpdateTrait;
use Trikoder\JsonApiBundle\Controller\Traits\CreateActionTrait;
use Trikoder\JsonApiBundle\Controller\Traits\DeleteActionTrait;
use Trikoder\JsonApiBundle\Controller\Traits\IndexActionTrait;
use Trikoder\JsonApiBundle\Controller\Traits\ShowActionTrait;
use Trikoder\JsonApiBundle\Controller\Traits\UpdateActionTrait;
use Trikoder\JsonApiBundle\Config\Annotation as JsonApiConfig;

/**
 * @Route("/profile")
 *
 * @JsonApiConfig\Config(
 *     modelClass="Trikoder\JsonApiBundle\Tests\Resources\Entity\User",
 *     @JsonApiConfig\UpdateConfig(
 *          allowedFields={"email"}
 *     )
 * )
 *
 */
class UserController extends JsonApiController
{
    use IndexTrait;
    use UpdateTrait;

    /**
     * @Route()
     * @Method({"GET"})
     *
     * @param Request $request
     * @return mixed
     */
    public function showAction(Request $request)
    {
        return $this->getUser();
    }

    /**
     * @Route()
     * @Method({"POST", "PUT", "PATCH"})
     *
     * @param Request $request
     * @return mixed
     */
    public function updateAction(Request $request)
    {
        $user = $this->getUser();

        return $this->updateModelFromRequestUsingModel($request, $user);
    }
}
