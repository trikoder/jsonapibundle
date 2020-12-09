<?php

namespace Trikoder\JsonApiBundle\Listener;

use Closure;
use LogicException;
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
        if (true === \is_object($controller)) {
            return $controller instanceof JsonApiEnabledInterface;
        }

        if (true === \is_callable($controller) && false === ($controller instanceof Closure)) {
            return $controller[0] instanceof JsonApiEnabledInterface;
        }

        throw new LogicException(sprintf('Unsupported type provided as controller: %s', \gettype($controller)));
    }

    /**
     * @param $eventController
     *
     * @return object|null
     */
    protected function resolveControllerFromEventController($eventController)
    {
        if (true === ($eventController instanceof Closure)) {
            return null;
        }

        if (true === \is_object($eventController)) {
            return $eventController;
        }

        if (true === \is_callable($eventController)) {
            return $eventController[0];
        }

        return null;
    }
}
