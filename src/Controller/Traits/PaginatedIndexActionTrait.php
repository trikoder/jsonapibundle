<?php

namespace Trikoder\JsonApiBundle\Controller\Traits;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PaginatedIndexActionTrait
 * @package Trikoder\JsonApiBundle\Controller\Traits
 */
trait PaginatedIndexActionTrait
{
    use IndexActionTrait {
        IndexActionTrait::indexAction as parentIndexAction;
    }

    /**
     * @param Request $request
     *
     * @Route("/")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $collection = $this->parentIndexAction($request);

        // TODO instead of return collection, encode and create new response with pagination links included

        return $collection;
    }
}