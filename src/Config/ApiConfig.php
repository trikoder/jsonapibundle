<?php

namespace Trikoder\JsonApiBundle\Config;

use Closure;
use Trikoder\JsonApiBundle\Config\Traits\LoadLazyPropertyTrait;
use Trikoder\JsonApiBundle\Contracts\Config\ApiConfigInterface;
use Trikoder\JsonApiBundle\Contracts\RepositoryInterface;
use Trikoder\JsonApiBundle\Contracts\RequestBodyDecoderInterface;

/**
 * Class ApiConfig
 */
class ApiConfig implements ApiConfigInterface
{
    use LoadLazyPropertyTrait;

    /**
     * @var string
     */
    private $modelClass;

    /**
     * @var Closure|RepositoryInterface
     */
    private $repository;

    /**
     * @var array|null
     */
    private $fixedFiltering;

    /**
     * @var array|null
     */
    private $allowedIncludePaths;

    /**
     * @var RequestBodyDecoderInterface
     */
    private $requestBodyDecoder;

    /**
     * @var bool
     */
    private $allowExtraParams;

    /**
     * ApiConfig constructor.
     *
     * @param $repository
     */
    public function __construct(
        string $modelClass,
        $repository,
        array $fixedFiltering = null,
        array $allowedIncludePaths = null,
        RequestBodyDecoderInterface $requestBodyDecoder,
        bool $allowExtraParams
    ) {
        $this->modelClass = $modelClass;
        $this->fixedFiltering = $fixedFiltering;
        $this->allowedIncludePaths = $allowedIncludePaths;
        $this->requestBodyDecoder = $requestBodyDecoder;
        $this->allowExtraParams = $allowExtraParams;
        $this->repository = $repository;
    }

    /**
     * @return string
     */
    public function getModelClass()
    {
        return $this->modelClass;
    }

    /**
     * @return RepositoryInterface
     */
    public function getRepository()
    {
        return $this->lazyLoadProperty('repository');
    }

    /**
     * @return array|null
     */
    public function getFixedFiltering()
    {
        return $this->fixedFiltering;
    }

    /**
     * @return array|null
     */
    public function getAllowedIncludePaths()
    {
        return $this->allowedIncludePaths;
    }

    /**
     * @return RequestBodyDecoderInterface
     */
    public function getRequestBodyDecoder()
    {
        return $this->requestBodyDecoder;
    }

    /**
     * @return bool
     */
    public function getAllowExtraParams()
    {
        return $this->allowExtraParams;
    }
}
