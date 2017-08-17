<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\Controller\Api\Demo;

use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Trikoder\JsonApiBundle\Contracts\ResponseFactoryInterface;
use Trikoder\JsonApiBundle\Contracts\SchemaClassMapProviderInterface;
use Trikoder\JsonApiBundle\Services\Neomerx\EncoderService;
use Trikoder\JsonApiBundle\Services\SchemaClassMap\EmptySchemaClassMap;
use Trikoder\JsonApiBundle\Tests\Resources\Entity\User;

/**
 * @Route("/custom-response")
 */
class CustomResponseController extends Controller
{
    /**
     * @Route("")
     * @Route("/from-array")
     */
    public function defaultAction()
    {
        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->get("trikoder.jsonapi.response_factory");
        /** @var EncoderService $encoder */
        $encoder = $this->get('trikoder.jsonapi.encoder');

        return $responseFactory->createResponse(
            $encoder->encode(new EmptySchemaClassMap(), (object)[
                'attributeX' => 'valueY'
            ])
        );
    }

    /**
     * @Route("/manual")
     */
    public function manualListAction(Request $request)
    {
        /** @var User[] $users */
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->get("trikoder.jsonapi.response_factory");
        /** @var EncoderService $encoder */
        $encoder = $this->get('trikoder.jsonapi.encoder');
        /** @var SchemaClassMapProviderInterface $schemaProvider */
        $schemaProvider = $this->get('trikoder.jsonapi.schema_class_map_provider');

        return $responseFactory->createResponse(
            $encoder->encode($schemaProvider, $users)
        );
    }

    /**
     * @Route("")
     */
    public function exceptionAction()
    {
        throw new Exception("Test exception");
    }
}
