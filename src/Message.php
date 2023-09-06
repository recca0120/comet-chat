<?php

namespace Recca0120\CometChat;

use Generator;
use JsonException;
use Psr\Http\Client\ClientExceptionInterface;

class Message extends Base
{
    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function send(
        string $receiver,
        string $receiverType = 'user',
        string $category = 'message',
        string $type = 'text',
        array $data = [],
        ?array $multipleReceivers = null,
        ?array $tags = null,
        ?string $onBehalfOf = null
    ): array {
        return $this->sendRequest(
            'POST',
            'messages',
            ['onBehalfOf' => $onBehalfOf],
            [
                'receiver' => $receiver,
                'receiverType' => $receiverType,
                'category' => $category,
                'type' => $type,
                'data' => $data,
                'multipleReceivers' => $multipleReceivers,
                'tags' => $tags,
            ]
        );
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function all(
        ?string $searchKey = null,
        ?string $receiverType = null,
        ?string $affix = null,
        ?string $id = null,
        ?string $category = null,
        ?string $type = null,
        ?bool $hideDeleted = null,
        ?bool $onlyDeleted = null,
        ?bool $hideReplies = null,
        ?bool $count = null,
        ?int $sentAt = null,
        ?int $limit = null,
        ?string $conversationId = null,
        ?bool $withTags = null,
        ?array $tags = null,
        ?array $categories = null,
        ?array $types = null,
        ?int $fromTimestamp = null,
        ?int $toTimestamp = null,
        ?string $onBehalfOf = null
    ): Generator {
        $path = 'messages';
        $query = [
            'searchKey' => $searchKey,
            'receiverType' => $receiverType,
            'affix' => $affix,
            'id' => $id,
            'category' => $category,
            'type' => $type,
            'hideDeleted' => $hideDeleted,
            'onlyDeleted' => $onlyDeleted,
            'hideReplies' => $hideReplies,
            'count' => $count,
            'sentAt' => $sentAt,
            'limit' => $limit,
            'conversationId' => $conversationId,
            'withTags' => $withTags,
            'tags' => $tags,
            'categories' => $categories,
            'types' => $types,
            'fromTimestamp' => $fromTimestamp,
            'toTimestamp' => $toTimestamp,
        ];
        $headers = ['onBehalfOf' => $onBehalfOf];

        while (true) {
            $result = $this->sendRequest(
                'GET',
                $path.'?'.http_build_query($query),
                $headers,
                raw: true
            );

            foreach ($result['data'] as $row) {
                yield $row;
            }

            if ($result['meta']['current']['count'] === 0) {
                break;
            }

            $next = $result['meta']['next'];
            $query['id'] = $next['id'];
            $query['sentAt'] = $next['sentAt'];
            $query['affix'] = $next['affix'];
        }
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function get(string $id, ?string $onBehalfOf = null): array
    {
        return $this->sendRequest(
            'GET',
            'messages/'.$id,
            ['onBehalfOf' => $onBehalfOf],
        );
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function update(string $id, array $data, ?array $tags = null): array
    {
        return $this->sendRequest(
            'PUT',
            'messages/'.$id,
            [],
            ['data' => $data, 'tags' => $tags]
        );
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function delete(string $id, ?bool $permanent = null, ?string $onBehalfOf = null): array
    {
        return $this->sendRequest(
            'DELETE',
            'messages/'.$id,
            ['onBehalfOf' => $onBehalfOf],
            ['permanent' => $permanent]
        );
    }
}
