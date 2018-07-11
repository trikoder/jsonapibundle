<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\Controller\Api\Demo;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Trikoder\JsonApiBundle\Config\Annotation as JsonApiConfig;
use Trikoder\JsonApiBundle\Controller\AbstractController as JsonApiController;
use Trikoder\JsonApiBundle\Controller\Traits\Actions\IndexTrait;
use Trikoder\JsonApiBundle\Response\DataResponse;

/**
 * @Route("/custom-meta-response")
 *
 * @JsonApiConfig\Config(
 *     modelClass="Trikoder\JsonApiBundle\Tests\Resources\Entity\User"
 * )
 */
class CustomMetaResponseController extends JsonApiController
{
    use IndexTrait;

    /**
     * @Route("")
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
     * @Route("/empty")
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
     * @Route("/empty-all")
     */
    public function emptyAllAction(Request $request)
    {
        return new DataResponse(null);
    }
}
