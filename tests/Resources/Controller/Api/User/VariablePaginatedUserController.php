<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\Controller\Api\User;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Trikoder\JsonApiBundle\Controller\AbstractController as JsonApiController;
use Trikoder\JsonApiBundle\Controller\Traits\PaginatedIndexActionTrait;
use Trikoder\JsonApiBundle\Config\Annotation as JsonApiConfig;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
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
 *
 */
class VariablePaginatedUserController extends JsonApiController
{
    use PaginatedIndexActionTrait;

    /**
     * @param Request $request
     *
     * @Route("/{number}{trailingSlash}", requirements={"trailingSlash" = "[/]{0,1}"}, defaults={"trailingSlash" = ""})
     * @Method("GET")
     * @return DataResponse
     */
    public function variableEndAction(Request $request, $number)
    {
        $router = $this->get('router');

        $collection = $this->createCollectionFromRequest($request);

        return $this->createPaginatedDataResponseFromCollectionAndRequest($collection, $request, $router);
    }
}
