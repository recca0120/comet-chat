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
        while (true) {
            $result = $this->sendRequest(
                'GET',
                'conversations?'.http_build_query([
                    'conversationType' => $conversationType,
                    'withTags' => $withTags,
                    'tags' => $tags,
                    'withUserAndGroupTags' => $withUserAndGroupTags,
                    'userTags' => $userTags,
                    'groupTags' => $groupTags,
                    'unread' => $unread,
                    'perPage' => $perPage,
                    'page' => $page,
                ]),
                ['onBehalfOf' => $onBehalfOf],
                raw: true
            );

            foreach ($result['data'] as $data) {
                yield $data;
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
}
