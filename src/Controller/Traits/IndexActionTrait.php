<?php

namespace Trikoder\JsonApiBundle\Controller\Traits;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Trikoder\JsonApiBundle\Controller\Traits\Actions;

/**
 * Class IndexActionTrait
 * @package Trikoder\JsonApiBundle\Controller\Traits
 */
trait IndexActionTrait
{
    use Actions\IndexTrait;

    /**
     * @param Request $request
     *
     * @Route("{trailingSlash}", requirements={"trailingSlash" = "[/]{0,1}"}, defaults={"trailingSlash" = ""})
     * @Method("GET")
     * @return array|null|\Trikoder\JsonApiBundle\Contracts\ObjectListCollectionInterface
     */
    public function indexAction(Request $request)
    {
        return $this->createCollectionFromRequest($request);
    }
}
