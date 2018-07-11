<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\Controller\Api\Demo;

use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Trikoder\JsonApiBundle\Config\Annotation as JsonApiConfig;
use Trikoder\JsonApiBundle\Controller\AbstractController as JsonApiController;

/**
 * @Route("/exception")
 *
 * @JsonApiConfig\Config(
 *     modelClass="Trikoder\JsonApiBundle\Tests\Resources\Entity\User"
 * )
 */
class ExceptionController extends JsonApiController
{
    /**
     * @Route("")
     */
    public function exceptionAction()
    {
        throw new Exception('Test exception', 44);
    }
}
