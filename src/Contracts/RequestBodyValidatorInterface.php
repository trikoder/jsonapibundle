<?php

declare(strict_types=1);

namespace Trikoder\JsonApiBundle\Contracts;

// @todo don't depend on this
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Should only validate structure of the document as per JsonApi spec
 */
interface RequestBodyValidatorInterface
{
    /**
     * @return ConstraintViolationListInterface
     */
    public function validate(array $body);
}
