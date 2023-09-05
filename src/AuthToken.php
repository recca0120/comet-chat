<?php

namespace Recca0120\CometChat;

use Generator;
use JsonException;
use Psr\Http\Client\ClientExceptionInterface;

class AuthToken extends Base
{
    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function create(string $uid, bool $force = false): array
    {
        return $this->sendRequest('POST', sprintf('users/%s/auth_tokens', $uid), [], ['force' => $force]);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function update(
        string $uid,
        string $authToken,
        ?string $platform = null,
        ?string $userAgent = null,
        array $appInfo = []
    ): array {
        return $this->sendRequest('PUT', sprintf('users/%s/auth_tokens/%s', $uid, $authToken), [], [
            'platform' => $platform,
            'userAgent' => $userAgent,
            'appInfo' => $appInfo,
        ]);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function get(string $uid, string $authToken): array
    {
        return $this->sendRequest('GET', sprintf('users/%s/auth_tokens/%s', $uid, $authToken));
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function flush(string $uid): array
    {
        return $this->sendRequest('DELETE', sprintf('users/%s/auth_tokens', $uid));
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function all(string $uid): Generator
    {
        $page = 1;

        while (true) {
            $result = $this->sendRequest(
                'GET',
                sprintf("users/%s/auth_tokens?%s", $uid, http_build_query(['page' => $page])),
                raw: true
            );

            foreach ($result['data'] as $row) {
                yield $row;
            }

            $pagination = $result['meta']['pagination'];
            $currentPage = $pagination['current_page'];
            $total_pages = $pagination['total_pages'];

            if ($currentPage >= $total_pages) {
                break;
            }

            $page++;
        }
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function list(string $uid): Generator
    {
        return $this->all($uid);
    }
}
