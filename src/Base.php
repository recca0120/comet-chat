<?php

namespace Recca0120\CometChat;

use Generator;
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
        while (true) {
            $url = $path.'?'.http_build_query($query);
            $result = $this->client->sendRequest($method, $url, $headers, $data);

            foreach ($result['data'] as $row) {
                yield $row;
            }

            $pagination = $result['meta']['pagination'];
            $currentPage = $pagination['current_page'];
            $total_pages = $pagination['total_pages'];

            if ($currentPage >= $total_pages) {
                break;
            }

            $query['page']++;
        }
    }
}
