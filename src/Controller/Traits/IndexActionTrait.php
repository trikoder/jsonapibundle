<?php

namespace Trikoder\JsonApiBundle\Controller\Traits;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class IndexActionTrait
 */
trait IndexActionTrait
{
    use Actions\IndexTrait;

    /**
     * @Route("{trailingSlash}", requirements={"trailingSlash": "[/]{0,1}"}, defaults={"trailingSlash": ""}, methods={"GET"})
     *
     * @return array|\Trikoder\JsonApiBundle\Contracts\ObjectListCollectionInterface|null
     */
    public function indexAction(Request $request)
    {
        $this->evaluateRequiredRole($this->getJsonApiConfig()->getIndex()->getIndexRequiredRoles());

        return $this->createCollectionFromRequest($request);
    }
}
