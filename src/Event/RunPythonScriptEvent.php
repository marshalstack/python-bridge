<?php

declare(strict_types=1);

namespace Marshal\PythonBridge\Event;

use Marshal\EventManager\ErrorMessagesTrait;
use Marshal\EventManager\EventParametersTrait;
use Psr\Http\Message\ResponseInterface;

class RunPythonScriptEvent
{
    use EventParametersTrait;
    use ErrorMessagesTrait;

    private ResponseInterface $response;

    public function __construct(private string $name, array $params)
    {
        $this->setParams($params);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getResponse(): ResponseInterface
    {
        if (! $this->hasResponse()) {
            throw new \RuntimeException("Response not set");
        }

        return $this->response;
    }

    public function hasResponse(): bool
    {
        return isset($this->response);
    }

    public function setResponse(ResponseInterface $response): static
    {
        $this->response = $response;
        return $this;
    }
}
