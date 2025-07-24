<?php

declare(strict_types=1);

namespace Marshal\PythonBridge;

class ConfigProvider
{
    public const string PYTHON_BRIDGE_LOGGER = 'marshal::python-bridge';

    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'events' => $this->getEventsConfig(),
            'loggers' => $this->getLoggerConfig(),
            'python' => $this->getServerConfig(),
        ];
    }

    private function getDependencies(): array
    {
        return [
            'delegators' => [
                Listener\PythonEventsListener::class => [
                    \Marshal\Logger\LoggerFactoryDelegator::class,
                ],
            ],
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

    private function getLoggerConfig(): array
    {
        return [
            self::PYTHON_BRIDGE_LOGGER => [
                'handlers' => [
                    \Monolog\Handler\ErrorLogHandler::class => [],
                ],
                'processors' => [
                    \Monolog\Processor\PsrLogMessageProcessor::class => [],
                ],
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
