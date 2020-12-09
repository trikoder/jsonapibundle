<?php

namespace Trikoder\JsonApiBundle\Services\ModelInput\Traits;

use Neomerx\JsonApi\Document\Error;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Trait ConstraintViolationToErrorTransformer
 */
trait ConstraintViolationToErrorTransformer
{
    /**
     * @param array|ConstraintViolationListInterface $violations
     *
     * @return array
     */
    protected function convertViolationsToErrors(ConstraintViolationListInterface $violations)
    {
        $errors = [];

        /** @var ConstraintViolationInterface $violation */
        foreach ($violations as $violation) {
            $errors[] = $this->convertViolationToError($violation);
        }

        return $errors;
    }

    /**
     * @return Error
     */
    protected function convertViolationToError(ConstraintViolationInterface $violation)
    {
        $code = $violation->getCode();
        $title = $violation->getMessage();
        $detail = sprintf('Constraint violation "%s"', $title);
        $source = [];
        if ($violation->getPropertyPath()) {
            // TODO - make diff between attributes and relationships
            $source['pointer'] = '/data/attributes/' . str_replace('.', '/', $violation->getPropertyPath());
            $source['parameter'] = (string) $violation->getInvalidValue();
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
