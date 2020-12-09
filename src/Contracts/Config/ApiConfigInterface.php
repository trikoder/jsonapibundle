<?php

namespace Trikoder\JsonApiBundle\Contracts\Config;

use Trikoder\JsonApiBundle\Contracts\RepositoryInterface;
use Trikoder\JsonApiBundle\Contracts\RequestBodyDecoderInterface;
use Trikoder\JsonApiBundle\Contracts\RequestBodyValidatorInterface;

/**
 * Interface ApiConfigInterface
 */
interface ApiConfigInterface
{
    /**
     * Model class which this controller handles
     *
     * @return string
     */
    public function getModelClass();

    /**
     * Model repository
     *
     * @return RepositoryInterface
     */
    public function getRepository();

    /**
     * List of filters that are appliend on every repository fetch
     *
     * @return array
     */
    public function getFixedFiltering();

    /**
     * List of include paths that are allowed in requests for this controller [] for nothing is allowed, null for everything is allowed
     *
     * @return array|null
     */
    public function getAllowedIncludePaths();

    /**
     * @return RequestBodyDecoderInterface
     */
    public function getRequestBodyDecoder();

    /**
     * @return RequestBodyDecoderInterface
     */
    public function getRelationshipRequestBodyDecoder();

    /**
     * @return RequestBodyValidatorInterface
     */
    public function getRequestBodyValidator();

    /**
     * @return RequestBodyValidatorInterface
     */
    public function getRelationshipRequestBodyValidator();

    /**
     * Flag if we allow extra params in request, if false, only params that are recognized by JsonApi are allowed
     *
     * @return bool
     */
    public function getAllowExtraParams();
}
