<?php

namespace Trikoder\JsonApiBundle\Contracts\ModelTools;

/**
 * Interface ModelValidatorInterface
 * @package Trikoder\JsonApiBundle\Contracts\ModelTools
 */
interface ModelValidatorInterface
{
    /**
     * @param object $model
     * @return $this
     * TODO - check if better in contructor so it cannot be changed on runtime?
     */
    public function forModel($model);

    /**
     * @param array $validationGroups
     * @return true|array true if valid or array of validation violations if not valid
     */
    public function validate(array $validationGroups = null);
}
