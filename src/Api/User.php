<?php

namespace Recca0120\CometChat\Api;

use Generator;
use Http\Client\Exception\HttpException;
use JsonException;
use Psr\Http\Client\ClientExceptionInterface;
use Recca0120\CometChat\Exceptions\QuotaExhausted;

class User extends Base
{
    /**
     * @return array{
     *    uid: int|string,
     *    name: string,
     *    link: string,
     *    avatar: string,
     *    status: string,
     *    role: string,
     *    createdAt: int,
     *    authToken: string,
     *    metadata: array
     * }
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function create(
        string $uid,
        string $name,
        ?string $avatar = null,
        ?string $link = null,
        ?string $role = null,
        ?array $metadata = null,
        ?array $tags = null,
        ?bool $withAuthToken = null
    ): array {
        try {
            return $this->sendRequest('POST', 'users', [], [
                'uid' => $uid,
                'name' => $name,
                'avatar' => $avatar,
                'link' => $link,
                'role' => $role,
                'metadata' => $metadata,
                'tags' => $tags,
                'withAuthToken' => $withAuthToken,
            ]);
        } catch (HttpException $e) {
            if ($e->getCode() !== 402) {
                throw $e;
            }

            throw new QuotaExhausted($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @return array{
     *     uid: int|string,
     *     name: string,
     *     link: string,
     *     avatar: string,
     *     status: string,
     *     role: string,
     *     createdAt: int,
     *     authToken: string,
     *     metadata: array,
     *     tags: array
     *  }
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function update(
        string $uid,
        string $name,
        ?string $avatar = null,
        ?string $link = null,
        ?string $role = null,
        ?array $metadata = null,
        ?array $tags = null,
        ?array $unset = null
    ): array {
        return $this->sendRequest('PUT', 'users/'.$uid, [], [
            'uid' => $uid,
            'name' => $name,
            'avatar' => $avatar,
            'link' => $link,
            'role' => $role,
            'metadata' => $metadata,
            'tags' => $tags,
            'unset' => $unset,
        ]);
    }


    /**
     * @return array{
     *     uid: string,
     *     name: string,
     *     link: string,
     *     status: string,
     *     role: string,
     *     createdAt: int,
     *     updatedAt: int,
     * }
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function get(string $uid, ?string $onBehalfOf = null): array
    {
        return $this->sendRequest('GET', 'users/'.$uid, [
            'onBehalfOf' => $onBehalfOf,
        ]);
    }

    /**
     * @return array{
     *     success: bool,
     *     message: string,
     * }
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function delete(string $uid, bool $permanent = false): array
    {
        return $this->sendRequest('DELETE', 'users/'.$uid, [], ['permanent' => $permanent]);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function all(
        ?string $searchKey = null,
        ?array $searchIn = null,
        ?string $status = null,
        ?bool $count = null,
        ?int $perPage = 100,
        ?int $page = 1,
        ?string $role = null,
        ?bool $withTags = null,
        ?array $tags = null,
        ?array $roles = null,
        ?bool $onlyDeactivated = null,
        ?bool $withDeactivated = null,
    ): Generator {
        return $this->paginate('GET', 'users', [
            'searchKey' => $searchKey,
            'searchIn' => $searchIn,
            'status' => $status,
            'count' => $count,
            'perPage' => $perPage,
            'page' => $page,
            'role' => $role,
            'withTags' => $withTags,
            'tags' => $tags,
            'roles' => $roles,
            'onlyDeactivated' => $onlyDeactivated,
            'withDeactivated' => $withDeactivated,
        ]);
    }
}
