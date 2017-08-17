<?php

namespace Trikoder\JsonApiBundle\Config\Annotation;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target("ANNOTATION")
 */
class DeleteConfig
{
    /**
     * @return array|null
     */
    public $requiredRoles;
}
