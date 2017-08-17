<?php

namespace Trikoder\JsonApiBundle\Config\Annotation;

use Closure;
use Trikoder\JsonApiBundle\Contracts\Config\ApiConfigInterface;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\Target;
use Trikoder\JsonApiBundle\Contracts\Config\CreateConfigInterface;
use Trikoder\JsonApiBundle\Contracts\Config\IndexConfigInterface;
use Trikoder\JsonApiBundle\Contracts\Config\UpdateConfigInterface;
use Trikoder\JsonApiBundle\Contracts\RepositoryInterface;
use Trikoder\JsonApiBundle\Contracts\RequestBodyDecoderInterface;

/**
 * @Annotation
 * @Target("ANNOTATION")
 */
class UpdateConfig
{
    /**
     * @return array|null
     */
    public $allowedFields;

    /**
     * @return array|null
     */
    public $requiredRoles;
}