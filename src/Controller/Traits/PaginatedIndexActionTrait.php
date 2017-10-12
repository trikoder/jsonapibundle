<?php

namespace Trikoder\JsonApiBundle\Controller\Traits;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Trikoder\JsonApiBundle\Controller\Traits\Actions\IndexTrait;
use Trikoder\JsonApiBundle\Response\DataResponse;

/**
 * Class PaginatedIndexActionTrait
 * @package Trikoder\JsonApiBundle\Controller\Traits
 */
trait PaginatedIndexActionTrait
{
    use IndexTrait;

    /**
     * @param Request $request
     *
     * @Route("{trailingSlash}", requirements={"trailingSlash" = "[/]{0,1}"}, defaults={"trailingSlash" = ""})
     * @Method("GET")
     * @return DataResponse
     */
    public function indexAction(Request $request)
    {
        $router = $this->get('router');

        $collection = $this->createCollectionFromRequest($request);

        return $this->createPaginatedDataResponseFromCollectionAndRequest($collection, $request, $router);
    }
}
