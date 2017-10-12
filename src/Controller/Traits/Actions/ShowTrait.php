<?php

namespace Trikoder\JsonApiBundle\Controller\Traits\Actions;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Trikoder\JsonApiBundle\Contracts\Config\ConfigInterface;

/**
 * Trait ShowTrait
 * @package Trikoder\JsonApiBundle\Controller\Traits\Actions
 */
trait ShowTrait
{

    /**
     * @param $id
     * @return null|object
     */
    public function getModelById($id)
    {
        /** @var ConfigInterface $config */
        $config = $this->getJsonApiConfig();

        $model = $config->getApi()->getRepository()->getOne($id, $config->getApi()->getFixedFiltering());

        // check if model is loaded
        if (null === $model) {
            throw new NotFoundHttpException();
        }
        // TODO - check if model correct class?

        // TODO - enable return of meta data, links etc
        return $model;
    }
}
