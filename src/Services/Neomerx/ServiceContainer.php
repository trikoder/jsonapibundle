<?php

namespace Trikoder\JsonApiBundle\Services\Neomerx;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class ServiceContainer implements ContainerInterface
{
    /**
     * @var string[]
     */
    protected $services = [];

    /**
     * @var ContainerInterface
     */
    private $fallbackContainer;

    public function __construct(ContainerInterface $fallbackContainer = null)
    {
        $this->fallbackContainer = $fallbackContainer;
    }

    /**
     * Sets a service.
     *
     * @param string $id The service identifier
     * @param object $service The service instance
     */
    public function set($id, $service)
    {
        $this->services[$id] = $service;
    }

    /**
     * Gets a service.
     *
     * @param string $id The service identifier
     * @param int $invalidBehavior The behavior when the service does not exist
     *
     * @return object The associated service
     *
     * @throws ServiceCircularReferenceException When a circular reference is detected
     * @throws ServiceNotFoundException          When the service is not defined
     *
     * @see Reference
     */
    public function get($id, $invalidBehavior = self::EXCEPTION_ON_INVALID_REFERENCE)
    {
        if (array_key_exists($id, $this->services)) {
            return $this->services[$id];
        } else {
            if (null !== $this->fallbackContainer) {
                return $this->fallbackContainer->get($id, $invalidBehavior);
            }
        }

        throw new ServiceNotFoundException($id);
    }

    /**
     * Returns true if the given service is defined.
     *
     * @param string $id The service identifier
     *
     * @return bool true if the service is defined, false otherwise
     */
    public function has($id)
    {
        if (array_key_exists($id, $this->services)) {
            return true;
        } else {
            if (null !== $this->fallbackContainer) {
                return $this->fallbackContainer->has($id);
            }
        }

        return false;
    }

    /**
     * Check for whether or not a service has been initialized.
     *
     * @param string $id
     *
     * @return bool true if the service has been initialized, false otherwise
     */
    public function initialized($id)
    {
        if (array_key_exists($id, $this->services)) {
            return true;
        } else {
            if (null !== $this->fallbackContainer) {
                return $this->fallbackContainer->initialized($id);
            }
        }

        return false;
    }

    /**
     * Gets a parameter.
     *
     * @param string $name The parameter name
     *
     * @return mixed The parameter value
     *
     * @throws InvalidArgumentException if the parameter is not defined
     */
    public function getParameter($name)
    {
        throw new InvalidArgumentException();
    }

    /**
     * Checks if a parameter exists.
     *
     * @param string $name The parameter name
     *
     * @return bool The presence of parameter in container
     */
    public function hasParameter($name)
    {
        return false;
    }

    /**
     * Sets a parameter.
     *
     * @param string $name The parameter name
     * @param mixed $value The parameter value
     */
    public function setParameter($name, $value)
    {
        // noop
    }
}
