<?php

namespace Trikoder\JsonApiBundle\Controller\Traits;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Trikoder\JsonApiBundle\Contracts\Config\ConfigInterface;

/**
 * Class ShowActionTrait
 * @package Trikoder\JsonApiBundle\Controller\Traits
 */
trait ShowActionTrait
{

    /**
     * @param Request $request
     *
     * @Route("/{id}")
     * @Method("GET")
     * @return null|object
     */
    public function showAction(Request $request, $id)
    {
        /** @var ConfigInterface $config */
        $config = $this->getJsonApiConfig();

        // TODO - enable return of meta data, links etc
        return $config->getApi()->getRepository()->getOne($id, $config->getApi()->getFixedFiltering());
    }
}