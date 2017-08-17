<?php

namespace Trikoder\JsonApiBundle\Controller\Traits;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Trikoder\JsonApiBundle\Contracts\Config\ConfigInterface;

/**
 * Class IndexActionTrait
 * @package Trikoder\JsonApiBundle\Controller\Traits
 */
trait IndexActionTrait
{

    /**
     * @param Request $request
     *
     * @Route("/")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        /** @var ConfigInterface $config */
        $config = $this->getJsonApiConfig();
        $filter = array_merge($request->query->get('filter', []), $config->getApi()->getFixedFiltering());
        $sort = $request->query->get('sort', $config->getIndex()->getIndexDefaultSort());

        $pagination = $this->resolvePaginationArguments($request->query->get('page', null));

        $listCollection = $this->getRepository()->getList($filter, $sort, $pagination['limit'], $pagination['offset']);

        return $listCollection;
    }

    /**
     * @param null $arguments
     * @return array
     */
    protected function resolvePaginationArguments($arguments = null)
    {
        /** @var ConfigInterface $config */
        $config = $this->getJsonApiConfig();

        $pagination = array_merge(['limit' => null, 'offset' => null], $config->getIndex()->getIndexDefaultPagination());

        if (true === is_array($arguments)) {

            // calculate limit first
            // offset limit strategy
            if (true === array_key_exists('limit', $arguments)) {
                $pagination['limit'] = (int)$arguments['limit'];
            } // page size strategy
            else {
                if (true === array_key_exists('size', $arguments)) {
                    $pagination['limit'] = (int)$arguments['size'];
                }
            }

            // offset limit strategy
            if (true === array_key_exists('offset', $arguments)) {
                $pagination['offset'] = (int)$arguments['offset'];
            }

            // page size strategy
            if (true === array_key_exists('page', $arguments) && null !== $pagination['limit']) {
                $pagination['offset'] = ((int)$arguments['page'] - 1) * $pagination['limit'];
            }

            // TODO - check for cursor strategy
        }

        return $pagination;
    }
}
