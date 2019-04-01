<?php

namespace Trikoder\JsonApiBundle\Services\ModelInput;

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
     * @var bool
     */
    private $isFormCsrfEnabled;

    /**
     * ModelToolsFactory constructor.
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        ValidatorInterface $validator,
        ModelMetaDataFactory $metadataFactory,
        bool $isFormCsrfEnabled
    ) {
        $this->formFactory = $formFactory;
        $this->validator = $validator;
        $this->metadataFactory = $metadataFactory;
        $this->isFormCsrfEnabled = $isFormCsrfEnabled;
    }

    public function createInputHandler($model, array $allowedFields = null): GenericFormModelInputHandler
    {
        $formBuilderOptions = [];

        // automatically set generic input handler to not validate csrf
        if ($this->isFormCsrfEnabled) {
            $formBuilderOptions['csrf_protection'] = false;
        }

        $inputHandler = new GenericFormModelInputHandler($model, $this->formFactory, $this->metadataFactory, $allowedFields, $formBuilderOptions);
        $inputHandler->forModel($model);

        return $inputHandler;
    }

    public function createValidator($model): ModelValidator
    {
        $validator = new ModelValidator($this->validator);
        $validator->forModel($model);

        return $validator;
    }
}
