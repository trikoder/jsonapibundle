<?php

namespace Trikoder\JsonApiBundle\Config\Annotation;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target("ANNOTATION")
 */
class UpdateRelationshipConfig
{
    /**
     * @return array|null
     */
    public $allowedRelationships;

    /**
     * @return array|null
     */
    public $requiredRoles;
}
