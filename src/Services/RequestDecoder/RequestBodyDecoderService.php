<?php

namespace Trikoder\JsonApiBundle\Services\RequestDecoder;

use Trikoder\JsonApiBundle\Contracts\RequestBodyDecoderInterface;
use Trikoder\JsonApiBundle\Contracts\RequestBodyValidatorInterface;
use Trikoder\JsonApiBundle\Services\RequestDecoder\Exception\InvalidBodyForMethodException;

class RequestBodyDecoderService implements RequestBodyDecoderInterface
{
    /**
     * @var RequestBodyValidatorInterface
     */
    private $requestBodyValidator;

    public function __construct(RequestBodyValidatorInterface $requestBodyValidator)
    {
        $this->requestBodyValidator = $requestBodyValidator;
    }

    /**
     * Takes array representation of jsonapi body payload and returnes flat array as would be expected by simple POST
     *
     *
     * @return array
     *
     * @throws InvalidBodyForMethodException;
     */
    public function decode(string $requestMethod, array $body = [])
    {
        $this->requestBodyValidator->validate($requestMethod, $body);

        $decoded = [];

        if (!array_key_exists('data', $body) || null === $body['data']) {
            return $decoded;
        }

        // from data take attributes, id and relations and flatten them in array
        $data = $body['data'];

        // parse relations first
        if (array_key_exists('relationships', $data)) {
            $relationships = $data['relationships'];
            if (true === \is_array($relationships)) {
                foreach ($relationships as $relationshipName => $relationshipData) {
                    // needs data
                    if (true === array_key_exists('data', $relationshipData)) {
                        $relationshipData = $relationshipData['data'];
                        // if data is numeric array, then it is list of items
                        // if data has type key then it is one object
                        $relationshipIsMultiple = (true === isset($relationshipData[0]));
                        // we need default value
                        if (false === array_key_exists($relationshipName, $decoded)) {
                            if ($relationshipIsMultiple) {
                                $decoded[$relationshipName] = [];
                            }
                        }
                        if ($relationshipIsMultiple) {
                            // TODO - can it be non-empty and non-array
                            if (true === empty($relationshipData) || false === \is_array($relationshipData)) {
                                $decoded[$relationshipName] = [];
                            } else {
                                foreach ($relationshipData as $relationshipDataItem) {
                                    // TODO - check if item has ID
                                    $decoded[$relationshipName][] = $relationshipDataItem['id'];
                                }
                            }
                        } else {
                            if (true === empty($relationshipData)) {
                                $decoded[$relationshipName] = null;
                            } else {
                                $decoded[$relationshipName] = $relationshipData['id'];
                            }
                        }
                    }
                }
            }
        }

        // parse data for attributes
        if (true === array_key_exists('attributes', $data)) {
            $attributes = $data['attributes'];
            if (false === empty($attributes)) {
                // TODO - check if attributes is array
                foreach ($attributes as $attributeName => $attributeValue) {
                    // TODO - detection of duplicates in relations and attributes?
                    $decoded[$attributeName] = $attributeValue;
                }
            }
        }

        if (true === array_key_exists('id', $data)) {
            $decoded['id'] = $data['id'];
        }

        return $decoded;
    }
}
