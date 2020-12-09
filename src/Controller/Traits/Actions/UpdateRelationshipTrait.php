<?php

namespace Trikoder\JsonApiBundle\Controller\Traits\Actions;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Trikoder\JsonApiBundle\Contracts\Config\ConfigInterface;
use Trikoder\JsonApiBundle\Contracts\RelationshipDoesNotExistException;
use Trikoder\JsonApiBundle\Contracts\RelationshipRepositoryInterface;
use Trikoder\JsonApiBundle\Contracts\RepositoryInterface;
use Trikoder\JsonApiBundle\Contracts\ResourceDoesNotExistException;
use Trikoder\JsonApiBundle\Contracts\ResponseFactoryInterface;
use Trikoder\JsonApiBundle\Services\ModelInput\ModelValidationException;
use Trikoder\JsonApiBundle\Services\ModelInput\UnhandleableModelInputException;
use Trikoder\JsonApiBundle\Services\Neomerx\EncoderService;

/**
 * Trait UpdateRelationshipTrait
 */
trait UpdateRelationshipTrait
{
    /**
     * @param $id
     *
     * @return object|null
     *
     * @throws ModelValidationException
     * @throws UnhandleableModelInputException
     *
     * @internal
     */
    protected function updateModelFromRequestUsingIdAndRelationshipName(Request $request, $id, string $relationshipName)
    {
        /** @var ConfigInterface $config */
        $config = $this->getJsonApiConfig();

        if (false === $this->validateRequestRelationshipName(
                $relationshipName, $config->getUpdateRelationship()->getAllowedRelationships()
            )
        ) {
            /** @var ResponseFactoryInterface $responseFactory */
            $responseFactory = $this->getJsonApiResponseFactory();

            return $responseFactory->createErrorFromException($this->getForbiddenRelationshipException($relationshipName));
        }

        /** @var RepositoryInterface $repository */
        $repository = $config->getApi()->getRepository();

        // load object
        $model = $repository->getOne($id, $config->getApi()->getFixedFiltering());

        // check if model is loaded
        if (null === $model) {
            throw new NotFoundHttpException();
        }

        return $this->updateModelFromRequestUsingModelAndRelationshipName($request, $model, $relationshipName);
    }

    /**
     * @param $model
     *
     * @return object
     *
     * @throws ModelValidationException
     * @throws UnhandleableModelInputException
     *
     * @internal
     */
    protected function updateModelFromRequestUsingModelAndRelationshipName(
        Request $request,
        $model,
        string $relationshipName
    ) {
        /** @var ConfigInterface $config */
        $config = $this->getJsonApiConfig();

        //TODO make sure request has data in array

        // TODO - check if model correct class?

        /** @var RelationshipRepositoryInterface $repository */
        $repository = $config->getApi()->getRepository();

        if (!$repository instanceof RelationshipRepositoryInterface) {
            throw new \Exception(sprintf('Expected instance of "%s%", "%s" given', RelationshipRepositoryInterface::class, \get_class($repository)));
        }

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->getJsonApiResponseFactory();

        try {
            switch ($method = $request->getMethod()) {
                case Request::METHOD_POST:
                    $saveResult = $repository->addToRelationship($model, $relationshipName, $request->request->all());
                    break;
                case Request::METHOD_DELETE:
                    $saveResult = $repository->removeFromRelationship($model, $relationshipName,
                        $request->request->all());
                    break;
                default:
                    // 405 instead?
                    return $responseFactory->createErrorFromException(new BadRequestHttpException(sprintf('Unsupported method %s', $method)));
            }
        } catch (RelationshipDoesNotExistException $exception) {
            return $responseFactory->createErrorFromException($this->getForbiddenRelationshipException($relationshipName));
        } catch (ResourceDoesNotExistException $exception) {
            return $responseFactory->createErrorFromException(new ConflictHttpException('One or more referenced resources does not exist'));
        }

        // if repository returned result, we take it as new model
        if (null !== $saveResult) {
            // if repository returned different class for model, we consider it error
            if (false === ($saveResult instanceof $model)) {
                throw new \LogicException(sprintf('Repository result is not a valid, expected object of type %s, got %s', \get_class($model), \get_class($saveResult)));
            }

            return $saveResult;
        }

        // return resulting
        return $model;
    }

    /**
     * @param $id
     *
     * @return object|\Symfony\Component\HttpFoundation\Response|null
     */
    protected function updateRequestFromRelationshipRequest(Request $request, $id, string $relationshipName)
    {
        // TODO change to injected
        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->getJsonApiResponseFactory();
        /** @var EncoderService $encoder */
        $encoder = $this->getJsonApiEncoder();

        try {
            $model = $this->updateModelFromRequestUsingIdAndRelationshipName($request, $id, $relationshipName);
        } catch (ModelValidationException $modelValidationException) {
            $response = $responseFactory->createConflict($encoder->encodeErrors($modelValidationException->getViolations()));

            return $response;
        } catch (UnhandleableModelInputException $unhandleableModelInputException) {
            if ($unhandleableModelInputException->getPrevious() instanceof ModelValidationException && $unhandleableModelInputException->getPrevious()->hasViolations()) {
                $response = $responseFactory->createConflict($encoder->encodeErrors($unhandleableModelInputException->getPrevious()->getViolations()));
            } else {
                $response = $responseFactory->createErrorFromException(new BadRequestHttpException($unhandleableModelInputException->getMessage()));
            }

            return $response;
        }

        return $model;
    }

    private function validateRequestRelationshipName(string $relationshipName, $allowedRelationships): bool
    {
        return
            null === $allowedRelationships
            ||
            \in_array($relationshipName, $allowedRelationships, true);
    }

    private function getForbiddenRelationshipException($relationshipName)
    {
        return new AccessDeniedHttpException(sprintf('Forbidden relationship %s', $relationshipName));
    }
}
