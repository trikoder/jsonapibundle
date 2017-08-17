<?php

namespace Trikoder\JsonApiBundle\Controller\Traits;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Trikoder\JsonApiBundle\Contracts\Config\ConfigInterface;
use Trikoder\JsonApiBundle\Contracts\ModelTools\ModelInputHandlerInterface;
use Trikoder\JsonApiBundle\Contracts\RepositoryInterface;
use Trikoder\JsonApiBundle\Contracts\ResponseFactoryInterface;

/**
 * Class DeleteActionTrait
 * @package Trikoder\JsonApiBundle\Controller\Traits
 */
trait DeleteActionTrait
{

    /**
     * @param Request $request
     *
     * @Route("/{id}")
     * @Method({"DELETE"})
     *
     * @return null
     */
    public function deleteAction(Request $request, $id)
    {
        /** @var ConfigInterface $config */
        $config = $this->getJsonApiConfig();

        /** @var RepositoryInterface $repository */
        $repository = $config->getApi()->getRepository();

        // load object
        $model = $repository->getOne($id, $config->getApi()->getFixedFiltering());

        // TODO - check if model is loaded

        $repository->remove($model);

        return null;
    }
}