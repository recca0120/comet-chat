<?php

namespace Recca0120\CometChat\Api;

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
        return $this->paginate(
            'GET',
            'users/'.$uid.'/auth_tokens',
            ['page' => 1]
        );
    }
}
