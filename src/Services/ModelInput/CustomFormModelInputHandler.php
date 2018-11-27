<?php

namespace Trikoder\JsonApiBundle\Services\ModelInput;

use Symfony\Component\Form\FormInterface;

/**
 * Class CustomFormModelInputHandler
 */
class CustomFormModelInputHandler extends AbstractFormModelInputHandler
{
    /**
     * @var FormInterface
     */
    private $form;

    /**
     * FormModelInputHandler constructor.
     */
    public function __construct(FormInterface $form)
    {
        $this->form = $form;
    }

    /**
     * @return FormInterface
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * {@inheritdoc}
     */
    public function forModel($model)
    {
        // add passed model to the form
        $this->getForm()->setData($model);

        return parent::forModel($model);
    }
}
