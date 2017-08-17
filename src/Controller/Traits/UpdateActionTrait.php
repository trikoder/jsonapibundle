<?php

namespace Trikoder\JsonApiBundle\Controller\Traits;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Trikoder\JsonApiBundle\Contracts\Config\ConfigInterface;
use Trikoder\JsonApiBundle\Contracts\ModelTools\ModelInputHandlerInterface;
use Trikoder\JsonApiBundle\Contracts\ModelTools\ModelValidatorInterface;
use Trikoder\JsonApiBundle\Contracts\RepositoryInterface;
use Trikoder\JsonApiBundle\Contracts\ResponseFactoryInterface;
use Trikoder\JsonApiBundle\Services\Neomerx\EncoderService;

/**
 * Class UpdateActionTrait
 * @package Trikoder\JsonApiBundle\Controller\Traits
 */
trait UpdateActionTrait
{

    /**
     * @param Request $request
     *
     * @Route("/{id}")
     * @Method({"PATCH", "PUT"})
     * @return object
     */
    public function updateAction(Request $request, $id)
    {
        /** @var ConfigInterface $config */
        $config = $this->getJsonApiConfig();

        //TODO make sure request has data in array

        /** @var RepositoryInterface $repository */
        $repository = $config->getApi()->getRepository();

        // load object
        $model = $repository->getOne($id, $config->getApi()->getFixedFiltering());

        // TODO - check if model is loaded

        // merge it with request data
        if (true === method_exists($this, 'getUpdateInputHandler')) {
            /** @var ModelInputHandlerInterface $handler */
            // TODO - document this possible method - this is so we can easily change input handler (add custom one or whatever)
            $handler = $this->getUpdateInputHandler();
        } else {
            /** @var ModelInputHandlerInterface $handler */
            $handler = $this->get("trikoder.jsonapi.model_tools_factory")->createInputHandler($model,
                $config->getUpdate()->getUpdateAllowedFields());
        }
        $model = $handler->forModel($model)->handle($request->getContent())->getResult();

        // validate result
        if (true === method_exists($this, 'getUpdateValidator')) {
            /** @var ModelValidatorInterface $validator */
            // TODO - document this possible method - this is so we can easily change validator
            $validator = $this->getUpdateValidator();
        } else {
            /** @var ModelValidatorInterface $validator */
            $validator = $this->get("trikoder.jsonapi.model_tools_factory")->createValidator($model);
        }

        $validated = $validator->forModel($model)->validate();

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->get("trikoder.jsonapi.response_factory");

        if (true !== $validated) {
            /** @var EncoderService $encoder */
            $encoder = $this->get('trikoder.jsonapi.encoder');

            // get errors and send them as response
            $response = $responseFactory->createConflict($encoder->encodeErrors($validated));
            return $response;
        }

        // save it
        $repository->save($model);

        // return resulting
        return $model;
    }
}
