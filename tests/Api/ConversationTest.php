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

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function test_get_user_conversation(): void
    {
        VCR::insertCassette('get_user_conversation.yaml');

//        $this->givenUsers(2);
//        $this->givenMessages(1, 'user_000', 'user_001');

        self::assertEquals([
            'conversationId' => 'user_000_user_user_001',
            'conversationType' => 'user',
            'unreadMessageCount' => '0',
            'createdAt' => 1694804476,
            'updatedAt' => 1694804476,
            'lastMessage' => [
                'id' => '1059',
                'conversationId' => 'user_000_user_user_001',
                'sender' => 'user_000',
                'receiverType' => 'user',
                'receiver' => 'user_001',
                'category' => 'message',
                'type' => 'text',
                'data' => [
                    'text' => 'Hi Tom!',
                    'entities' => [
                        'sender' => [
                            'entity' => [
                                'uid' => 'user_000',
                                'name' => 'user_000',
                                'role' => 'default',
                                'status' => 'offline',
                                'createdAt' => 1694804472,
                            ],
                            'entityType' => 'user',
                        ],
                        'receiver' => [
                            'entity' => [
                                'uid' => 'user_001',
                                'name' => 'user_001',
                                'role' => 'default',
                                'status' => 'offline',
                                'createdAt' => 1694804473,
                                'conversationId' => 'user_000_user_user_001',
                            ],
                            'entityType' => 'user',
                        ],
                    ],
                ],
                'sentAt' => 1694804476,
                'updatedAt' => 1694804476,
            ],
            'conversationWith' => [
                'uid' => 'user_001',
                'name' => 'user_001',
                'status' => 'offline',
                'role' => 'default',
                'createdAt' => 1694804473,
                'conversationId' => 'user_000_user_user_001',
            ],
        ], $this->conversation->getUserConversation('user_001', 'user_000'));
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function test_update_user_conversation(): void
    {
        VCR::insertCassette('udpate_user_conversation.yaml');

//        $this->givenUsers(2);
//        $this->givenMessages(1, 'user_000', 'user_001');

        self::assertEquals([
            'conversationId' => 'user_000_user_user_001',
            'conversationType' => 'user',
            'unreadMessageCount' => '0',
            'createdAt' => 1694804476,
            'updatedAt' => 1694804476,
            'lastMessage' => [
                'id' => '1059',
                'conversationId' => 'user_000_user_user_001',
                'sender' => 'user_000',
                'receiverType' => 'user',
                'receiver' => 'user_001',
                'category' => 'message',
                'type' => 'text',
                'data' => [
                    'text' => 'Hi Tom!',
                    'entities' => [
                        'sender' => [
                            'entity' => [
                                'uid' => 'user_000',
                                'name' => 'user_000',
                                'role' => 'default',
                                'status' => 'offline',
                                'createdAt' => 1694804472,
                            ],
                            'entityType' => 'user',
                        ],
                        'receiver' => [
                            'entity' => [
                                'uid' => 'user_001',
                                'name' => 'user_001',
                                'role' => 'default',
                                'status' => 'offline',
                                'createdAt' => 1694804473,
                                'conversationId' => 'user_000_user_user_001',
                            ],
                            'entityType' => 'user',
                        ],
                    ],
                ],
                'sentAt' => 1694804476,
                'updatedAt' => 1694804476,
            ],
            'conversationWith' => [
                'uid' => 'user_001',
                'name' => 'user_001',
                'status' => 'offline',
                'role' => 'default',
                'createdAt' => 1694804473,
                'conversationId' => 'user_000_user_user_001',
            ],
            'tags' => ['sent'],
        ], $this->conversation->updateUserConversation('user_001', 'user_000', ['sent']));
    }
}
