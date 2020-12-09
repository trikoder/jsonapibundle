<?php

namespace Trikoder\JsonApiBundle\Controller\Traits;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Trikoder\JsonApiBundle\Response\CreatedResponse;

/**
 * Class CreateActionTrait
 */
trait CreateActionTrait
{
    use Actions\CreateTrait;

    /**
     * @Route("{trailingSlash}", requirements={"trailingSlash": "[/]{0,1}"}, defaults={"trailingSlash": ""}, methods={"POST"})
     *
     * @return \Symfony\Component\HttpFoundation\Response|CreatedResponse
     */
    public function createAction(Request $request)
    {
        $this->evaluateRequiredRole($this->getJsonApiConfig()->getCreate()->getCreateRequiredRoles());

        return $this->createCreatedFromRequest($request);
    }
}
