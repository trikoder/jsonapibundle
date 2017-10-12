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

    /**
     * @param Request $currentRequest
     * @return Request
     */
    public function decode(Request $currentRequest)
    {

        /** @var ConfigInterface $config */
        $config = $this->controller->getJsonApiConfig();

        // we save parsed parameters so we can use them later
        $this->parsedRequestParameters = $this->jsonApiFactory->createQueryParametersParser()->parse(new NeomerxHttpRequest(
            function () use ($currentRequest) {
                return $currentRequest->getMethod();
            },
            function () use ($currentRequest) {
                return $currentRequest->headers->all();
            },
            function () use ($currentRequest, $config) {
                $currentRequestQuery = $currentRequest->query->all();

                // update some defaults
                if (false === empty($config->getIndex()->getIndexAllowedFields())) {
                    // should index filtering be applied to other crud actions? i think yes
                    // should resulting fields list be product of query fields and config fields? or leave it to underlaying checkers?
                    $configFields = $config->getIndex()->getIndexAllowedFields();
                    // if all request fields are empty so we apply for all types
                    $currentRequestQueryFieldsEmpty = (false === array_key_exists('fields',
                            $currentRequestQuery) || false === is_array($currentRequestQuery['fields']));
                    if (true === $currentRequestQueryFieldsEmpty) {
                        $currentRequestQuery['fields'] = [];
                    }
                    foreach ($configFields as $type => $fields) {
                        if (false === array_key_exists($type, $currentRequestQuery['fields'])) {
                            $currentRequestQuery['fields'][$type] = implode(',', $fields);
                        }
                    }
                }
                return $currentRequestQuery;
            }
        ));

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
        // TODO - should we recheck built params also?
        $queryChecker->checkQuery($this->parsedRequestParameters);

        // TODO add header checker, might cause problems with clients, should be configurable (checker eg. \Neomerx\JsonApi\Http\Headers\RestrictiveHeadersChecker)

        // build query args
        $queryParams = $this->buildQueryParams();

        // eg. check data and data type

        // decode payload
        $requestContentPrepared = [];
        $requestContent = $currentRequest->getContent();
        if(false === empty($requestContent)) {
            // payload is body of request
            $requestContentPrepared = (array)json_decode($requestContent, true);
        } else {
            // try to fallback to post fields in case of multipart request, see also note below about content and request
            if($currentRequest->request->has('data')) {
                // TODO check if data is array eg. batch/bulk payload?
                $requestContentPrepared = ['data' => (array)json_decode($currentRequest->request->get('data', "[]"), true)];
            }
        }

        // TODO - check if correct type is sent (find it by schema type in class map by model)

        // decode content
        $decodedContent = $config->getApi()->getRequestBodyDecoder()->decode($requestContentPrepared);

        /** -- content and request
         * when talking about request params, jsonapi decoded content is equivalent to post params, so we override it
         * we could merge it, that would be multipart support however this is jsonapi so we only do jsonapi stuff (atm :))
         */

        // create new reqest that will replace original one
        $transformedRequest = new Request(
            $queryParams,
            // TODO - should we merge original request with decoded content? see comment block above
            (null === $decodedContent ? [] : $decodedContent),
            $currentRequest->attributes->all(),
            $currentRequest->cookies->all(),
            $currentRequest->files->all(),
            $currentRequest->server->all(),
            is_array($decodedContent) ? http_build_query($decodedContent) : ''
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
            $queryParams['filter'] = $this->decodeFilterParams($filteringParams);
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

    /**
     * @param $sourceFilterParams
     * @return array
     */
    private function decodeFilterParams($sourceFilterParams)
    {
        $decodedParams = [];

        if (true === is_array($sourceFilterParams)) {
            foreach ($sourceFilterParams as $field => $filters) {
                if (false !== strpos($filters, ',')) {
                    $decodedParams[$field] = explode(',', $filters);
                } else {
                    $decodedParams[$field] = $filters;
                }
            }
        }

        return $decodedParams;
    }
}
