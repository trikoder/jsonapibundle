<?php

namespace Trikoder\JsonApiBundle\Controller\Traits\Actions;

use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Trikoder\JsonApiBundle\Contracts\Config\ConfigInterface;
use Trikoder\JsonApiBundle\Contracts\ModelTools\ModelInputHandlerInterface;
use Trikoder\JsonApiBundle\Contracts\ModelTools\ModelValidatorInterface;
use Trikoder\JsonApiBundle\Contracts\RepositoryInterface;
use Trikoder\JsonApiBundle\Contracts\ResponseFactoryInterface;
use Trikoder\JsonApiBundle\Services\ModelInput\ModelValidationException;
use Trikoder\JsonApiBundle\Services\ModelInput\UnhandleableModelInputException;
use Trikoder\JsonApiBundle\Services\Neomerx\EncoderService;

/**
 * Trait UpdateTrait
 */
trait UpdateTrait
{
    /**
     * @param $id
     *
     * @return object|null
     *
     * @throws ModelValidationException
     * @throws UnhandleableModelInputException
     *
     * @deprecated see \Trikoder\JsonApiBundle\Controller\Traits\Actions\UpdateTrait::updateModelFromRequestUsingId
     */
    protected function updateModelFromRequest(Request $request, $id)
    {
        return $this->updateModelFromRequestUsingId($request, $id);
    }

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
    protected function updateModelFromRequestUsingId(Request $request, $id)
    {
        /** @var ConfigInterface $config */
        $config = $this->getJsonApiConfig();

        //TODO make sure request has data in array

        /** @var RepositoryInterface $repository */
        $repository = $config->getApi()->getRepository();

        // load object
        $model = $repository->getOne($id, $config->getApi()->getFixedFiltering());

        // check if model is loaded
        if (null === $model) {
            throw new NotFoundHttpException();
        }

        $model = $this->updateModelFromRequestUsingModel($request, $model);

        // return resulting
        return $model;
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
    protected function handleUpdateModelInputFromRequest(ConfigInterface $config, $model, Request $request)
    {
        // merge it with request data
        if (true === method_exists($this, 'getUpdateInputHandler')) {
            // TODO - add check if callable, and give info it should be protected
            /** @var ModelInputHandlerInterface $handler */
            // TODO - document this possible method - this is so we can easily change input handler (add custom one or whatever)
            $handler = $this->getUpdateInputHandler();
        } else {
            $handler = $this->getJsonApiModelToolsFactory()->createInputHandler($model,
                $config->getUpdate()->getUpdateAllowedFields());
        }

        // update model input with files from request
        $modelInput = $request->request->all();
        if ($request->files->count() > 0) {
            foreach ($request->files->all() as $filesKey => $filesValue) {
                if (\array_key_exists($filesKey, $modelInput)) {
                    throw new RuntimeException(sprintf('Conflict with request files, duplicate param found in request and files %s', $filesKey));
                }
                $modelInput[$filesKey] = $filesValue;
            }
        }

        $model = $handler->forModel($model)->handle($modelInput)->getResult();

        // validate result
        if ($handler instanceof ModelValidatorInterface) {
            $validated = $handler->validate();
            if (true !== $validated) {
                throw new ModelValidationException($validated);
            }
        }

        return $model;
    }

    /**
     * @param $model
     *
     * @throws ModelValidationException
     * @throws UnhandleableModelInputException
     *
     * @internal
     */
    protected function validateUpdatedModel(ConfigInterface $config, $model)
    {
        if (true === method_exists($this, 'getUpdateValidator')) {
            // TODO - add check if callable, and give info it should be protected
            /** @var ModelValidatorInterface $validator */
            // TODO - document this possible method - this is so we can easily change validator
            $validator = $this->getUpdateValidator();
        } else {
            /** @var ModelValidatorInterface $validator */
            $validator = $this->getJsonApiModelToolsFactory()->createValidator($model);
        }

        $validated = $validator->forModel($model)->validate();

        if (true !== $validated) {
            throw new ModelValidationException($validated);
        }
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
    protected function updateModelFromRequestUsingModel(Request $request, $model)
    {
        /** @var ConfigInterface $config */
        $config = $this->getJsonApiConfig();

        //TODO make sure request has data in array

        // TODO - check if model correct class?

        $model = $this->handleUpdateModelInputFromRequest($config, $model, $request);

        $this->validateUpdatedModel($config, $model);

        /** @var RepositoryInterface $repository */
        $repository = $config->getApi()->getRepository();

        // save it
        $saveResult = $repository->save($model);
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
    protected function updateRequestFromRequest(Request $request, $id)
    {
        // TODO change to injected
        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->getJsonApiResponseFactory();
        /** @var EncoderService $encoder */
        $encoder = $this->getJsonApiEncoder();

        try {
            $model = $this->updateModelFromRequestUsingId($request, $id);
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
}
