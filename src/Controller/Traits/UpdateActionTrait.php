<?php

namespace Trikoder\JsonApiBundle\Controller\Traits;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UpdateActionTrait
 */
trait UpdateActionTrait
{
    use Actions\UpdateTrait;

    /**
     * @Route("/{id}{trailingSlash}", requirements={"trailingSlash": "[/]{0,1}"}, defaults={"trailingSlash": ""}, methods={"PATCH", "PUT", "POST"})
     *
     * @return object
     */
    public function updateAction(Request $request, $id)
    {
        $this->evaluateRequiredRole($this->getJsonApiConfig()->getUpdate()->getUpdateRequiredRoles());

        return $this->updateRequestFromRequest($request, $id);
    }
}
