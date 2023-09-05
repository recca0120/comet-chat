<?php

namespace Recca0120\CometChat;

use JsonException;
use Psr\Http\Client\ClientExceptionInterface;

abstract class Base
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    protected function sendRequest(
        string $method,
        string $path,
        array $headers = [],
        array $data = [],
        $raw = false
    ): array {
        $result = $this->client->sendRequest($method, $path, $headers, $data);

        return $raw === true ? $result : $result['data'];
    }
}
