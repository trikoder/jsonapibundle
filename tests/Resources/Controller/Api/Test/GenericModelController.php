<?php

declare(strict_types=1);

namespace Trikoder\JsonApiBundle\Tests\Resources\Controller\Api\Test;

use Symfony\Component\Routing\Annotation\Route;
use Trikoder\JsonApiBundle\Config\Annotation as JsonApiConfig;
use Trikoder\JsonApiBundle\Controller\AbstractController as JsonApiController;
use Trikoder\JsonApiBundle\Controller\Traits\CreateActionTrait;

/**
 * @Route("/generic")
 *
 * @JsonApiConfig\Config(
 *     modelClass="Trikoder\JsonApiBundle\Tests\Resources\Entity\GenericModel",
 *     repository="Trikoder\JsonApiBundle\Tests\Resources\Repository\GenericModelRepository"
 * )
 */
final class GenericModelController extends JsonApiController
{
    use CreateActionTrait;
}
