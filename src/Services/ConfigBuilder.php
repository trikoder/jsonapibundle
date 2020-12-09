<?php

namespace Trikoder\JsonApiBundle\Services;

use Closure;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Trikoder\JsonApiBundle\Config\Annotation;
use Trikoder\JsonApiBundle\Config\ApiConfig;
use Trikoder\JsonApiBundle\Config\Config;
use Trikoder\JsonApiBundle\Config\CreateConfig;
use Trikoder\JsonApiBundle\Config\DeleteConfig;
use Trikoder\JsonApiBundle\Config\IndexConfig;
use Trikoder\JsonApiBundle\Config\UpdateConfig;
use Trikoder\JsonApiBundle\Config\UpdateRelationshipConfig;
use Trikoder\JsonApiBundle\Contracts\Config\ApiConfigInterface;
use Trikoder\JsonApiBundle\Model\ModelFactoryResolverInterface;
use Trikoder\JsonApiBundle\Repository\RepositoryFactoryInterface;
use Trikoder\JsonApiBundle\Repository\RepositoryResolverInterface;

/**
 * Class ConfigBuilder
 */
class ConfigBuilder
{
    /**
     * @var ContainerInterface
     */
    private $serviceContainer;

    /**
     * @var array
     */
    private $defaults;

    /**
     * ConfigBuilder constructor.
     */
    public function __construct(array $defaults, ContainerInterface $container)
    {
        // we need whole container so we can fetch any annotation referenced service
        $this->serviceContainer = $container;
        $this->defaults = $defaults;
    }

    /**
     * Creates config using provided annotation, if non supplied, config is with defaults
     *
     * @return Config
     */
    public function fromAnnotation(Annotation\Config $configAnnotation = null)
    {
        if (null === $configAnnotation->index) {
            $configAnnotation->index = new Annotation\IndexConfig();
        }
        if (null === $configAnnotation->create) {
            $configAnnotation->create = new Annotation\CreateConfig();
        }
        if (null === $configAnnotation->update) {
            $configAnnotation->update = new Annotation\UpdateConfig();
        }
        if (null === $configAnnotation->delete) {
            $configAnnotation->delete = new Annotation\DeleteConfig();
        }

        if (null === $configAnnotation->updateRelationship) {
            $configAnnotation->updateRelationship = new Annotation\UpdateRelationshipConfig();
        }

        // prepare config parts
        $configApi = $this->createApiConfig($configAnnotation);
        $configIndex = $this->createIndexConfig($configAnnotation); // TODO - review index name for retreive (R of CRUD)
        $configCreate = $this->createCreateConfig($configAnnotation, $configApi);
        $configUpdate = $this->createUpdateConfig($configAnnotation);
        $configDelete = $this->createDeleteConfig($configAnnotation);
        $configUpdateRelationship = $this->createUpdateRelationshipConfig($configAnnotation);

        $config = new Config($configApi, $configCreate, $configIndex, $configUpdate, $configDelete, $configUpdateRelationship);

        return $config;
    }

    /**
     * @return ApiConfig
     */
    protected function createApiConfig(Annotation\Config $configAnnotation = null)
    {
        $modelClass = $this->annotationValueIfNotNull($configAnnotation, function ($configAnnotation) {
            return $configAnnotation->modelClass;
        }, $this->defaults['model_class']);

        $repository = $this->annotationValueIfNotNull($configAnnotation, function ($configAnnotation) {
            return $configAnnotation->repository;
        }, $this->defaults['repository']);
        if (true === \is_string($repository)) {
            if ($this->serviceContainer->has($repository)) {
                $repository = $this->serviceContainer->get($repository);
            } else {
                throw new \RuntimeException(sprintf('Value for repository setting must be valid service, given value: %s', $repository));
            }
        }
        // if resolver or factory, put closure to resolve the repo
        if (true === ($repository instanceof RepositoryResolverInterface)) {
            $repository = function () use ($modelClass, $repository) {
                /* @var RepositoryResolverInterface $repository */
                return $repository->resolve($modelClass);
            };
        } else {
            if (true === ($repository instanceof RepositoryFactoryInterface)) {
                $repository = function () use ($modelClass, $repository) {
                    /* @var RepositoryFactoryInterface $repository */
                    return $repository->create($modelClass);
                };
            }
        }

        $requestBodyDecoder = $this->annotationValueIfNotNull($configAnnotation, function ($configAnnotation) {
            return $configAnnotation->requestBodyDecoder;
        }, $this->defaults['request_body_decoder']);
        if (true === \is_string($requestBodyDecoder)) {
            if ($this->serviceContainer->has($requestBodyDecoder)) {
                $requestBodyDecoder = $this->serviceContainer->get($requestBodyDecoder);
            } else {
                throw new \RuntimeException(sprintf('String value for RequestBodyDecoder setting must be valid service, given value: %s', $requestBodyDecoder));
            }
        }

        $relationshipRequestBodyDecoder = $this->annotationValueIfNotNull($configAnnotation, function ($configAnnotation) {
            return $configAnnotation->relationshipRequestBodyDecoder;
        }, $this->defaults['relationship_request_body_decoder']);
        if (true === \is_string($relationshipRequestBodyDecoder)) {
            if ($this->serviceContainer->has($relationshipRequestBodyDecoder)) {
                $relationshipRequestBodyDecoder = $this->serviceContainer->get($relationshipRequestBodyDecoder);
            } else {
                throw new \RuntimeException(sprintf('String value for RequestBodyDecoder setting must be valid service, given value: %s', $relationshipRequestBodyDecoder));
            }
        }

        $requestBodyValidator = $this->annotationValueIfNotNull($configAnnotation, function ($configAnnotation) {
            return $configAnnotation->requestBodyValidator;
        }, $this->defaults['request_body_validator']);
        if (true === \is_string($requestBodyValidator)) {
            if ($this->serviceContainer->has($requestBodyValidator)) {
                $requestBodyValidator = $this->serviceContainer->get($requestBodyValidator);
            } else {
                throw new \RuntimeException(sprintf('String value for RequestBodyValidator setting must be valid service, given value: %s', $requestBodyValidator));
            }
        }

        $relationshipRequestBodyValidator = $this->annotationValueIfNotNull($configAnnotation, function ($configAnnotation) {
            return $configAnnotation->requestBodyValidator;
        }, $this->defaults['relationship_request_body_validator']);
        if (true === \is_string($relationshipRequestBodyValidator)) {
            if ($this->serviceContainer->has($relationshipRequestBodyValidator)) {
                $relationshipRequestBodyValidator = $this->serviceContainer->get($relationshipRequestBodyValidator);
            } else {
                throw new \RuntimeException(sprintf('String value for RequestBodyValidator setting must be valid service, given value: %s', $relationshipRequestBodyValidator));
            }
        }

        $fixedFiltering = $this->annotationValueIfNotNull($configAnnotation, function ($configAnnotation) {
            return $configAnnotation->fixedFiltering;
        }, $this->defaults['fixed_filtering']);

        $allowedIncludePaths = $this->annotationValueIfNotNull($configAnnotation, function ($configAnnotation) {
            return $configAnnotation->allowedIncludePaths;
        }, $this->defaults['allowed_include_paths']);

        $allowExtraParams = $this->annotationValueIfNotNull($configAnnotation, function ($configAnnotation) {
            return $configAnnotation->allowExtraParams;
        }, $this->defaults['allow_extra_params']);

        $config = new ApiConfig(
            $modelClass,
            $repository,
            $fixedFiltering,
            $allowedIncludePaths,
            $requestBodyDecoder,
            $allowExtraParams,
            $requestBodyValidator,
            $relationshipRequestBodyValidator,
            $relationshipRequestBodyDecoder
        );

        return $config;
    }

    /**
     * @return IndexConfig
     */
    protected function createIndexConfig(Annotation\Config $configAnnotation = null)
    {
        $allowedSortFields = $this->annotationValueIfNotNull($configAnnotation, function ($configAnnotation) {
            return $configAnnotation->index->allowedSortFields;
        }, $this->defaults['index']['allowed_sort_fields']);

        $allowedFilteringParameters = $this->annotationValueIfNotNull($configAnnotation, function ($configAnnotation) {
            return $configAnnotation->index->allowedFilteringParameters;
        }, $this->defaults['index']['allowed_filtering_parameters']);

        $defaultSort = $this->annotationValueIfNotNull($configAnnotation, function ($configAnnotation) {
            return $configAnnotation->index->defaultSort;
        }, $this->defaults['index']['default_sort']);

        $defaultPagination = $this->annotationValueIfNotNull($configAnnotation, function ($configAnnotation) {
            return $configAnnotation->index->defaultPagination;
        }, $this->defaults['index']['default_pagination']);

        $allowedFields = $this->annotationValueIfNotNull($configAnnotation, function ($configAnnotation) {
            return $configAnnotation->index->allowedFields;
        }, $this->defaults['index']['allowed_fields']);

        $requiredRoles = $this->annotationValueIfNotNull($configAnnotation, function ($configAnnotation) {
            return $configAnnotation->index->requiredRoles;
        }, $this->defaults['index']['required_roles']);

        $config = new IndexConfig(
            $allowedSortFields,
            $allowedFilteringParameters,
            $defaultSort,
            $defaultPagination,
            $allowedFields,
            $requiredRoles
        );

        return $config;
    }

    /**
     * @return CreateConfig
     */
    protected function createCreateConfig(
        Annotation\Config $configAnnotation = null,
        ApiConfigInterface $apiConfig = null
    ) {
        $factory = $this->annotationValueIfNotNull($configAnnotation, function ($configAnnotation) {
            return $configAnnotation->create->factory;
        }, $this->defaults['create']['factory']);
        if (true === \is_string($factory)) {
            if ($this->serviceContainer->has($factory)) {
                $factory = $this->serviceContainer->get($factory);
            } else {
                throw new \RuntimeException(sprintf('String value for create factory setting must be valid service, given value: %s', $factory));
            }
        }
        if (true === ($factory instanceof ModelFactoryResolverInterface)) {
            $factory = $factory->resolve($apiConfig->getModelClass());
        }

        $allowedFields = $this->annotationValueIfNotNull($configAnnotation, function ($configAnnotation) {
            return $configAnnotation->create->allowedFields;
        }, $this->defaults['create']['allowed_fields']);

        $requiredRoles = $this->annotationValueIfNotNull($configAnnotation, function ($configAnnotation) {
            return $configAnnotation->create->requiredRoles;
        }, $this->defaults['create']['required_roles']);

        $config = new CreateConfig($factory, $allowedFields, $requiredRoles);

        return $config;
    }

    /**
     * @return UpdateConfig
     */
    protected function createUpdateConfig(Annotation\Config $configAnnotation = null)
    {
        $allowedFields = $this->annotationValueIfNotNull($configAnnotation, function ($configAnnotation) {
            return $configAnnotation->update->allowedFields;
        }, $this->defaults['update']['allowed_fields']);

        $requiredRoles = $this->annotationValueIfNotNull($configAnnotation, function ($configAnnotation) {
            return $configAnnotation->update->requiredRoles;
        }, $this->defaults['update']['required_roles']);

        $config = new UpdateConfig($allowedFields, $requiredRoles);

        return $config;
    }

    /**
     * @return DeleteConfig
     */
    protected function createDeleteConfig(Annotation\Config $configAnnotation = null)
    {
        $requiredRoles = $this->annotationValueIfNotNull($configAnnotation, function ($configAnnotation) {
            return $configAnnotation->delete->requiredRoles;
        }, $this->defaults['delete']['required_roles']);

        $config = new DeleteConfig($requiredRoles);

        return $config;
    }

    /**
     * @return UpdateRelationshipConfig
     */
    protected function createUpdateRelationshipConfig(Annotation\Config $configAnnotation = null)
    {
        $allowedRelationships = $this->annotationValueIfNotNull($configAnnotation, function ($configAnnotation) {
            return $configAnnotation->updateRelationship->allowedRelationships;
        }, null);

        $requiredRoles = $this->annotationValueIfNotNull($configAnnotation, function ($configAnnotation) {
            return $configAnnotation->updateRelationship->requiredRoles;
        }, null);

        $config = new UpdateRelationshipConfig($allowedRelationships, $requiredRoles);

        return $config;
    }

    /**
     * Helper method to select between config options
     *
     * @param Annotation\Config $configAnnotation
     * @param $alternativeValue
     */
    protected function annotationValueIfNotNull(
        Annotation\Config $configAnnotation = null,
        Closure $propertyFetcher,
        $alternativeValue
    ) {
        if (null !== $configAnnotation) {
            $configValue = $propertyFetcher($configAnnotation);
            if (null !== $configValue) {
                return $configValue;
            }
        }

        return $alternativeValue;
    }
}
