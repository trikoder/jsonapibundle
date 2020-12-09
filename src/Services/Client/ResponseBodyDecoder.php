<?php

namespace Trikoder\JsonApiBundle\Services\Client;

use InvalidArgumentException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Trikoder\JsonApiBundle\Contracts\SchemaClassMapProviderInterface;
use Trikoder\JsonApiBundle\Schema\AbstractSchema;
use Trikoder\JsonApiBundle\Services\Neomerx\FactoryService;

class ResponseBodyDecoder
{
    /**
     * @var FactoryService
     */
    private $factoryService;
    /**
     * @var SchemaClassMapProviderInterface
     */
    private $schemaClassMap;

    private $resourceMap;

    private $propertyAccessor;

    public function __construct(FactoryService $factoryService, SchemaClassMapProviderInterface $schemaClassMap, PropertyAccessorInterface $propertyAccessor = null)
    {
        $this->factoryService = $factoryService;
        $this->schemaClassMap = $schemaClassMap;
        $this->resourceMap = $this->schemaClassMapToResourceMap($schemaClassMap);

        if (null === $propertyAccessor) {
            $this->propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
                ->enableMagicCall()
                ->enableExceptionOnInvalidIndex()
                //->enableExceptionOnInvalidPropertyPath()
                ->getPropertyAccessor();
        } else {
            $this->propertyAccessor = $propertyAccessor;
        }
    }

    /**
     * @return object
     */
    public function decode(string $json, SchemaClassMapProviderInterface $schemaClassMap = null)
    {
        if (null !== $schemaClassMap) {
            $resourceMap = $this->schemaClassMapToResourceMap($schemaClassMap);
        } else {
            $resourceMap = $this->resourceMap;
        }

        $body = json_decode($json, true);

        if (null === $body['data']) {
            return null;
        }

        if (empty($body['data']) && !\is_array($body['data'])) {
            throw new InvalidArgumentException('Provided JsonApi payload does not contain any data');
        }

        // get main resource
        if (\array_key_exists('included', $body)) {
            // reorganize includedes to be type-id keyed
            $included = [];
            foreach ($body['included'] as $includedItem) {
                if (empty($includedItem['id'])) {
                    throw new InvalidArgumentException('Included resource must have identifier');
                }
                $includedKey = sprintf('%s-%s', $includedItem['type'], $includedItem['id']);
                $included[$includedKey] = $includedItem;
            }
        } else {
            $included = null;
        }

        // if one or multiple?
        if (\array_key_exists('type', $body['data'])) {
            $resourceType = $this->getResourceForType($body['data']['type'], $resourceMap);
            $resource = $this->populateResource($body['data'], $resourceType, $included, $resourceMap);
        } else {
            $resource = [];
            foreach ($body['data'] as $resourceItem) {
                $resourceType = $this->getResourceForType($resourceItem['type'], $resourceMap);
                $resource[] = $this->populateResource($resourceItem, $resourceType, $included, $resourceMap);
            }
        }

        return $resource;
    }

    private function getResourceForType(string $type, array $resourceMap): string
    {
        return $resourceMap[$type];
    }

    private function populateResource(array $payload, $resourceClass, array $included = null, $resourceMap)
    {
        $resource = new $resourceClass();

        // populate id
        if (\array_key_exists('id', $payload)) {
            $this->propertyAccessor->setValue($resource, 'id', $payload['id']);
        } else {
            throw new InvalidArgumentException('Resource must have identifier');
        }

        // populate attributes
        if (\array_key_exists('attributes', $payload)) {
            foreach ($payload['attributes'] as $fieldName => $fieldValue) {
                $this->propertyAccessor->setValue($resource, $fieldName, $fieldValue);
            }
        }

        // populate relations
        if (\array_key_exists('relationships', $payload)) {
            foreach ($payload['relationships'] as $relationName => $relationPayload) {
                if (!empty($relationPayload['data'])) {
                    // if one or many? if array is numeric indexed then it is list of objects, if assoc, then it is one object
                    if (\array_key_exists(0, $relationPayload['data'])) {
                        // many
                        $relationResource = [];

                        foreach ($relationPayload['data'] as $relationItemPayload) {
                            $relationResourceId = $relationItemPayload['id'];
                            $relationResourceType = $this->getResourceForType($relationItemPayload['type'], $resourceMap);
                            $relationResourcePayload = \array_key_exists(sprintf('%s-%s', $relationItemPayload['type'], $relationResourceId), $included) ? $included[sprintf('%s-%s', $relationItemPayload['type'], $relationResourceId)] : $relationItemPayload;
                            $relationResource[] = $this->populateResource($relationResourcePayload, $relationResourceType, $included, $resourceMap);
                        }
                    } else {
                        // one
                        $relationResourceId = $relationPayload['data']['id'];
                        $relationResourceType = $this->getResourceForType($relationPayload['data']['type'], $resourceMap);
                        $relationResourcePayload = \array_key_exists(sprintf('%s-%s', $relationPayload['data']['type'], $relationResourceId), $included) ? $included[sprintf('%s-%s', $relationPayload['data']['type'], $relationResourceId)] : $relationPayload['data'];
                        $relationResource = $this->populateResource($relationResourcePayload, $relationResourceType, $included, $resourceMap);
                    }

                    $this->propertyAccessor->setValue($resource, $relationName, $relationResource);
                }
            }
        }

        return $resource;
    }

    private function schemaClassMapToResourceMap(SchemaClassMapProviderInterface $schemaClassMap): array
    {
        $resourceMap = [];
        $container = $this->factoryService->createContainer($schemaClassMap->getMap());

        foreach ($schemaClassMap->getMap() as $modelClass => $schemaClass) {
            /** @var AbstractSchema $schema */
            $schema = $container->getSchemaByType($modelClass);

            $resourceType = $schema->getResourceType();

            $resourceMap[$resourceType] = $modelClass;
        }

        return $resourceMap;
    }
}
