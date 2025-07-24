<?php

declare(strict_types=1);

namespace Marshal\PythonBridge\Transport;

use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

final class HttpTransportFactory
{
    private const string DEFAULT_HOST = 'http://localhost:8000';

    public function __invoke(ContainerInterface $container): HttpTransport
    {
        $httpClient = $container->get(ClientInterface::class);
        if (! $httpClient instanceof ClientInterface) {
            throw new \InvalidArgumentException(\sprintf(
                "Expected an instance of %s, found %s instead",
                ClientInterface::class,
                \get_debug_type($httpClient)
            ));
        }

        $requestFactory = $container->get(RequestFactoryInterface::class);
        if (! $requestFactory instanceof RequestFactoryInterface) {
            throw new \InvalidArgumentException(\sprintf(
                "Expected an instance of %s, found %s instead",
                RequestFactoryInterface::class,
                \get_debug_type($requestFactory)
            ));
        }

        $host = $container->get('config')['python']['host'] ?? self::DEFAULT_HOST;

        return new HttpTransport($httpClient, $requestFactory, $host);
    }
}
