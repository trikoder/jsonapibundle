<?php

namespace Trikoder\JsonApiBundle\Listener;

use Trikoder\JsonApiBundle\Controller\JsonApiEnabledInterface;

trait JsonApiEnabledControllerDetectorTrait
{
    /**
     * @param callable|object $controller
     * @return bool
     */
    protected function isJsonApiEnabledController($controller)
    {
        if (true === is_callable($controller)) {
            if ($controller[0] instanceof JsonApiEnabledInterface) {
                return true;
            } else {
                return false;
            }
        } elseif (true === is_object($controller)) {
            if ($controller instanceof JsonApiEnabledInterface) {
                return true;
            } else {
                return false;
            }
        }

        throw new \LogicException("Unsupported type provided as controller");
    }
}
