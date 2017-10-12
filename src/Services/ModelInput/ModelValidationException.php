<?php

namespace Trikoder\JsonApiBundle\Services\ModelInput;

use Exception;

/**
 * Class ModelValidationException
 * @package Trikoder\JsonApiBundle\Services\ModelInput
 */
class ModelValidationException extends Exception
{
    /**
     * @var string
     */
    private $violations;

    /**
     * ModelValidationException constructor.
     * @param array $violations
     */
    public function __construct(array $violations)
    {
        parent::__construct("Model is not valid, see included violations", 0);
        $this->violations = $violations;
    }

    /**
     * @return string
     */
    public function getViolations()
    {
        return $this->violations;
    }
}
