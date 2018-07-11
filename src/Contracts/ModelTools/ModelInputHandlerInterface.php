<?php

namespace Trikoder\JsonApiBundle\Contracts\ModelTools;

/**
 * Interface ModelInputHandlerInterface
 */
interface ModelInputHandlerInterface
{
    /**
     * @param object $model
     *
     * @return $this
     * TODO - check if better in contructor so it cannot be changed on runtime?
     */
    public function forModel($model);

    /**
     * @param array $input
     *
     * @return $this
     */
    public function handle(array $input);

    /**
     * @return object model with inputs applied by handler's rules
     */
    public function getResult();
}
