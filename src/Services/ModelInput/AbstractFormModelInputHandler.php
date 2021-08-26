<?php

namespace Trikoder\JsonApiBundle\Services\ModelInput;

use Doctrine\Common\Persistence\Proxy as LegacyProxy;
use Doctrine\Persistence\Proxy;
use Symfony\Component\Form\Extension\Validator\Constraints\Form;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Form\FormInterface;
use Trikoder\JsonApiBundle\Contracts\ModelTools\ModelInputHandlerInterface;
use Trikoder\JsonApiBundle\Services\ModelInput\Traits\FormErrorToErrorTransformer;

abstract class AbstractFormModelInputHandler implements ModelInputHandlerInterface
{
    use FormErrorToErrorTransformer;

    /**
     * @var object
     */
    protected $model;

    /**
     * @var string
     */
    protected $modelClass;

    /**
     * @return FormInterface
     */
    abstract public function getForm();

    /**
     * @param object $model
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function forModel($model)
    {
        // TODO - we really do not wanna change model types once set? if we use generic handler that caluclates form on the fly
        if (null !== $this->model && null !== $this->modelClass) {
            if (!($model instanceof $this->modelClass)) {
                throw new \Exception('change of model type is not allowed');
            }
        }
        $this->model = $model;

        // make sure we are not working with doctrine proxy
        if ($this->model instanceof Proxy || $this->model instanceof LegacyProxy) {
            $this->modelClass = get_parent_class($this->model);
        } else {
            $this->modelClass = \get_class($this->model);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function handle(array $input)
    {
        $form = $this->getForm();

        try {
            $form->submit($input, false); // false is important to allow patch
        } catch (\Exception $exception) {
            // this will pack any attachment exceptions into UnhandleableModelInputException
            throw new UnhandleableModelInputException([], $exception);
        }

        // if form is not valid this would be concerned invalid input handle?
        if (false === $form->isValid()) {
            // in case validation problem is extra fields, then do not attach it as validation error
            $formErrors = $form->getErrors(true);
            $extraFieldsErrors = $formErrors->findByCodes(Form::NO_SUCH_FIELD_ERROR);
            if ($extraFieldsErrors->count() > 0) {
                $extraFields = [];
                /** @var FormErrorIterator $extraFieldsError */
                foreach ($extraFieldsErrors as $extraFieldsError) {
                    $extraFields[] = $extraFieldsError->getOrigin()->getName();
                }
                throw new UnhandleableModelInputException($extraFields);
            } else {
                throw new UnhandleableModelInputException([], new ModelValidationException($this->convertFormErrorsToErrors($formErrors)));
            }
        }

        $this->model = $form->getData();

        return $this;
    }

    /**
     * @return object model with inputs applied by handler's rules
     */
    public function getResult()
    {
        return $this->model;
    }
}
