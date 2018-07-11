<?php

namespace Trikoder\JsonApiBundle\Controller\Traits;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Trikoder\JsonApiBundle\Controller\Traits\Actions\IndexTrait;
use Trikoder\JsonApiBundle\Response\DataResponse;

/**
 * Class PaginatedIndexActionTrait
 */
trait PaginatedIndexActionTrait
{
    use IndexTrait;

    /**
     * @param Request $request
     *
     * @Route("{trailingSlash}", requirements={"trailingSlash": "[/]{0,1}"}, defaults={"trailingSlash": ""})
     * @Method("GET")
     *
     * @return DataResponse
     */
    public function indexAction(Request $request)
    {
        // TODO change to injected
        $router = $this->getRouter();

        $collection = $this->createCollectionFromRequest($request);

        return $this->createPaginatedDataResponseFromCollectionAndRequest($collection, $request, $router);
    }
}
