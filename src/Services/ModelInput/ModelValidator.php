<?php

namespace Trikoder\JsonApiBundle\Services\ModelInput;

use Neomerx\JsonApi\Contracts\Document\ErrorInterface;
use Neomerx\JsonApi\Document\Error;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Trikoder\JsonApiBundle\Contracts\ModelTools\ModelValidatorInterface;

class ModelValidator implements ModelValidatorInterface
{
    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /** @var object $model */
    protected $model;

    /**
     * ModelValidator constructor.
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param object $model
     * @return $this
     */
    public function forModel($model)
    {
        $this->model = $model;
        return $this;
    }

    /**
     * @param array $validationGroups
     * @return true|ErrorInterface[] true if valid or array of validation violations if not valid
     */
    public function validate(array $validationGroups = null)
    {
        /** @var ConstraintViolationListInterface $validationResult */
        $validationResult = $this->validator->validate($this->model, null, $validationGroups);

        if (count($validationResult) == 0) {
            return true;
        } else {
            // TODO - should we transform this?
            $errors = [];

            /** @var ConstraintViolationInterface $violation */
            foreach ($validationResult as $violation) {
                $errors[] = $this->convertViolationToError($violation);
            }

            return $errors;
        }
    }

    protected function convertViolationToError(ConstraintViolationInterface $violation)
    {
        $code = $violation->getCode();
        $title = 'Constraint violation';
        $detail = $violation->getMessage();
        $source = [];
        if ($violation->getPropertyPath()) {
            // TODO - make diff between attributes and relationships
            $source['pointer'] = '/data/attributes/' . $violation->getPropertyPath();
        }
        if ($code === 2) {
            // TODO - parameter should be string? maybe we should send this in meta?
            $source['parameter'] = $violation->getParameters();
        }

        return new Error(
            null,
            null,
            Response::HTTP_CONFLICT,
            $code,
            $title,
            $detail,
            $source
        );
    }
}