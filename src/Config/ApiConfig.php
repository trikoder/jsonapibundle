<?php

namespace Trikoder\JsonApiBundle\Config;

use Closure;
use Trikoder\JsonApiBundle\Config\Traits\LoadLazyPropertyTrait;
use Trikoder\JsonApiBundle\Contracts\Config\ApiConfigInterface;
use Trikoder\JsonApiBundle\Contracts\RepositoryInterface;
use Trikoder\JsonApiBundle\Contracts\RequestBodyDecoderInterface;
use Trikoder\JsonApiBundle\Contracts\RequestBodyValidatorInterface;

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
     * @var RequestBodyValidatorInterface
     */
    private $requestBodyValidator;

    /**
     * @var RequestBodyDecoderInterface
     */
    private $relationshipBodyDecoder;

    /**
     * @var RequestBodyValidatorInterface
     */
    private $relationshipRequestBodyValidator;

    public function __construct(
        string $modelClass,
        $repository,
        array $fixedFiltering = null,
        array $allowedIncludePaths = null,
        RequestBodyDecoderInterface $requestBodyDecoder,
        bool $allowExtraParams,
        RequestBodyValidatorInterface $requestBodyValidator,
        RequestBodyValidatorInterface $relationshipRequestBodyValidator,
        RequestBodyDecoderInterface $relationshipBodyDecoder
    ) {
        $this->modelClass = $modelClass;
        $this->fixedFiltering = $fixedFiltering;
        $this->allowedIncludePaths = $allowedIncludePaths;
        $this->requestBodyDecoder = $requestBodyDecoder;
        $this->allowExtraParams = $allowExtraParams;
        $this->repository = $repository;
        $this->requestBodyValidator = $requestBodyValidator;
        $this->relationshipRequestBodyValidator = $relationshipRequestBodyValidator;
        $this->relationshipBodyDecoder = $relationshipBodyDecoder;
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
     * @return RequestBodyDecoderInterface
     */
    public function getRelationshipRequestBodyDecoder()
    {
        return $this->relationshipBodyDecoder;
    }

    /**
     * @return bool
     */
    public function getAllowExtraParams()
    {
        return $this->allowExtraParams;
    }

    /**
     * @internal
     */
    public function getRequestBodyValidator()
    {
        return $this->requestBodyValidator;
    }

    /**
     * @internal
     */
    public function getRelationshipRequestBodyValidator()
    {
        return $this->relationshipRequestBodyValidator;
    }
}
