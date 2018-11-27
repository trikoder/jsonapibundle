<?php

namespace Trikoder\JsonApiBundle\Listener;

use Iterator;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterControllerArgumentsEvent;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Trikoder\JsonApiBundle\Contracts\ObjectListCollectionInterface;
use Trikoder\JsonApiBundle\Contracts\RequestBodyDecoderInterface;
use Trikoder\JsonApiBundle\Contracts\ResponseFactoryInterface;
use Trikoder\JsonApiBundle\Contracts\SchemaClassMapProviderInterface;
use Trikoder\JsonApiBundle\Controller\JsonApiEnabledInterface;
use Trikoder\JsonApiBundle\Response\DataResponse;
use Trikoder\JsonApiBundle\Services\Neomerx\EncoderService;
use Trikoder\JsonApiBundle\Services\Neomerx\FactoryService;
use Trikoder\JsonApiBundle\Services\RequestDecoder;

/**
 * Class KernelListener
 */
class KernelListener
{
    use JsonApiEnabledControllerDetectorTrait;

    /**
     * Flag to save state if current controller is json api enabled controller
     *
     * @var bool
     */
    protected $isJsonApiEnabledRequest = false;

    /**
     * Schema class map provider
     *
     * @var SchemaClassMapProviderInterface
     */
    protected $schemaClassMapProvider;

    /**
     * TODO interface this instead of using implementation
     *
     * @var FactoryService
     */
    protected $jsonApiFactory;

    /**
     * @var RequestBodyDecoderInterface
     */
    private $requestBodyDecoder;

    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * @var EncoderService
     */
    private $encoderService;

    /**
     * @var RequestDecoder
     */
    private $requestDecoder;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * KernelListener constructor.
     */
    public function __construct(
        FactoryService $neomerxFactoryService,
        RequestBodyDecoderInterface $requestBodyDecoder,
        ResponseFactoryInterface $responseFactory,
        EncoderService $encoderService,
        Logger $logger
    ) {
        // we use neomerx so here we hint it is foreign implementation
        $this->jsonApiFactory = $neomerxFactoryService;
        $this->requestBodyDecoder = $requestBodyDecoder;
        $this->responseFactory = $responseFactory;
        $this->encoderService = $encoderService;
        $this->logger = $logger;
    }

    /**
     * Setter of schema class map provider
     */
    protected function setSchemaClassMapProvider(SchemaClassMapProviderInterface $schemaClassMapProvider)
    {
        $this->schemaClassMapProvider = $schemaClassMapProvider;
    }

    /**
     * Check if controller is json api and also get it's schema map
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $this->resolveControllerFromEventController($event->getController());

        // if api enabled controller, save information in the request
        if (null !== $controller && true === $this->isJsonApiEnabledController($controller)) {
            /* @var JsonApiEnabledInterface $controller */
            $this->isJsonApiEnabledRequest = true;

            $this->setSchemaClassMapProvider($controller->getSchemaClassMapProvider());
        }
    }

    /**
     * Transforms controller result to valid json api response if possible
     *
     *
     * @throws \Exception
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        // if this is not json api enabled request do nothing
        if (false === $this->isJsonApiEnabledRequest) {
            return;
        }

        // prepare empty values to be populated
        $resultMeta = [];
        $resultLinks = [
            'self' => $event->getRequest()->getUri(),
        ];

        // get result which we need to process
        $controllerResult = $event->getControllerResult();

        // process the result
        $response = $this->getResponseFromControllerResult($controllerResult, $resultMeta, $resultLinks);

        $event->setResponse($response);
    }

    /**
     * @param $controllerResult
     *
     * @return Response
     *
     * @throws \Exception
     */
    private function getResponseFromControllerResult($controllerResult, array $resultMeta = [], array $resultLinks = [])
    {
        // find what action to perform based on result from controller
        switch (true) {
            // if you have response from controller, check if it is valid
            case $controllerResult instanceof Response:

                // TODO - this should never happen here? Response is called in kernelResponse

                break;

            case $controllerResult instanceof DataResponse:

                $resultMeta = array_merge($resultMeta, $controllerResult->getMeta());
                $resultLinks = array_merge($resultLinks, $controllerResult->getLinks());

                // allow null value data response
                if (null !== $controllerResult->getData()) {
                    // as data inside can be anything, we extract extra info from response and pass all down for another round of decoding
                    return $this->getResponseFromControllerResult($controllerResult->getData(), $resultMeta,
                        $resultLinks);
                }

                $response = $this->responseFactory->createResponse($this->encode(null, $resultMeta,
                    $resultLinks));

                return $response;

                break;

            // if we got our collection encode it and package it in response
            case $controllerResult instanceof ObjectListCollectionInterface:

                // append total from collection
                $resultMeta['total'] = $controllerResult->getTotal();

                $response = $this->responseFactory->createResponse($this->encode(
                    $controllerResult->getCollection(),
                    $resultMeta,
                    $resultLinks
                ));

                return $response;

                break;

            // if you got array or object, try to encode it and package in response
            case \is_array($controllerResult) || \is_object($controllerResult):

                $response = $this->responseFactory->createResponse($this->encode($controllerResult, $resultMeta,
                    $resultLinks));

                return $response;

                break;

            // no content response?
            case null === $controllerResult:

                $response = $this->responseFactory->createNoContent();

                return $response;

                break;

            // none of supported results were returned, run around the room in panic?
            default:
                // TODO - move this from generic to package exception
                throw new \Exception('Unsuported result from controller');
        }
    }

    /**
     * @param array|Iterator|null|object|string $data
     *
     * @return string
     */
    protected function encode($data = '', array $meta = null, array $links = [])
    {
        // if meta is empty, ommit it
        if (true === empty($meta)) {
            $meta = null;
        }

        return $this->encoderService->encode($this->schemaClassMapProvider, $data,
            $this->requestDecoder->getParsedRequestParameters(), $meta, $links);
    }

    /**
     * Event is called when controller arguments are being resolved, here we can replace original request with translated request
     */
    public function onKernelControllerArguments(FilterControllerArgumentsEvent $event)
    {
        /** @var JsonApiEnabledInterface $controller */
        $controller = $this->resolveControllerFromEventController($event->getController());

        // if this is not json api enabled request do nothing
        if (null === $controller || false === $this->isJsonApiEnabledController($controller)) {
            return;
        }

        // TODO IDEA - move this to arguments resolver so it can be moved before built-in one?

        // get original request
        $currentRequest = $event->getRequest();

        // decode it
        /* @var RequestDecoder $requestDecoder */
        $this->requestDecoder = new RequestDecoder($this->jsonApiFactory, $controller);
        $transformedRequest = $this->requestDecoder->decode($currentRequest);

        // we always fix first Request as first arg
        $arguments = $event->getArguments();
        $arguments[0] = $transformedRequest;

        $event->setArguments($arguments);
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        // TODO - define what actions are needed
        // TODO here we need to translate/update response to json?
    }

    /**
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        // if this is not json api enabled request do nothing
        if (false === $this->isJsonApiEnabledRequest) {
            return;
        }

        $exception = $event->getException();

        $response = $this->responseFactory->createErrorFromException($exception);

        $event->setResponse($response);

        // send exception to logger
        if ($this->logger) {
            $this->logger->error($exception->getMessage(), [
                'exception' => $exception,
            ]);
        }
    }
}
