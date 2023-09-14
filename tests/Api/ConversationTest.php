<?php

namespace Recca0120\CometChat\Tests\Api;

use JsonException;
use Psr\Http\Client\ClientExceptionInterface;
use Recca0120\CometChat\Api\Conversation;
use Recca0120\CometChat\Tests\TestCase;
use VCR\VCR;

class ConversationTest extends TestCase
{
    protected string $fixturePath = 'conversation';
    private Conversation $conversation;

    protected function setUp(): void
    {
        parent::setUp();
        $this->conversation = $this->client->conversation();
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function test_list_conversations(): void
    {
        VCR::insertCassette('list_conversation.yaml');

//        $this->givenUsers(25);
//        $this->givenMessages(24);

        $pages = iterator_to_array($this->conversation->all(unread: true, perPage: 5));
        $records = array_reduce($pages, static fn($acc, $paginator) => [...$acc, ...$paginator->items()], []);

        self::assertCount(24, $records);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function test_list_conversations_by_user(): void
    {
        VCR::insertCassette('list_conversation_by_user.yaml');

//        $this->givenUsers(25);
//        $this->givenMessages(24);

        $pages = iterator_to_array($this->conversation->all(unread: true, perPage: 5, onBehalfOf: 'conversation_001'));
        $records = array_reduce($pages, static fn($acc, $paginator) => [...$acc, ...$paginator->items()], []);

        self::assertCount(1, $records);
    }
}
