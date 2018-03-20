<?php

namespace Trikoder\JsonApiBundle\Controller\Traits\Actions;

use RuntimeException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\RouterInterface;
use Trikoder\JsonApiBundle\Contracts\Config\ConfigInterface;
use Trikoder\JsonApiBundle\Contracts\ModelTools\ModelInputHandlerInterface;
use Trikoder\JsonApiBundle\Contracts\ModelTools\ModelValidatorInterface;
use Trikoder\JsonApiBundle\Contracts\ResponseFactoryInterface;
use Trikoder\JsonApiBundle\Contracts\SchemaClassMapProviderInterface;
use Trikoder\JsonApiBundle\Services\ModelInput\ModelValidationException;
use Trikoder\JsonApiBundle\Services\Neomerx\EncoderService;

/**
 * Trait CreateTrait
 * @package Trikoder\JsonApiBundle\Controller\Traits\Actions
 */
trait CreateTrait
{
    /**
     * @param ConfigInterface $config
     * @param object $emptyModel
     * @param Request $request
     * @return object
     * @throws ModelValidationException
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
            $handler = $this->get("trikoder.jsonapi.model_tools_factory")->createInputHandler($emptyModel,
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
     * @param ConfigInterface $config
     * @param object $model
     * @throws ModelValidationException
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
            $validator = $this->get("trikoder.jsonapi.model_tools_factory")->createValidator($model);
        }

        // TODO - add support for validation groups in config
        $validated = $validator->forModel($model)->validate();

        if (true !== $validated) {
            // TODO get errors and send them as response
            throw new ModelValidationException($validated);
        }
    }

    /**
     * @param Request $request
     * @return object
     * @throws ModelValidationException
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

        // save it
        $config->getApi()->getRepository()->save($model);

        return $model;
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function createCreatedFromRequest(Request $request)
    {

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->get("trikoder.jsonapi.response_factory");
        /** @var EncoderService $encoder */
        $encoder = $this->get('trikoder.jsonapi.encoder');
        /** @var SchemaClassMapProviderInterface $schemaProvider */
        $schemaProvider = $this->getSchemaClassMapProvider();

        try {
            $model = $this->createModelFromRequest($request);
        } catch (ModelValidationException $modelValidationException) {
            $response = $responseFactory->createConflict($encoder->encodeErrors($modelValidationException->getViolations()));
            return $response;
        }

        // return
        $response = $responseFactory->createCreated(
            $encoder->encode($schemaProvider, $model),
            $this->get('router')->generate($this->findShowRouteName(), ['id' => $model->getId()],
                RouterInterface::ABSOLUTE_URL)
        );
        return $response;
    }

    /**
     * Find showAction route to this controller.
     *
     * @return string
     */
    protected function findShowRouteName()
    {
        /** @var Router $router */
        $router = $this->get('router');
        $controllerName = get_class($this) . '::showAction';
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
            // TODO - update this to be more agnostic
            throw new HttpException(500, 'Show route not found for ' . $controllerName);
        }
        return $showRouteName;
    }
}
