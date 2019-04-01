<?php

namespace Trikoder\JsonApiBundle\Contracts\ModelTools;

use Trikoder\JsonApiBundle\Services\ModelInput\UnhandleableModelInputException;

/**
 * Interface ModelInputHandlerInterface
 */
interface ModelInputHandlerInterface
{
    /**
     * @param object $model
     *
     * @return $this
     */
    public function forModel($model);

    /**
     * @throws UnhandleableModelInputException
     *
     * @return $this
     */
    public function handle(array $input);

    /**
     * @return object model with inputs applied by handler's rules
     */
    public function getResult();
}
