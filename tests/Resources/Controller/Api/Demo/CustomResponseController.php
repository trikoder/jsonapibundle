<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\Controller\Api\Demo;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Trikoder\JsonApiBundle\Contracts\ResponseFactoryInterface;
use Trikoder\JsonApiBundle\Contracts\SchemaClassMapProviderInterface;
use Trikoder\JsonApiBundle\Controller\AbstractController as JsonApiController;
use Trikoder\JsonApiBundle\Response\DataResponse;
use Trikoder\JsonApiBundle\Schema\Builtin\StdClassSchema;
use Trikoder\JsonApiBundle\Services\Neomerx\EncoderService;
use Trikoder\JsonApiBundle\Tests\Resources\Entity\User;

/**
 * @Route(path="/custom-response")
 */
class CustomResponseController extends JsonApiController
{
    /**
     * @Route(path="")
     * @Route(path="/from-array")
     */
    public function defaultAction()
    {
        return new DataResponse((object) [
            'attributeX' => 'valueY',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getSchemaClassMapProvider()
    {
        $classMap = parent::getSchemaClassMapProvider();
        $classMap->add(\stdClass::class, StdClassSchema::class);

        return $classMap;
    }

    /**
     * @Route(path="/manual")
     */
    public function manualListAction(Request $request)
    {
        // TODO change services to injected

        /** @var User[] $users */
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->get('trikoder.jsonapi.response_factory');
        /** @var EncoderService $encoder */
        $encoder = $this->get('trikoder.jsonapi.encoder');
        /** @var SchemaClassMapProviderInterface $schemaProvider */
        $schemaProvider = $this->get(SchemaClassMapProviderInterface::class);

        return $responseFactory->createResponse(
            $encoder->encode($schemaProvider, $users)
        );
    }

    /**
     * @Route(path="")
     */
    public function exceptionAction()
    {
        throw new Exception('Test exception');
    }
}
