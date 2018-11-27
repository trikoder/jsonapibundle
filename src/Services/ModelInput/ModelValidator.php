<?php

namespace Trikoder\JsonApiBundle\Services\ModelInput;

use Neomerx\JsonApi\Contracts\Document\ErrorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Trikoder\JsonApiBundle\Contracts\ModelTools\ModelValidatorInterface;
use Trikoder\JsonApiBundle\Services\ModelInput\Traits\ConstraintViolationToErrorTransformer;

class ModelValidator implements ModelValidatorInterface
{
    use ConstraintViolationToErrorTransformer;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /** @var object $model */
    protected $model;

    /**
     * ModelValidator constructor.
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param object $model
     *
     * @return $this
     */
    public function forModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @param array $validationGroups
     *
     * @return true|ErrorInterface[] true if valid or array of validation violations if not valid
     */
    public function validate(array $validationGroups = null)
    {
        /** @var ConstraintViolationListInterface $validationResult */
        $validationResult = $this->validator->validate($this->model, null, $validationGroups);

        if (0 == \count($validationResult)) {
            return true;
        } else {
            return $this->convertViolationsToErrors($validationResult);
        }
    }
}
