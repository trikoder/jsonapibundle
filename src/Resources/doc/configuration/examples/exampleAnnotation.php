<?php

use Trikoder\JsonApiBundle\Config\Annotation as JsonApiConfig;

/**
 * @JsonApiConfig\Config(
 *     modelClass="\stdClass",
 *     repository="trikoder.jsonapi.doctrine_repository_factory",
 *     requestBodyDecoder="trikoder.jsonapi.request_body_decoder",
 *     fixedFiltering={},
 *     allowedIncludePaths=null,
 *     allowExtraParams=false,
 *     index=@JsonApiConfig\IndexConfig(
 *         allowedSortFields=null,
 *         allowedFilteringParameters=null,
 *         defaultSort={},
 *         defaultPagination={},
 *         allowedFields=null,
 *         requiredRoles=null
 *     ),
 *     create=@JsonApiConfig\CreateConfig(
 *         factory="trikoder.jsonapi.simple_model_factory",
 *         allowedFields=null,
 *         requiredRoles=null
 *     ),
 *     update=@JsonApiConfig\UpdateConfig(
 *         allowedFields=null,
 *         requiredRoles=null
 *     ),
 *     delete=@JsonApiConfig\DeleteConfig(
 *         requiredRoles=null
 *     )
 * )
 */
class Controller
{
}
