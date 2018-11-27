<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\Controller\Api\User;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Trikoder\JsonApiBundle\Config\Annotation as JsonApiConfig;
use Trikoder\JsonApiBundle\Controller\AbstractController as JsonApiController;
use Trikoder\JsonApiBundle\Controller\Traits\IndexActionTrait;
use Trikoder\JsonApiBundle\Controller\Traits\ShowActionTrait;
use Trikoder\JsonApiBundle\Tests\Resources\Entity\User;
use Trikoder\JsonApiBundle\Tests\Resources\JsonApi\Schema\CustomerSchema;

/**
 * @Route("/customer")
 * @TODO - add role check
 *
 * @JsonApiConfig\Config(
 *     modelClass="Trikoder\JsonApiBundle\Tests\Resources\Entity\User",
 *     fixedFiltering={"customer": true},
 *     index=@JsonApiConfig\IndexConfig(
 *         allowedSortFields={"email"}
 *     )
 * )
 */
class CustomerController extends JsonApiController
{
    use IndexActionTrait;
    use ShowActionTrait;

    /**
     * {@inheritdoc}
     */
    public function getSchemaClassMapProvider()
    {
        // replace one property
        $mapService = parent::getSchemaClassMapProvider();
        $mapService->add(User::class, CustomerSchema::class);

        return $mapService;
    }

    /**
     * Dummy action - so we can try to generate route inside schema
     *
     *
     * @Route("/dummy-action/{id}", name="customer_dummy_action")
     */
    public function dummyAction(Request $request, $id)
    {
        return $this->showAction($request, $id);
    }
}
