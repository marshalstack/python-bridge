<?php

declare(strict_types=1);

namespace Marshal\PythonBridge\Listener;

use Marshal\EventManager\EventListenerInterface;
use Marshal\PythonBridge\ConfigProvider;
use Marshal\PythonBridge\Event\RunPythonScriptEvent;
use Marshal\PythonBridge\Transport\TransportInterface;

class PythonEventsListener implements EventListenerInterface
{
    private const string LOGGER_NAME = ConfigProvider::PYTHON_BRIDGE_LOGGER;
    private array $validationMessages = [];

    public function __construct(private TransportInterface $transport, private array $config)
    {
    }

    public function getListeners(): array
    {
        return [
            RunPythonScriptEvent::class => ['listener' => [$this, 'onRunPythonScriptEvent']],
        ];
    }

    public function onRunPythonScriptEvent(RunPythonScriptEvent $event): void
    {
        $script = $event->getName();
        if (! isset($this->config['scripts'][$script])) {
            $event->setErrorMessage('scriptNotFound', "Script $script config not found in config");
            return;
        }

        $scriptConfig = $this->config['scripts'][$script];
        if (! $this->isValid($scriptConfig)) {
            $event->setErrorMessage('invalidScriptConfig', "Script $script validation error");
            return;
        }

        // get additional paths
        $paths = [];
        $app = \explode('::', $script)[0];
        if (isset($this->config['paths'][$app])) {
            foreach ($this->config['paths'][$app] as $path) {
                $paths[] = $path;
            }
        }

        try {
            $response = $this->transport->run(
                module: $scriptConfig['module'],
                function: $scriptConfig['function'],
                args: $event->getParams(),
                paths: $paths
            );

            $event->setResponse($response);
        } catch (\Throwable $e) {
            $event->setErrorMessage('error', $e->getMessage());
        }
    }

    private function isValid(mixed $config): bool
    {
        // @todo validation messages
        if (! \is_array($config)) {
            $this->validationMessages[] = "Must be valid array";
            return FALSE;
        }

        if (! isset($config['module'])) {
            $this->validationMessages[] = "Module not found";
            return FALSE;
        }

        if (! isset($config['function'])) {
            $this->validationMessages[] = "Function not found";
            return FALSE;
        }

        return TRUE;
    }
}
