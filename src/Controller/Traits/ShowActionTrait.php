<?php

namespace Trikoder\JsonApiBundle\Controller\Traits;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ShowActionTrait
 */
trait ShowActionTrait
{
    use Actions\ShowTrait;

    /**
     * @param Request $request
     *
     * @Route("/{id}{trailingSlash}", requirements={"trailingSlash": "[/]{0,1}"}, defaults={"trailingSlash": ""})
     * @Method("GET")
     *
     * @return null|object
     */
    public function showAction(Request $request, $id)
    {
        return $this->getModelById($id);
    }
}
