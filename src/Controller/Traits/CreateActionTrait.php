<?php

namespace Trikoder\JsonApiBundle\Controller\Traits;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Trikoder\JsonApiBundle\Controller\Traits\Actions;

/**
 * Class CreateActionTrait
 * @package Trikoder\JsonApiBundle\Controller\Traits
 */
trait CreateActionTrait
{
    use Actions\CreateTrait;

    /**
     * @param Request $request
     *
     * @Route("{trailingSlash}", requirements={"trailingSlash" = "[/]{0,1}"}, defaults={"trailingSlash" = ""})
     * @Method("POST")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        return $this->createCreatedFromRequest($request);
    }
}
