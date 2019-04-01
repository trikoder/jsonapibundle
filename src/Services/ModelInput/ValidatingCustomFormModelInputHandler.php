<?php

namespace Trikoder\JsonApiBundle\Services\ModelInput;

use Trikoder\JsonApiBundle\Contracts\ModelTools\ModelValidatorInterface;

/**
 * Class CustomFormModelInputHandler
 */
class ValidatingCustomFormModelInputHandler extends CustomFormModelInputHandler implements ModelValidatorInterface
{
    /**
     * @var bool
     */
    private $validated = false;

    /**
     * @var array
     */
    private $validationViolations;

    /**
     * {@inheritdoc}
     */
    public function forModel($model)
    {
        // reset validated state
        $this->validated = false;

        return parent::forModel($model);
    }

    /**
     * {@inheritdoc}
     */
    public function handle(array $input)
    {
        // todo, validate it before returning result ?
        // if invalid, throw that model validation exception
        return parent::handle($input);
    }

    /**
     * @param array $validationGroups
     *
     * @return true|array true if valid or array of validation violations if not valid
     */
    public function validate(array $validationGroups = null)
    {
        if (false === $this->validated) {
            $this->validated = true;

            if ($this->getForm()->isValid()) {
                $this->validationViolations = true;
            } else {
                $this->validationViolations = $this->convertFormErrorsToErrors($this->getForm()->getErrors(true));
            }
        }

        return $this->validationViolations;
    }
}
