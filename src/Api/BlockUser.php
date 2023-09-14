<?php

namespace Recca0120\CometChat\Api;

use Generator;
use JsonException;
use Psr\Http\Client\ClientExceptionInterface;

class BlockUser extends Base
{
    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function block(string $uid, array $blockedUids): array
    {
        return $this->sendRequest('POST', sprintf('users/%s/blockedusers', $uid), [], [
            'blockedUids' => $blockedUids,
        ]);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function blockUser(string $uid, array $blockedUids): array
    {
        return $this->block($uid, $blockedUids);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function unblock(string $uid, array $blockedUids): array
    {
        return $this->sendRequest('DELETE', sprintf('users/%s/blockedusers', $uid), [], [
            'blockedUids' => $blockedUids,
        ]);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function unblockUser(string $uid, array $blockedUids): array
    {
        return $this->unblock($uid, $blockedUids);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function all(string $uid, int $perPage = 100, int $page = 1): Generator
    {
        return $this->paginate('GET', sprintf('users/%s/blockedusers', $uid), [
            'perPage' => $perPage,
            'page' => $page,
        ]);
    }
}
