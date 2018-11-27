<?php

namespace Trikoder\JsonApiBundle\Services\ModelInput;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\ObjectManager;
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
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var FormInterface
     */
    private $form;

    /**
     * GenericFormModelInputHandler constructor.
     *
     * @param object $model
     */
    public function __construct(
        $model,
        $allowedFields = null,
        FormFactoryInterface $formFactory,
        ObjectManager $objectManager // TODO - remove this dependancy and add withMetaData method to enable usage without Doctrine entity
    ) {
        $this->formFactory = $formFactory;
        $this->allowedFields = $allowedFields;
        $this->objectManager = $objectManager;

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

        if (null === $this->allowedFields) {
            $this->allowedFields = $this->calculateAllFields();
        }

        return $this;
    }

    /**
     * Gets list of all fields from model meta data
     *
     * @return array
     */
    private function calculateAllFields()
    {
        /** @var ClassMetadata $classMetadata */
        $classMetadata = $this->objectManager->getClassMetadata($this->modelClass);

        // get all fields, relations, and identifiers
        $fields = array_unique(array_merge(
            $classMetadata->getFieldNames(),
            $classMetadata->getAssociationNames(),
            $classMetadata->getIdentifierFieldNames()
        ));

        return $fields;
    }

    private function createForm()
    {
        $formBuilder = $this->formFactory->create(FormType::class, $this->model, [
            'method' => 'POST',
            // TODO - check for csrf as it returned errors, need to enable it?
            // 'csrf_protection' => false // false because we cannot server csrf
        ]);

        /** @var ClassMetadata $classMetadata */
        $classMetadata = $this->objectManager->getClassMetadata($this->modelClass);

        /*
         * handle some known form types
         */
        foreach ($this->allowedFields as $fieldName) {
            $fieldOptions = [];
            $fieldType = $classMetadata->getTypeOfField($fieldName);
            switch ($fieldType) {
                case 'date':
                    $fieldOptions['widget'] = 'single_text';
                    $fieldOptions['view_timezone'] = 'UTC';
                    break;
                case 'datetime':
                    $fieldOptions['widget'] = 'single_text';
                    $fieldOptions['view_timezone'] = 'UTC';
                    break;
                case 'array':
                    $fieldOptions['entry_type'] = TextType::class;
                    $fieldOptions['allow_add'] = true;
                    $fieldOptions['allow_delete'] = true;
                    $fieldOptions['delete_empty'] = true;
                    break;
            }

            $formBuilder->add($fieldName, null, $fieldOptions);
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
