<?php

declare(strict_types=1);

namespace Marshal\PythonBridge\Listener;

use Marshal\PythonBridge\Transport\TransportInterface;
use Psr\Container\ContainerInterface;

final class PythonEventsListenerFactory
{
    public function __invoke(ContainerInterface $container): PythonEventsListener
    {
        $config = $container->get('config')['python'] ?? [];
        $transport = $container->get(TransportInterface::class);
        if (! $transport instanceof TransportInterface) {
            throw new \InvalidArgumentException(\sprintf(
                "Expected %s, gived %s instead",
                TransportInterface::class,
                \get_debug_type($transport)
            ));
        }

        return new PythonEventsListener($transport, $config);
    }
}
