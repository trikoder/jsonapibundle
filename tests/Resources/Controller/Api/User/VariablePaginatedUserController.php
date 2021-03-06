<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\Controller\Api\User;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Trikoder\JsonApiBundle\Config\Annotation as JsonApiConfig;
use Trikoder\JsonApiBundle\Controller\AbstractController as JsonApiController;
use Trikoder\JsonApiBundle\Controller\Traits\PaginatedIndexActionTrait;
use Trikoder\JsonApiBundle\Response\DataResponse;

/**
 * @Route("/user-{variable}-paginated")
 *
 * @JsonApiConfig\Config(
 *     modelClass="Trikoder\JsonApiBundle\Tests\Resources\Entity\User",
 *     index=@JsonApiConfig\IndexConfig(
 *         allowedSortFields={"email", "id"},
 *         allowedFilteringParameters={"email", "id"},
 *         defaultPagination={"limit": 2}
 *     )
 * )
 */
class VariablePaginatedUserController extends JsonApiController
{
    use PaginatedIndexActionTrait;

    /**
     * @Route("/{number}{trailingSlash}", requirements={"trailingSlash": "[/]{0,1}"}, defaults={"trailingSlash": ""}, methods={"GET"})
     *
     * @return DataResponse
     */
    public function variableEndAction(Request $request, $number)
    {
        // TODO change to injected
        $router = $this->get('router');

        $collection = $this->createCollectionFromRequest($request);

        return $this->createPaginatedDataResponseFromCollectionAndRequest($collection, $request, $router);
    }
}
