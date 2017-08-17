<?php

namespace Trikoder\JsonApiBundle\Controller\Traits;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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
use Trikoder\JsonApiBundle\Services\Neomerx\EncoderService;

/**
 * Class CreateActionTrait
 * @package Trikoder\JsonApiBundle\Controller\Traits
 */
trait CreateActionTrait
{

    /**
     * @param Request $request
     *
     * @Route("/")
     * @Method("POST")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        /** @var ConfigInterface $config */
        $config = $this->getJsonApiConfig();

        //TODO make sure request has data in array

        // create empty object, config executes and closures
        $emptyModelFactory = $config->getCreate()->getCreateFactory();
        $emptyModel = $emptyModelFactory->create($config->getApi()->getModelClass());

        // merge it with request data
        if (true === method_exists($this, 'getCreateInputHandler')) {
            /** @var ModelInputHandlerInterface $handler */
            // TODO - document this possible method - this is so we can easily change input handler (add custom one or whatever)
            $handler = $this->getCreateInputHandler();
        } else {
            /** @var ModelInputHandlerInterface $handler */
            $handler = $this->get("trikoder.jsonapi.model_tools_factory")->createInputHandler($emptyModel,
                $config->getCreate()->getCreateAllowedFields());
        }
        $model = $handler->forModel($emptyModel)->handle($request->getContent())->getResult();

        // validate result
        if (true === method_exists($this, 'getCreateValidator')) {
            /** @var ModelValidatorInterface $validator */
            // TODO - document this possible method - this is so we can easily change validator
            $validator = $this->getCreateValidator();
        } else {
            /** @var ModelValidatorInterface $validator */
            $validator = $this->get("trikoder.jsonapi.model_tools_factory")->createValidator($model);
        }

        // TODO - add support for validation groups in config
        $validated = $validator->forModel($model)->validate();

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->get("trikoder.jsonapi.response_factory");
        /** @var EncoderService $encoder */
        $encoder = $this->get('trikoder.jsonapi.encoder');

        if (true !== $validated) {
            // TODO get errors and send them as response
            $response = $responseFactory->createConflict($encoder->encodeErrors($validated));
            return $response;
        }

        // save it
        $config->getApi()->getRepository()->save($model);
        /** @var SchemaClassMapProviderInterface $schemaProvider */
        $schemaProvider = $this->get('trikoder.jsonapi.schema_class_map_provider');

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
