<?php

namespace Trikoder\JsonApiBundle\Config\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target("CLASS")
 */
class Config
{
    /**
     * @var string
     */
    public $modelClass;

    /**
     * @var string
     */
    public $repository;

    /**
     * @var array
     */
    public $fixedFiltering = [];

    /**
     * @var array|null
     */
    public $allowedIncludePaths;

    /**
     * @var string
     */
    public $requestBodyDecoder;

    /**
     * @var bool
     */
    public $allowExtraParams;

    /**
     * @var Trikoder\JsonApiBundle\Config\Annotation\CreateConfig
     */
    public $create;

    /**
     * @var Trikoder\JsonApiBundle\Config\Annotation\IndexConfig
     */
    public $index;

    /**
     * @var Trikoder\JsonApiBundle\Config\Annotation\UpdateConfig
     */
    public $update;

    /**
     * @var Trikoder\JsonApiBundle\Config\Annotation\DeleteConfig
     */
    public $delete;
}
