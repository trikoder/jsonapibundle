<?php

namespace Trikoder\JsonApiBundle\Config\Annotation;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target("ANNOTATION")
 */
class CreateConfig
{
    /**
     * @return string
     */
    public $factory;

    /**
     * @return array|null
     */
    public $allowedFields;

    /**
     * @return array|null
     */
    public $requiredRoles;
}
