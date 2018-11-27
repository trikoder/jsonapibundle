<?php

namespace Trikoder\JsonApiBundle\Controller\Traits;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ShowActionTrait
 */
trait ShowActionTrait
{
    use Actions\ShowTrait;

    /**
     * @Route("/{id}{trailingSlash}", requirements={"trailingSlash": "[/]{0,1}"}, defaults={"trailingSlash": ""}, methods={"GET"})
     *
     * @return null|object
     */
    public function showAction(Request $request, $id)
    {
        return $this->getModelById($id);
    }
}
