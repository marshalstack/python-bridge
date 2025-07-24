<?php

declare(strict_types=1);

namespace Marshal\PythonBridge\Transport;

use Psr\Http\Message\ResponseInterface;

interface TransportInterface
{
    public function run(string $module, string $function, array $args, array $paths): ResponseInterface;
}
