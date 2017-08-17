<?php

namespace Trikoder\JsonApiBundle\Services;

use Neomerx\JsonApi\Contracts\Encoder\Parameters\EncodingParametersInterface;
use Neomerx\JsonApi\Contracts\Encoder\Parameters\SortParameterInterface;
use Symfony\Component\HttpFoundation\Request;
use Trikoder\JsonApiBundle\Contracts\Config\ConfigInterface;
use Trikoder\JsonApiBundle\Controller\JsonApiEnabledInterface;
use Trikoder\JsonApiBundle\Services\Neomerx\FactoryService;
use Neomerx\JsonApi\Http\Request as NeomerxHttpRequest;

/**
 * Class RequestDecoder
 * @package Trikoder\JsonApiBundle\Services
 *
 * TODO - define interface for this to be used in code
 */
class RequestDecoder
{
    /**
     * @var FactoryService
     */
    private $jsonApiFactory;

    /**
     * Request parameters that were parsed from request
     * @var EncodingParametersInterface
     */
    protected $parsedRequestParameters = null;

    /**
     * @var JsonApiEnabledInterface
     */
    private $controller;

    public function __construct(FactoryService $jsonApiFactory, JsonApiEnabledInterface $controller)
    {
        $this->jsonApiFactory = $jsonApiFactory;
        $this->controller = $controller;
    }

    public function decode(Request $currentRequest)
    {
        // we save parsed parameters so we can use them later
        $this->parsedRequestParameters = $this->jsonApiFactory->createQueryParametersParser()->parse(new NeomerxHttpRequest(
            function () use ($currentRequest) {
                return $currentRequest->getMethod();
            },
            function () use ($currentRequest) {
                return $currentRequest->headers->all();
            },
            function () use ($currentRequest) {
                return $currentRequest->query->all();
            }
        ));

        /** @var ConfigInterface $config */
        $config = $this->controller->getJsonApiConfig();

        // validate request
        $queryChecker = $this->jsonApiFactory->createQueryChecker(
            $config->getApi()->getAllowExtraParams(),
            $config->getApi()->getAllowedIncludePaths(),
            $config->getIndex()->getIndexAllowedFields(),
            $config->getIndex()->getIndexAllowedSortFields(),
            null,
            $config->getIndex()->getIndexAllowedFilteringParameters()
        );
        // TODO - this throws exception - need to check if we wanna handle it here or let it bubble up to controllerException event
        $queryChecker->checkQuery($this->parsedRequestParameters);

        // TODO add header checker, might cause problems with clients, should be configurable (checker eg. \Neomerx\JsonApi\Http\Headers\RestrictiveHeadersChecker)

        // build query args
        $queryParams = $this->buildQueryParams();

        // eg. check data and data type
        $requestContent = $currentRequest->getContent();

        // TODO - check if correct type is sent (find it by schema type in class map by model)

        // decode content
        $decodedContent = $config->getApi()->getRequestBodyDecoder()->decode((array)json_decode($requestContent, true));

        // create new reqest that will replace original one
        $transformedRequest = new Request(
            $queryParams,
            $currentRequest->request->all(),
            $currentRequest->attributes->all(),
            $currentRequest->cookies->all(),
            $currentRequest->files->all(),
            $currentRequest->server->all(),
            $decodedContent
        );

        return $transformedRequest;
    }

    public function getParsedRequestParameters()
    {
        return $this->parsedRequestParameters;
    }

    protected function buildQueryParams()
    {
        $queryParams = [];
        if ($fieldParams = $this->parsedRequestParameters->getFieldSets()) {
            $queryParams['fields'] = $fieldParams;
        }
        if ($includeParams = $this->parsedRequestParameters->getIncludePaths()) {
            $queryParams['include'] = $includeParams;
        }
        if ($filteringParams = $this->parsedRequestParameters->getFilteringParameters()) {
            $queryParams['filter'] = $filteringParams;
        }
        if ($sortParams = $this->parsedRequestParameters->getSortParameters()) {
            $queryParams['sort'] = [];
            /** @var SortParameterInterface $sortParam */
            foreach ($sortParams as $sortParam) {
                $queryParams['sort'][$sortParam->getField()] = ($sortParam->isAscending()) ? 'ASC' : 'DESC';
            }
        }
        if ($paginationParams = $this->parsedRequestParameters->getPaginationParameters()) {
            $queryParams['page'] = $paginationParams;
        }

        return $queryParams;
    }
}