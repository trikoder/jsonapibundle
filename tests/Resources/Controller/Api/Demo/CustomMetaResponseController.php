<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\Controller\Api\Demo;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Trikoder\JsonApiBundle\Config\Annotation as JsonApiConfig;
use Trikoder\JsonApiBundle\Controller\AbstractController as JsonApiController;
use Trikoder\JsonApiBundle\Controller\Traits\Actions\IndexTrait;
use Trikoder\JsonApiBundle\Response\DataResponse;

/**
 * @Route(path="/custom-meta-response")
 *
 * @JsonApiConfig\Config(
 *     modelClass="Trikoder\JsonApiBundle\Tests\Resources\Entity\User"
 * )
 */
class CustomMetaResponseController extends JsonApiController
{
    use IndexTrait;

    /**
     * @Route(path="")
     */
    public function indexAction(Request $request)
    {
        $collection = $this->createCollectionFromRequest($request);

        return new DataResponse(
            $collection,
            [
                'customInfo' => 'valid',
            ]
        );
    }

    /**
     * @Route(path="/empty")
     */
    public function emptyAction(Request $request)
    {
        return new DataResponse(
            null,
            [
                'customInfo' => 'valid',
            ]
        );
    }

    /**
     * @Route(path="/empty-all")
     */
    public function emptyAllAction(Request $request)
    {
        return new DataResponse(null);
    }
}
