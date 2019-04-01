<?php

namespace Trikoder\JsonApiBundle\Controller\Traits\Actions;

use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;
use Trikoder\JsonApiBundle\Contracts\Config\ConfigInterface;
use Trikoder\JsonApiBundle\Contracts\ModelTools\ModelInputHandlerInterface;
use Trikoder\JsonApiBundle\Contracts\ModelTools\ModelValidatorInterface;
use Trikoder\JsonApiBundle\Contracts\RepositoryInterface;
use Trikoder\JsonApiBundle\Contracts\ResponseFactoryInterface;
use Trikoder\JsonApiBundle\Contracts\SchemaClassMapProviderInterface;
use Trikoder\JsonApiBundle\Services\ModelInput\ModelValidationException;
use Trikoder\JsonApiBundle\Services\ModelInput\UnhandleableModelInputException;
use Trikoder\JsonApiBundle\Services\Neomerx\EncoderService;

/**
 * Trait CreateTrait
 */
trait CreateTrait
{
    /**
     * @param object $emptyModel
     *
     * @return object
     *
     * @throws ModelValidationException
     * @throws UnhandleableModelInputException
     */
    protected function handleCreateModelInputFromRequest(ConfigInterface $config, $emptyModel, Request $request)
    {
        // merge it with request data
        if (true === method_exists($this, 'getCreateInputHandler')) {
            // TODO - add check if callable, and give info it should be protected
            /** @var ModelInputHandlerInterface $handler */
            // TODO - document this possible method - this is so we can easily change input handler (add custom one or whatever)
            $handler = $this->getCreateInputHandler();
        } else {
            /** @var ModelInputHandlerInterface $handler */
            $handler = $this->getJsonApiModelToolsFactory()->createInputHandler($emptyModel,
                $config->getCreate()->getCreateAllowedFields());
        }

        // update model input with files from request
        $modelInput = $request->request->all();
        if ($request->files->count() > 0) {
            foreach ($request->files->all() as $filesKey => $filesValue) {
                if (array_key_exists($filesKey, $modelInput)) {
                    throw new RuntimeException(sprintf('Conflict with request files, duplicate param found in request and files %s',
                        $filesKey));
                }
                $modelInput[$filesKey] = $filesValue;
            }
        }

        $model = $handler->forModel($emptyModel)->handle($modelInput)->getResult();

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
     * @param object $model
     *
     * @throws ModelValidationException
     * @throws UnhandleableModelInputException
     */
    protected function validateCreatedModel(ConfigInterface $config, $model)
    {
        if (true === method_exists($this, 'getCreateValidator')) {
            // TODO - add check if callable, and give info it should be protected
            /** @var ModelValidatorInterface $validator */
            // TODO - document this possible method - this is so we can easily change validator
            $validator = $this->getCreateValidator();
        } else {
            /** @var ModelValidatorInterface $validator */
            $validator = $this->getJsonApiModelToolsFactory()->createValidator($model);
        }

        // TODO - add support for validation groups in config
        $validated = $validator->forModel($model)->validate();

        if (true !== $validated) {
            // TODO get errors and send them as response
            throw new ModelValidationException($validated);
        }
    }

    /**
     * @return object
     *
     * @throws ModelValidationException
     * @throws UnhandleableModelInputException
     */
    protected function createModelFromRequest(Request $request)
    {
        /** @var ConfigInterface $config */
        $config = $this->getJsonApiConfig();

        //TODO make sure request has data in array

        // create empty object, config executes and closures
        $emptyModelFactory = $config->getCreate()->getCreateFactory();
        $emptyModel = $emptyModelFactory->create($config->getApi()->getModelClass());

        $model = $this->handleCreateModelInputFromRequest($config, $emptyModel, $request);

        $this->validateCreatedModel($config, $model);

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

        return $model;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function createCreatedFromRequest(Request $request)
    {
        // TODO change to injected

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->getJsonApiResponseFactory();
        /** @var EncoderService $encoder */
        $encoder = $this->getJsonApiEncoder();
        /** @var SchemaClassMapProviderInterface $schemaProvider */
        $schemaProvider = $this->getSchemaClassMapProvider();

        try {
            $model = $this->createModelFromRequest($request);
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

        $showLocation = null;
        if (true === method_exists($this, 'getRouter')) {
            $showRouteName = $this->findShowRouteName();
            if (null !== $showRouteName) {
                $showLocation = $this->getRouter()->generate($showRouteName, ['id' => $model->getId()], RouterInterface::ABSOLUTE_URL);
            }
        }
        $response = $responseFactory->createCreated(
            $encoder->encode($schemaProvider, $model),
            $showLocation
        );

        return $response;
    }

    /**
     * Find showAction route to this controller.
     * Returns null if route cannot be found
     *
     * @internal
     *
     * @return string|null
     */
    protected function findShowRouteName()
    {
        if (true !== method_exists($this, 'getRouter')) {
            return null;
        }
        /** @var Router $router */
        $router = $this->getRouter();
        $controllerName = \get_class($this) . '::showAction';
        $showRouteName = null;
        /** @var Route $route */
        foreach ($router->getRouteCollection() as $routeName => $route) {
            $defaults = $route->getDefaults();
            if (isset($defaults['_controller']) && $defaults['_controller'] == $controllerName) {
                $showRouteName = $routeName;
                break;
            }
        }
        if (null === $showRouteName) {
            return null;
        }

        return $showRouteName;
    }
}
