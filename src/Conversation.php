<?php

namespace Recca0120\CometChat;

use Generator;
use JsonException;
use Psr\Http\Client\ClientExceptionInterface;

class Conversation extends Base
{
    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function all(
        ?string $conversationType = null,
        ?bool $withTags = null,
        ?array $tags = null,
        ?bool $withUserAndGroupTags = null,
        ?array $userTags = null,
        ?array $groupTags = null,
        ?bool $unread = null,
        int $perPage = 100,
        int $page = 1,
        ?string $onBehalfOf = null
    ): Generator {
        return $this->paginate('GET', 'conversations', [
            'conversationType' => $conversationType,
            'withTags' => $withTags,
            'tags' => $tags,
            'withUserAndGroupTags' => $withUserAndGroupTags,
            'userTags' => $userTags,
            'groupTags' => $groupTags,
            'unread' => $unread,
            'perPage' => $perPage,
            'page' => $page,
        ], ['onBehalfOf' => $onBehalfOf]);
    }
}
