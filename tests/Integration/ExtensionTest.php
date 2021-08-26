<?php

declare(strict_types=1);

namespace Trikoder\JsonApiBundle\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ExtensionTest extends KernelTestCase
{
    public function testKernelEventListenerEventPriorities()
    {
        static::bootKernel();

        $container = static::$kernel->getContainer();

        /** @var EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = $container->get('event_dispatcher');

        $listener = $container->get('test.trikoder.jsonapi.request_listener');

        $this->assertSame(
            16,
            $eventDispatcher->getListenerPriority(
                KernelEvents::CONTROLLER,
                [$listener, 'onKernelController']
            )
        );

        $this->assertSame(
            -10,
            $eventDispatcher->getListenerPriority(
                KernelEvents::CONTROLLER_ARGUMENTS,
                [$listener, 'onKernelControllerArguments']
            )
        );

        $this->assertSame(
            0,
            $eventDispatcher->getListenerPriority(
                KernelEvents::VIEW,
                [$listener, 'onKernelView']
            )
        );

        $this->assertSame(
            0,
            $eventDispatcher->getListenerPriority(
                KernelEvents::RESPONSE,
                [$listener, 'onKernelResponse']
            )
        );

        $this->assertSame(
            0,
            $eventDispatcher->getListenerPriority(
                KernelEvents::EXCEPTION,
                [$listener, 'onKernelException']
            )
        );
    }
}
