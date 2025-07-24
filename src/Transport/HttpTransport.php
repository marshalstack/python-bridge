<?php

declare(strict_types=1);

namespace Marshal\PythonBridge\Transport;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;

class HttpTransport implements TransportInterface
{
    public function __construct(
        private ClientInterface $httpClient,
        private RequestFactoryInterface $requestFactory,
        private string $host
    ) {
    }

    public function run(
        string $module,
        string $function,
        array $args,
        array $paths
    ): ResponseInterface {
        // create a PSR-7 request
        $request = $this->requestFactory->createRequest(
            'POST',
            $this->host
        );

        // prepare the request body
        $payload = \json_encode([
            'module' => $module,
            'function' => $function,
            'args' => \json_encode($args),
            'paths' => \json_encode($paths),
        ]);

        // append the body
        $body = $request->getBody();
        $body->write($payload);
        $body->rewind();

        return $this->httpClient->sendRequest(
            $request->withBody($body)->withHeader('content-type', 'application/json')
        );
    }
}
