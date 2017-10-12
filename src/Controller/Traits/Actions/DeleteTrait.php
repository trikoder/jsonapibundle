<?php

namespace Trikoder\JsonApiBundle\Controller\Traits\Actions;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Trikoder\JsonApiBundle\Contracts\Config\ConfigInterface;
use Trikoder\JsonApiBundle\Contracts\RepositoryInterface;

/**
 * Trait DeleteTrait
 * @package Trikoder\JsonApiBundle\Controller\Traits\Actions
 */
trait DeleteTrait
{
    /**
     * @param $id
     * @return null
     */
    public function deleteModelById($id)
    {
        /** @var ConfigInterface $config */
        $config = $this->getJsonApiConfig();

        /** @var RepositoryInterface $repository */
        $repository = $config->getApi()->getRepository();

        // load object
        $model = $repository->getOne($id, $config->getApi()->getFixedFiltering());

        // check if model is loaded
        if(null === $model) {
            throw new NotFoundHttpException();
        }
        // TODO - check if model correct class?

        $repository->remove($model);

        return null;
    }
}
