<?php

namespace Trikoder\JsonApiBundle\Config\Annotation;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target("ANNOTATION")
 */
class IndexConfig
{
    /**
     * @return array|null
     */
    public $allowedSortFields;

    /**
     * @return array|null
     */
    public $allowedFilteringParameters;

    /**
     * @return array|null
     */
    public $defaultSort;

    /**
     * @return array|null
     */
    public $defaultPagination;

    /**
     * @return array|null
     */
    public $allowedFields;
}
