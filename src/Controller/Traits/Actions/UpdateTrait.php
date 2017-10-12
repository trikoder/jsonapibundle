<?php

namespace Trikoder\JsonApiBundle\Controller\Traits\Actions;

use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Trikoder\JsonApiBundle\Contracts\Config\ConfigInterface;
use Trikoder\JsonApiBundle\Contracts\ModelTools\ModelInputHandlerInterface;
use Trikoder\JsonApiBundle\Contracts\ModelTools\ModelValidatorInterface;
use Trikoder\JsonApiBundle\Contracts\RepositoryInterface;
use Trikoder\JsonApiBundle\Contracts\ResponseFactoryInterface;
use Trikoder\JsonApiBundle\Services\ModelInput\ModelValidationException;
use Trikoder\JsonApiBundle\Services\Neomerx\EncoderService;

/**
 * Trait UpdateTrait
 * @package Trikoder\JsonApiBundle\Controller\Traits\Actions
 */
trait UpdateTrait
{
    /**
     * @param Request $request
     * @param $id
     * @return null|object
     * @throws ModelValidationException
     *
     * @deprecated see \Trikoder\JsonApiBundle\Controller\Traits\Actions\UpdateTrait::updateModelFromRequestUsingId
     */
    protected function updateModelFromRequest(Request $request, $id)
    {
        return $this->updateModelFromRequestUsingId($request, $id);
    }

    /**
     * @param Request $request
     * @param $id
     * @return null|object
     * @throws ModelValidationException
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
        if(null === $model) {
            throw new NotFoundHttpException();
        }

        $model = $this->updateModelFromRequestUsingModel($request, $model);

        // return resulting
        return $model;
    }

    /**
     * @param Request $request
     * @param $model
     * @return object
     * @throws ModelValidationException
     */
    protected function updateModelFromRequestUsingModel(Request $request, $model) {
        /** @var ConfigInterface $config */
        $config = $this->getJsonApiConfig();

        //TODO make sure request has data in array

        /** @var RepositoryInterface $repository */
        $repository = $config->getApi()->getRepository();

        // TODO - check if model correct class?

        // merge it with request data
        if (true === method_exists($this, 'getUpdateInputHandler')) {
            // TODO - add check if callable, and give info it should be protected
            /** @var ModelInputHandlerInterface $handler */
            // TODO - document this possible method - this is so we can easily change input handler (add custom one or whatever)
            $handler = $this->getUpdateInputHandler();
        } else {
            /** @var ModelInputHandlerInterface $handler */
            $handler = $this->get("trikoder.jsonapi.model_tools_factory")->createInputHandler($model,
                $config->getUpdate()->getUpdateAllowedFields());
        }

        // update model input with files from request
        $modelInput = $request->request->all();
        if($request->files->count() > 0) {
            foreach ($request->files->all() as $filesKey => $filesValue) {
                if(array_key_exists($filesKey, $modelInput)) {
                    throw new RuntimeException(sprintf('Conflict with request files, duplicate param found in request and files %s', $filesKey));
                }
                $modelInput[$filesKey] = $filesValue;
            }
        }

        $model = $handler->forModel($model)->handle($modelInput)->getResult();

        // validate result
        if($handler instanceof ModelValidatorInterface) {
            $validated = $handler->validate();
            if (true !== $validated) {
                throw new ModelValidationException($validated);
            }
        }
        if (true === method_exists($this, 'getUpdateValidator')) {
            // TODO - add check if callable, and give info it should be protected
            /** @var ModelValidatorInterface $validator */
            // TODO - document this possible method - this is so we can easily change validator
            $validator = $this->getUpdateValidator();
        } else {
            /** @var ModelValidatorInterface $validator */
            $validator = $this->get("trikoder.jsonapi.model_tools_factory")->createValidator($model);
        }

        $validated = $validator->forModel($model)->validate();

        if (true !== $validated) {
            throw new ModelValidationException($validated);
        }

        // save it
        $repository->save($model);

        // return resulting
        return $model;
    }

    /**
     * @param Request $request
     * @param $id
     * @return null|object|\Symfony\Component\HttpFoundation\Response
     */
    protected function updateRequestFromRequest(Request $request, $id)
    {
        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->get("trikoder.jsonapi.response_factory");
        /** @var EncoderService $encoder */
        $encoder = $this->get('trikoder.jsonapi.encoder');

        try {
            $model = $this->updateModelFromRequest($request, $id);
        } catch (ModelValidationException $modelValidationException) {
            $response = $responseFactory->createConflict($encoder->encodeErrors($modelValidationException->getViolations()));
            return $response;
        }

        return $model;
    }
}
