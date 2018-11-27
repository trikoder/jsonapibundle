<?php

namespace Trikoder\JsonApiBundle\Controller\Traits;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DeleteActionTrait
 */
trait DeleteActionTrait
{
    use Actions\DeleteTrait;

    /**
     * @Route("/{id}{trailingSlash}", requirements={"trailingSlash": "[/]{0,1}"}, defaults={"trailingSlash": ""}, methods={"DELETE"})
     */
    public function deleteAction(Request $request, $id)
    {
        $this->evaluateRequiredRole($this->getJsonApiConfig()->getDelete()->getDeleteRequiredRoles());

        $this->deleteModelById($id);

        return null;
    }
}
