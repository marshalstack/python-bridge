<?php

declare(strict_types=1);

namespace Marshal\PythonBridge;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'events' => $this->getEventsConfig(),
            'python' => $this->getServerConfig(),
        ];
    }

    private function getDependencies(): array
    {
        return [
            'factories' => [
                Listener\PythonEventsListener::class => Listener\PythonEventsListenerFactory::class,
                Transport\TransportInterface::class => Transport\HttpTransportFactory::class,
            ],
        ];
    }

    private function getEventsConfig(): array
    {
        return [
            Listener\PythonEventsListener::class => [
                Event\RunPythonScriptEvent::class,
            ],
        ];
    }

    private function getServerConfig(): array
    {
        return [
            'host' => 'http://localhost:8000',
        ];
    }
}
