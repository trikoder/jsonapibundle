<?php

namespace Trikoder\JsonApiBundle\Services\ModelInput;

use Exception;
use Neomerx\JsonApi\Document\Error;

/**
 * Class ModelValidationException
 */
class ModelValidationException extends Exception
{
    /**
     * @var Error[]
     */
    private $violations;

    /**
     * @todo
     *
     * Decouple Errors from neomerx in manner that we create ErrorInterface inside bundle and overwrite encodeErrors
     * in encoder service to require our interface but internally pass it to neomerx where interface would also be
     * satisfied as it extends it.
     *
     * Reasoning for that is we wanna avoid usage of neomerx stuff outside of bundle layer so that there are not
     * additional direct dependencies for bundle and in the future, we can move from neomerx to some other package
     * without need to change our projects and have all changes only in bundle.
     *
     * ModelValidationException constructor.
     *
     * @param Error[] $violations
     */
    public function __construct(array $violations)
    {
        parent::__construct('Model is not valid, see included violations', 0);
        $this->violations = $violations;
    }

    /**
     * @return Error[]
     */
    public function getViolations()
    {
        return $this->violations;
    }
}
