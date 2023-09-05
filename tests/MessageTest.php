<?php

namespace Recca0120\CometChat\Tests;

use JsonException;
use Psr\Http\Client\ClientExceptionInterface;
use Recca0120\CometChat\Message;
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

        $result = $this->message->send(
            receiver: 'uuid_999',
            data: ['text' => 'Hi Tom!']
        );

        self::assertEquals('356', $result['id']);
        self::assertEquals('uuid_999', $result['receiver']);
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

        $generator = $this->message->all(count: true, limit: 1000);
        self::assertCount(355, iterator_to_array($generator));
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
