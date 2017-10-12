<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\Controller\Api\Demo;

use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Trikoder\JsonApiBundle\Contracts\ResponseFactoryInterface;
use Trikoder\JsonApiBundle\Contracts\SchemaClassMapProviderInterface;
use Trikoder\JsonApiBundle\Response\DataResponse;
use Trikoder\JsonApiBundle\Schema\Builtin\StdClassSchema;
use Trikoder\JsonApiBundle\Services\Neomerx\EncoderService;
use Trikoder\JsonApiBundle\Tests\Resources\Entity\User;
use Trikoder\JsonApiBundle\Controller\AbstractController as JsonApiController;

/**
 * @Route("/custom-response")
 *
 */
class CustomResponseController extends JsonApiController
{
    /**
     * @Route("")
     * @Route("/from-array")
     */
    public function defaultAction()
    {
        return new DataResponse((object)[
            'attributeX' => 'valueY'
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getSchemaClassMapProvider()
    {
        $classMap = parent::getSchemaClassMapProvider();
        $classMap->add(\stdClass::class, StdClassSchema::class);
        return $classMap;
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
