<?php

namespace Trikoder\JsonApiBundle\Services\ModelInput;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class ModelToolsFactory
 */
class ModelToolsFactory
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * ModelToolsFactory constructor.
     *
     * @param FormFactoryInterface $formFactory
     * @param ValidatorInterface $validator
     * @param ObjectManager $objectManager
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        ValidatorInterface $validator,
        ObjectManager $objectManager
    ) {
        $this->formFactory = $formFactory;
        $this->validator = $validator;
        $this->objectManager = $objectManager;
    }

    /**
     * @param $model
     * @param array|null $allowedFields
     *
     * @return GenericFormModelInputHandler
     */
    public function createInputHandler($model, array $allowedFields = null)
    {
        $inputHandler = new GenericFormModelInputHandler($model, $allowedFields, $this->formFactory, $this->objectManager);
        $inputHandler->forModel($model);

        return $inputHandler;
    }

    /**
     * @param $model
     *
     * @return ModelValidator
     */
    public function createValidator($model)
    {
        $validator = new ModelValidator($this->validator);
        $validator->forModel($model);

        return $validator;
    }
}
