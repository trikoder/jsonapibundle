<?php

namespace Trikoder\JsonApiBundle\Controller\Traits;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UpdateRelationshipActionTrait
 */
trait UpdateRelationshipActionTrait
{
    use Actions\UpdateRelationshipTrait;

    /**
     * @Route("/{id}/relationships/{relationshipName}{trailingSlash}", requirements={"trailingSlash": "[/]{0,1}", "relationshipName": "[^/]+"}, defaults={"trailingSlash": "", "_jsonapibundle_relationship_endpoint": true}, methods={"POST", "DELETE"})
     *
     * @return object
     */
    public function updateRelationshipAction(Request $request, $id, $relationshipName)
    {
        $this->evaluateRequiredRole($this->getJsonApiConfig()->getUpdateRelationship()->getRequiredRoles());

        $result = $this->updateRequestFromRelationshipRequest($request, $id, $relationshipName);

        if ($result instanceof Response) {
            return $result;
        }

        return $this->getJsonApiResponseFactory()->createNoContent();
    }
}
