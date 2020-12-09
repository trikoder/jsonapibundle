<?php

namespace Trikoder\JsonApiBundle\Services\ModelInput\Traits;

use Neomerx\JsonApi\Document\Error;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationInterface;

/**
 * Trait FormErrorToErrorTransformer
 */
trait FormErrorToErrorTransformer
{
    /**
     * @return array
     */
    protected function convertFormErrorsToErrors(FormErrorIterator $formErrors)
    {
        $errors = [];

        /* @var FormError $violation */
        foreach ($formErrors as $error) {
            $errors[] = $this->convertFormErrorToError($error);
        }

        return $errors;
    }

    /**
     * @return Error
     */
    protected function convertFormErrorToError(FormError $violation)
    {
        $title = $violation->getMessage();
        $detail = sprintf('Form error "%s"', $violation->getMessage());
        $source = [];

        if ($violation->getCause() && $violation->getCause() instanceof ConstraintViolationInterface) {
            $source['pointer'] = $this->parsePointerFromViolation($violation);
            $source['parameter'] = (string) $violation->getCause()->getInvalidValue();
        } elseif ($violation->getOrigin()) {
            // TODO - make diff between attributes and relationships
            $source['pointer'] = $this->parsePointerFromViolation($violation);
            $source['parameter'] = (string) $violation->getOrigin()->getData();
        }

        return new Error(
            null,
            null,
            Response::HTTP_CONFLICT,
            $this->getCodeFromViolation($violation),
            $title,
            $detail,
            $source
        );
    }

    /**
     * @return string|null
     */
    private function parsePointerFromViolation(FormError $violation)
    {
        $propertyPath = $this->getPropertyPathFromOrigin($violation->getOrigin());
        // TODO - make diff between attributes and relationships
        return '/data/attributes/' . $propertyPath;
    }

    /**
     * @return string
     */
    protected function getCodeFromViolation(FormError $violation)
    {
        if (
            $violation->getCause() instanceof ConstraintViolation
            &&
            $violation->getCause()->getConstraint() instanceof Constraint
        ) {
            return $violation->getCause()->getConstraint()->payload['code'] ?? null;
        }

        return null;
    }

    /**
     * @param FormInterface|null $form
     */
    private function getPropertyPathFromOrigin($form): string
    {
        if (null === $form) {
            return '';
        }

        if (
            $form->isRoot()
            ||
            (null !== $form->getParent() && $form->getParent()->isRoot())
        ) {
            return $form->getName();
        }

        return $this->getPropertyPathFromOrigin($form->getParent()) . '/' . $form->getName();
    }
}
