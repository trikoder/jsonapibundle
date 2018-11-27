<?php

namespace Trikoder\JsonApiBundle\Controller\Traits;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Trikoder\JsonApiBundle\Controller\Traits\Actions\IndexTrait;
use Trikoder\JsonApiBundle\Response\DataResponse;

/**
 * Class PaginatedIndexActionTrait
 */
trait PaginatedIndexActionTrait
{
    use IndexTrait;

    /**
     * @Route("{trailingSlash}", requirements={"trailingSlash": "[/]{0,1}"}, defaults={"trailingSlash": ""}, methods={"GET"})
     *
     * @return DataResponse
     */
    public function indexAction(Request $request)
    {
        $this->evaluateRequiredRole($this->getJsonApiConfig()->getIndex()->getIndexRequiredRoles());

        // TODO change to injected
        $router = $this->getRouter();

        $collection = $this->createCollectionFromRequest($request);

        return $this->createPaginatedDataResponseFromCollectionAndRequest($collection, $request, $router);
    }
}
