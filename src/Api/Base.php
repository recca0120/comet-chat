<?php

namespace Recca0120\CometChat\Api;

use Generator;
use JsonException;
use Psr\Http\Client\ClientExceptionInterface;
use Recca0120\CometChat\Client;
use Recca0120\CometChat\Paginator;

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

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    protected function paginate(
        string $method,
        string $path,
        array $query = [],
        array $headers = [],
        array $data = []
    ): Generator {
        $currentPage = 1;
        $perPage = (int) ($query['perPage'] ?? 100);
        while (true) {
            $paginator = new Paginator($this->client->sendRequest(
                $method,
                $path.'?'.http_build_query($query),
                $headers,
                $data
            ), $perPage, $currentPage);

            yield $paginator;

            if (! $paginator->hasMorePages()) {
                break;
            }

            foreach ($paginator->nextQuery() as $key => $value) {
                $query[$key] = $value;
            }

            $currentPage++;
        }
    }
}
