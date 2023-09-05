<?php

namespace Recca0120\CometChat\Tests;

use Psr\Http\Client\ClientExceptionInterface;
use Recca0120\CometChat\Conversation;
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
     * @throws \JsonException
     */
    public function test_list_conversations(): void
    {
        VCR::insertCassette('list_conversation.yaml');

//        $user = $this->client->user();
//
//        foreach ($user->all() as $record) {
//            $user->delete($record['uid'], permanent: true);
//        }
//
//        for ($i = 0; $i < 25; $i++) {
//            try {
//                $uid = 'conversation_'.str_pad((string) $i, 3, '0', STR_PAD_LEFT);
//                $user->create(uid: $uid, name: $uid, withAuthToken: true);
//                usleep(500);
//            } catch (\Exception $e) {
//                dump($e->getMessage());
//                exit;
//            }
//        }

//        $message = $this->client->message();
//        foreach ($message->all() as $record) {
//            $message->delete($record['id'], permanent: true);
//        }
//
//        for ($i = 1; $i < 25; $i++) {
//            try {
//                $uid = 'conversation_'.str_pad((string) $i, 3, '0', STR_PAD_LEFT);
//                $message->send(receiver: $uid, data: ['text' => 'Hi Tom!'], onBehalfOf: 'conversation_001');
//                usleep(500);
//            } catch (\Exception $e) {
//                dump($e->getMessage());
//            }
//        }

        $generator = $this->conversation->all(unread: true, perPage: 5, onBehalfOf: 'conversation_001');

        self::assertCount(1, iterator_to_array($generator));
    }
}
