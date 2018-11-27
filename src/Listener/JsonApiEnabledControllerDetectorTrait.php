<?php

namespace Trikoder\JsonApiBundle\Listener;

use Closure;
use Trikoder\JsonApiBundle\Controller\JsonApiEnabledInterface;

trait JsonApiEnabledControllerDetectorTrait
{
    /**
     * @param callable|object $controller
     *
     * @return bool
     */
    protected function isJsonApiEnabledController($controller)
    {
        // we cannot support Closure as we cannot look inside it safely
        if (true === \is_callable($controller) && false === ($controller instanceof Closure)) {
            if ($controller[0] instanceof JsonApiEnabledInterface) {
                return true;
            } else {
                return false;
            }
        } elseif (true === \is_object($controller)) {
            if ($controller instanceof JsonApiEnabledInterface) {
                return true;
            } else {
                return false;
            }
        }

        throw new \LogicException('Unsupported type provided as controller');
    }

    /**
     * @param $eventController
     *
     * @return null|object
     */
    protected function resolveControllerFromEventController($eventController)
    {
        if (true === \is_callable($eventController) && false === ($eventController instanceof Closure)) {
            return $eventController[0];
        } elseif (true === \is_callable($eventController) && true === ($eventController instanceof Closure)) {
            return null;
        } elseif (true === \is_object($eventController)) {
            return $eventController;
        } else {
            return null;
        }
    }
}
