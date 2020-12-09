<?php

namespace Trikoder\JsonApiBundle\Services\RequestDecoder;

use Neomerx\JsonApi\Contracts\Encoder\Parameters\EncodingParametersInterface;
use Neomerx\JsonApi\Contracts\Encoder\Parameters\SortParameterInterface;
use Neomerx\JsonApi\Exceptions\JsonApiException;
use Neomerx\JsonApi\Http\Request as NeomerxHttpRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Trikoder\JsonApiBundle\Contracts\Config\ConfigInterface;
use Trikoder\JsonApiBundle\Controller\JsonApiEnabledInterface;
use Trikoder\JsonApiBundle\Services\Neomerx\FactoryService;

/**
 * Class RequestDecoder
 */
class RequestDecoder
{
    const METHODS_WITH_BODY = [
        Request::METHOD_PATCH,
        Request::METHOD_POST,
        Request::METHOD_PUT,
    ];

    /**
     * @var FactoryService
     */
    private $jsonApiFactory;

    /**
     * Request parameters that were parsed from request
     *
     * @var EncodingParametersInterface
     */
    protected $parsedRequestParameters = null;

    /**
     * @var JsonApiEnabledInterface
     */
    private $controller;

    public function __construct(
        FactoryService $jsonApiFactory,
        JsonApiEnabledInterface $controller
    ) {
        $this->jsonApiFactory = $jsonApiFactory;
        $this->controller = $controller;
    }

    /**
     * @return Request
     */
    public function decode(Request $currentRequest)
    {
        try {
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
                        $currentRequestQueryFieldsEmpty = (false === \array_key_exists('fields',
                                $currentRequestQuery) || false === \is_array($currentRequestQuery['fields']));
                        if (true === $currentRequestQueryFieldsEmpty) {
                            $currentRequestQuery['fields'] = [];
                        }
                        foreach ($configFields as $type => $fields) {
                            if (false === \array_key_exists($type, $currentRequestQuery['fields'])) {
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
            try {
                $queryChecker->checkQuery($this->parsedRequestParameters);
            } catch (JsonApiException $jsonApiException) {
                throw new BadRequestHttpException('Invalid or not allowed query parameters');
            }

            // TODO add header checker, might cause problems with clients, should be configurable (checker eg. \Neomerx\JsonApi\Http\Headers\RestrictiveHeadersChecker)

            // build query args
            $queryParams = $this->buildQueryParams();

            // eg. check data and data type

            // decode payload
            $requestContentPrepared = [];
            $requestContent = $currentRequest->getContent();
            if (false === empty($requestContent)) {
                // payload is body of request
                $requestContentPrepared = (array) json_decode($requestContent, true);
            } else {
                // try to fallback to post fields in case of multipart request, see also note below about content and request
                if ($currentRequest->request->has('data')) {
                    $requestDataValue = $currentRequest->request->get('data');

                    if (null !== $requestDataValue) {
                        switch (true) {
                            // if it is raw post payload
                            case true === \is_array($requestDataValue):
                                // TODO check if data is array eg. batch/bulk payload?
                                $requestContentPrepared = ['data' => $requestDataValue];
                                break;

                            // if is json encoded
                            case true === \is_string($requestDataValue):
                                $requestContentPrepared = ['data' => (array) json_decode($requestDataValue, true)];
                                break;

                            default:
                                throw new BadRequestHttpException('Value passed inside "data" post value is not a parsable jsonapi data');
                        }
                    } else {
                        $requestContentPrepared = [];
                    }
                }
            }

            // TODO - check if correct type is sent (find it by schema type in class map by model)

            $decodedContent = [];

            if (
                $currentRequest->attributes->has('_jsonapibundle_relationship_endpoint')
                &&
                true === $currentRequest->attributes->get('_jsonapibundle_relationship_endpoint')
                &&
                \in_array($currentRequest->getMethod(), [Request::METHOD_POST, Request::METHOD_DELETE])
            ) {
                $validator = $config->getApi()->getRelationshipRequestBodyValidator();

                try {
                    if ($validator->validate($requestContentPrepared)->count()) {
                        throw new BadRequestHttpException('Body must contain data to be a valid JSON API payload.');
                    }
                } catch (UnexpectedTypeException $exception) {
                    //inconsistency in Symfony before 4.2.0 causes payload like \Trikoder\JsonApiBundle\Tests\Integration\RequestDecoderTest::testJsonPostPayloadWithDefaultsAttributeAndInvalidStructure to fail with exception
                    //@see https://github.com/symfony/symfony/pull/27917
                    throw new BadRequestHttpException('Body does not contain valid data');
                }

                $decodedContent = $config->getApi()->getRelationshipRequestBodyDecoder()->decode(
                    $currentRequest->getMethod(),
                    $requestContentPrepared
                );
            } elseif (\in_array($currentRequest->getMethod(), self::METHODS_WITH_BODY, true)) {
                try {
                    if ($config->getApi()->getRequestBodyValidator()->validate($requestContentPrepared)->count()) {
                        throw new BadRequestHttpException('Body must contain data to be a valid JSON API payload.');
                    }
                } catch (UnexpectedTypeException $exception) {
                    //inconsistency in Symfony before 4.2.0 causes payload like \Trikoder\JsonApiBundle\Tests\Integration\RequestDecoderTest::testJsonPostPayloadWithDefaultsAttributeAndInvalidStructure to fail with exception
                    //@see https://github.com/symfony/symfony/pull/27917
                    throw new BadRequestHttpException('Body does not contain valid data');
                }

                $decodedContent = $config->getApi()->getRequestBodyDecoder()->decode(
                    $currentRequest->getMethod(),
                    $requestContentPrepared
                );
            }

            /** -- content and request
             * when talking about request params, jsonapi decoded content is equivalent to post params, so we override it
             * we could merge it, that would be multipart support however this is jsonapi so we only do jsonapi stuff (atm :))
             */

            // create new request that will replace original one
            // empty duplicated to get copy of current request with allowed preserved properties
            $transformedRequest = $currentRequest->duplicate();
            // we favour initialize for setup because we can give content of request here, what we cannot do in duplicate
            $transformedRequest->initialize(
                $queryParams,
                // TODO - should we merge original request with decoded content? see comment block above
                (null === $decodedContent ? [] : $decodedContent),
                $currentRequest->attributes->all(),
                $currentRequest->cookies->all(),
                $currentRequest->files->all(),
                $currentRequest->server->all(),
                \is_array($decodedContent) ? http_build_query($decodedContent) : ''
            );

            // catch any underlying neomerix exceptions and return them as http
        } catch (JsonApiException $exception) {
            throw new HttpException($exception->getHttpCode(), null, $exception);
        }

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
     *
     * @return array
     */
    private function decodeFilterParams($sourceFilterParams)
    {
        $decodedParams = [];

        if (true === \is_array($sourceFilterParams)) {
            foreach ($sourceFilterParams as $field => $filters) {
                if (true === \is_array($filters)) {
                    $decodedParams[$field] = $this->decodeFilterParams($filters);
                } else {
                    if (false !== strpos($filters, ',')) {
                        $decodedParams[$field] = explode(',', $filters);
                    } else {
                        $decodedParams[$field] = $filters;
                    }
                }
            }
        }

        return $decodedParams;
    }
}
