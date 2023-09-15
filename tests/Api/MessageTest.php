<?php

namespace Recca0120\CometChat\Tests\Api;

use JsonException;
use Psr\Http\Client\ClientExceptionInterface;
use Recca0120\CometChat\Api\Message;
use Recca0120\CometChat\Tests\TestCase;
use VCR\VCR;

class MessageTest extends TestCase
{
    protected string $fixturePath = 'message';
    private Message $message;

    protected function setUp(): void
    {
        parent::setUp();
        $this->message = $this->client->message();
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function test_send_message(): void
    {
        VCR::insertCassette('send_message.yaml');

//        $this->givenUsers(2);

        $result = $this->message->send(
            receiver: 'user_001',
            data: ['text' => 'Hi Tom!'],
            onBehalfOf: 'user_000'
        );

        self::assertEquals([
            'id' => '2144',
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
                            'status' => 'offline',
                            'role' => 'default',
                            'createdAt' => 1694808268,
                        ],
                        'entityType' => 'user',
                    ],
                    'receiver' => [
                        'entity' => [
                            'uid' => 'user_001',
                            'name' => 'user_001',
                            'status' => 'offline',
                            'role' => 'default',
                            'createdAt' => 1694808269,
                            'conversationId' => 'user_000_user_user_001',
                        ],
                        'entityType' => 'user',
                    ],
                ],
            ],
            'sentAt' => 1694808288,
            'updatedAt' => 1694808288,
        ], $result);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function test_list_messages(): void
    {
        VCR::insertCassette('list_message.yaml');

//        for ($i = 0; $i < 200; $i++) {
//            $this->message->send(
//                receiver: 'uuid_02',
//                data: ['text' => 'message'.str_pad((string) $i, 3, '0', STR_PAD_LEFT)]
//            );
//            usleep(500);
//        }

        $pages = iterator_to_array($this->message->all(count: true, limit: 1000));
        $records = array_reduce($pages, static fn($acc, $paginator) => [...$acc, ...$paginator->items()], []);

        self::assertCount(2, $pages);
        self::assertCount(355, $records);
        self::assertEquals([
            'id' => '355',
            'conversationId' => 'app_system_user_uuid_02',
            'sender' => 'app_system',
            'receiverType' => 'user',
            'receiver' => 'uuid_02',
            'category' => 'message',
            'type' => 'text',
            'data' => [
                'text' => 'message150',
                'entities' => [
                    'sender' => [
                        'entity' => [
                            'uid' => 'app_system',
                            'name' => 'System',
                            'role' => 'default',
                            'status' => 'offline',
                        ],
                        'entityType' => 'user',
                    ],
                    'receiver' => [
                        'entity' => [
                            'uid' => 'uuid_02',
                            'link' => 'http://major-tom-web-app.test/@username_02',
                            'name' => 'name_02',
                            'role' => 'default',
                            'avatar' => 'http://major-tom-web-app.test/storage/avatar/avatar_02_960.jpg',
                            'status' => 'offline',
                            'createdAt' => 1693269115,
                            'updatedAt' => 1693818100,
                        ],
                        'entityType' => 'user',
                    ],
                ],
            ],
            'sentAt' => 1693887765,
            'updatedAt' => 1693887765,
        ], last($records));
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function test_get_message(): void
    {
        VCR::insertCassette('get_message.yaml');

        $result = $this->message->get(id: '356');

        self::assertEquals('uuid_999', $result['receiver']);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function test_update_message(): void
    {
        VCR::insertCassette('update_message.yaml');

        $result = $this->message->update(id: '356', data: [
            'text' => 'update',
        ]);

        self::assertEquals('uuid_999', $result['receiver']);
        self::assertEquals('edited', $result['data']['action']);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function test_delete_message(): void
    {
        VCR::insertCassette('delete_message.yaml');

        $result = $this->message->delete(id: '356', permanent: true);

        self::assertEquals('uuid_999', $result['receiver']);
        self::assertEquals('deleted', $result['data']['action']);
    }
}
