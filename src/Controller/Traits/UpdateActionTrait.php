<?php

namespace Trikoder\JsonApiBundle\Controller\Traits;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UpdateActionTrait
 */
trait UpdateActionTrait
{
    use Actions\UpdateTrait;

    /**
     * @param Request $request
     *
     * @Route("/{id}{trailingSlash}", requirements={"trailingSlash": "[/]{0,1}"}, defaults={"trailingSlash": ""})
     * @Method({"PATCH", "PUT"})
     *
     * @return object
     */
    public function updateAction(Request $request, $id)
    {
        return $this->updateRequestFromRequest($request, $id);
    }
}
