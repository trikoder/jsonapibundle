<?php

namespace Trikoder\JsonApiBundle\Services\ModelInput;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class GenericFormModelInputHandler
 */
class GenericFormModelInputHandler extends AbstractFormModelInputHandler
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var null
     */
    private $allowedFields;

    /**
     * @var FormInterface
     */
    private $form;

    /**
     * @var GenericModelMetaData
     */
    private $modelMetaData;

    /**
     * @var ModelMetaDataFactory
     */
    private $metaDataFactory;

    /**
     * @var array
     */
    private $formBuilderOptions;

    public function __construct(
        $model,
        FormFactoryInterface $formFactory,
        ModelMetaDataFactory $metaDataFactory,
        $allowedFields = null,
        array $formBuilderOptions
    ) {
        $this->formFactory = $formFactory;
        $this->metaDataFactory = $metaDataFactory;
        $this->allowedFields = $allowedFields;
        $this->formBuilderOptions = $formBuilderOptions;

        $this->forModel($model);
    }

    /**
     * @param object $model
     *
     * @return $this
     */
    public function forModel($model)
    {
        parent::forModel($model);

        $this->modelMetaData = $this->metaDataFactory->getMetaDataForModel($this->modelClass);

        if (null === $this->allowedFields) {
            $this->allowedFields = $this->modelMetaData->getAllFields();
        }

        return $this;
    }

    private function createForm()
    {
        $formBuilderOptions = array_merge(['method' => 'POST'], $this->formBuilderOptions);

        $formBuilder = $this->formFactory->create(FormType::class, $this->model, $formBuilderOptions);

        /*
         * handle some known form types
         */
        foreach ($this->allowedFields as $fieldName) {
            $fieldOptions = [];
            $type = null;
            $fieldType = $this->modelMetaData->getTypeForField($fieldName);
            switch ($fieldType) {
                case 'DateTime':
                case 'datetime':
                case 'date':
                    $type = DateTimeType::class;
                    $fieldOptions['widget'] = 'single_text';
                    $fieldOptions['view_timezone'] = 'UTC';
                    break;
                case 'array':
                    $type = CollectionType::class;
                    $fieldOptions['entry_type'] = TextType::class;
                    $fieldOptions['allow_add'] = true;
                    $fieldOptions['allow_delete'] = true;
                    $fieldOptions['delete_empty'] = true;
                    break;
                case 'bool':
                    $type = CheckboxType::class;
                    break;
                default:
                    // if we do not have doctrine entity, we cannot leave form to autoguess
                    if ($this->modelMetaData instanceof GenericModelMetaData) {
                        $type = TextType::class;
                    }
            }

            $formBuilder->add($fieldName, $type, $fieldOptions);
        }

        $this->form = $formBuilder;
    }

    /**
     * @return FormInterface
     */
    public function getForm()
    {
        if (null === $this->form) {
            $this->createForm();
        }

        return $this->form;
    }
}
