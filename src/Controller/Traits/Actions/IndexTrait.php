<?php

namespace Trikoder\JsonApiBundle\Controller\Traits\Actions;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Trikoder\JsonApiBundle\Contracts\Config\ConfigInterface;
use Trikoder\JsonApiBundle\Contracts\Config\IndexConfigInterface;
use Trikoder\JsonApiBundle\Contracts\ObjectListCollectionInterface;
use Trikoder\JsonApiBundle\Response\DataResponse;

/**
 * Trait IndexTrait
 */
trait IndexTrait
{
    /**
     * @param Request $request
     *
     * @return array|null|\Trikoder\JsonApiBundle\Contracts\ObjectListCollectionInterface
     */
    private function createCollectionFromRequest(Request $request)
    {
        /** @var ConfigInterface $config */
        $config = $this->getJsonApiConfig();
        $filter = array_merge($request->query->get('filter', []), $config->getApi()->getFixedFiltering());
        $sort = $request->query->get('sort', $config->getIndex()->getIndexDefaultSort());

        $pagination = $this->resolvePaginationArguments($request->query->get('page', null));

        $listCollection = $config->getApi()->getRepository()->getList($filter, $sort, $pagination['limit'],
            $pagination['offset']);

        return $listCollection;
    }

    /**
     * @param ObjectListCollectionInterface $collection
     * @param Request $request
     * @param RouterInterface $router
     *
     * @return DataResponse
     */
    private function createPaginatedDataResponseFromCollectionAndRequest(
        ObjectListCollectionInterface $collection,
        Request $request,
        RouterInterface $router
    ) {
        $paginationRequest = $request->query->get('page', null);
        $paginationParams = $this->resolvePaginationArguments($paginationRequest);

        $urlGenerator = function ($params) use ($request, $router) {
            return $this->generateSelfUrlFromRequest($router, $request, $params);
        };

        return $this->createPaginatedDataResponseFromCollection($collection, $paginationParams, $urlGenerator);
    }

    /**
     * @param RouterInterface $router
     * @param Request $request
     * @param array $overrideParams
     *
     * @return string
     */
    private function generateSelfUrlFromRequest(RouterInterface $router, Request $request, array $overrideParams)
    {
        $routeName = $request->get('_route');
        $routeParams = $request->query->all();

        // figure out request attributes from route
        $compiledRoute = $router->getRouteCollection()->get($routeName)->compile();
        $compiledRouteVariables = $compiledRoute->getVariables();
        foreach ($compiledRouteVariables as $compiledRouteVariable) {
            if ($request->attributes->has($compiledRouteVariable)) {
                $routeParams[$compiledRouteVariable] = $request->attributes->get($compiledRouteVariable);
            }
        }

        $routeParams = array_merge($routeParams, $overrideParams);

        foreach ($routeParams as $paramName => &$paramValues) {
            if (in_array($paramName, ['fields', 'filter']) && is_array($paramValues)) {
                foreach ($paramValues as $fieldName => $fieldProperties) {
                    if (is_array($fieldProperties)) {
                        $paramValues[$fieldName] = implode(',', $fieldProperties);
                    }
                }
            } elseif ('include' === $paramName && is_array($paramValues)) {
                $routeParams[$paramName] = implode(',', $paramValues);
            }
        }

        return $router->generate(
            $routeName,
            $routeParams,
            RouterInterface::ABSOLUTE_URL
        );
    }

    /**
     * @param ObjectListCollectionInterface $collection
     * @param array $paginationParams Array as returned from resolvePaginationArguments
     * @param callable $urlGenerator Callable that accepts override params as first argument
     *
     * @return DataResponse
     */
    private function createPaginatedDataResponseFromCollection(
        ObjectListCollectionInterface $collection,
        array $paginationParams,
        callable $urlGenerator
    ) {
        // check for required values
        if (
            false === array_key_exists('strategy', $paginationParams) ||
            false === array_key_exists('limit', $paginationParams) ||
            false === array_key_exists('offset', $paginationParams)
        ) {
            throw new \InvalidArgumentException();
        }
        if (false === array_key_exists('limit', $paginationParams) || null === $paginationParams['limit']) {
            throw new \RuntimeException('Limit values for pagination is missing. Did you forget to configure defaults?');
        }

        $totalResults = $collection->getTotal();

        switch ($paginationParams['strategy']) {
            case IndexConfigInterface::PAGINATION_STRATEGY_PAGE_SIZE:
                $pages = $this->calculatePagesForPageSize(
                    $paginationParams['offset'],
                    $paginationParams['limit'],
                    $totalResults
                );
                break;

            default:
                $pages = $this->calculatePagesForLimitOffset(
                    $paginationParams['offset'],
                    $paginationParams['limit'],
                    $totalResults
                );
        }

        $links = [];
        $links['self'] = $urlGenerator(['page' => $pages['self']]);
        $links['first'] = $urlGenerator(['page' => $pages['first']]);
        $links['last'] = $urlGenerator(['page' => $pages['last']]);
        $links['prev'] = (null !== $pages['prev'] ? $urlGenerator(['page' => $pages['prev']]) : null);
        $links['next'] = (null !== $pages['next'] ? $urlGenerator(['page' => $pages['next']]) : null);

        return new DataResponse(
            $collection,
            [],
            $links
        );
    }

    /**
     * @param $offset
     * @param $limit
     * @param $total
     *
     * @return array containing pagination params values for first, last, self, previous, next
     */
    private function calculatePagesForLimitOffset(int $offset, int $limit, int $total): array
    {
        $result = [];

        $result['first'] = ['limit' => $limit, 'offset' => 0];
        $result['last'] = ['limit' => $limit, 'offset' => ceil($total / $limit) * $limit - $limit];
        $result['prev'] = ($offset >= $limit) ? ['limit' => $limit, 'offset' => ($offset - $limit)] : null;
        $result['next'] = ($offset < $result['last']['offset']) ? [
            'limit' => $limit,
            'offset' => $offset + $limit,
        ] : null;
        $result['self'] = ['limit' => $limit, 'offset' => $offset];

        return $result;
    }

    /**
     * @param $offset
     * @param $limit
     * @param $total
     *
     * @return array containing pagination params values for first, last, self, previous, next
     */
    private function calculatePagesForPageSize(int $offset, int $limit, int $total): array
    {
        $result = [];

        $result['self'] = ['size' => $limit, 'number' => ceil($offset / $limit) + 1];
        $result['first'] = ['size' => $limit, 'number' => 1];
        $result['last'] = ['size' => $limit, 'number' => ceil($total / $limit)];
        $result['prev'] = ($result['self']['number'] > 1) ? [
            'size' => $limit,
            'number' => $result['self']['number'] - 1,
        ] : null;
        $result['next'] = ($result['self']['number'] < $result['last']['number']) ? [
            'size' => $limit,
            'number' => $result['self']['number'] + 1,
        ] : null;

        return $result;
    }

    /**
     * @param null $arguments
     *
     * @return array
     */
    private function resolvePaginationArguments($arguments = null)
    {
        /** @var ConfigInterface $config */
        $config = $this->getJsonApiConfig();

        $pagination = array_merge([
            'limit' => null,
            'offset' => 0,
            'strategy' => IndexConfigInterface::PAGINATION_STRATEGY_LIMIT_OFFSET,
        ],
            $config->getIndex()->getIndexDefaultPagination());

        if (true === is_array($arguments)) {
            // calculate limit first
            // page size strategy
            if (true === array_key_exists('size', $arguments)) {
                $pagination['limit'] = (int) $arguments['size'];
                $pagination['strategy'] = IndexConfigInterface::PAGINATION_STRATEGY_PAGE_SIZE;
            } else {
                // offset limit strategy
                if (true === array_key_exists('limit', $arguments)) {
                    $pagination['limit'] = (int) $arguments['limit'];
                    $pagination['strategy'] = IndexConfigInterface::PAGINATION_STRATEGY_LIMIT_OFFSET;
                }
            }

            // page size strategy
            if (true === array_key_exists('number', $arguments)) {
                $pagination['offset'] = ((int) $arguments['number'] - 1) * $pagination['limit'];
                $pagination['strategy'] = IndexConfigInterface::PAGINATION_STRATEGY_PAGE_SIZE;
            } else {
                // offset limit strategy
                if (true === array_key_exists('offset', $arguments)) {
                    $pagination['offset'] = (int) $arguments['offset'];
                    $pagination['strategy'] = IndexConfigInterface::PAGINATION_STRATEGY_LIMIT_OFFSET;
                }
            }

            // TODO - check for cursor strategy
        }

        return $pagination;
    }
}
