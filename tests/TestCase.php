<?php

namespace Recca0120\CometChat\Tests;

use GuzzleHttp\Client as GuzzleClient;
use JsonException;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Recca0120\CometChat\Client;
use VCR\VCR;

abstract class TestCase extends BaseTestCase
{
    protected Client $client;
    protected string $fixturePath = '';

    protected function setUp(): void
    {
        parent::setUp();

        VCR::configure()
            ->setCassettePath(__DIR__.'/fixtures/'.$this->fixturePath)
            ->enableRequestMatchers(['method', 'url', 'query_string', 'body', 'post_fields'])
            ->enableLibraryHooks(['curl', 'stream_wrapper']);
        VCR::turnOn();

        $config = require __DIR__.'/../config/cometchat.php';

        $this->client = new Client($config['app_id'], $config['api_key'], $config['region'], new GuzzleClient());
    }

    protected function tearDown(): void
    {
        VCR::eject();
        VCR::turnOff();
        parent::tearDown();
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    protected function givenUsers(int $count): array
    {
        $this->clearUsers();
        $user = $this->client->user();

        $result = [];
        for ($i = 0; $i < $count; $i++) {
            try {
                $uid = 'user_'.str_pad((string) $i, 3, '0', STR_PAD_LEFT);
                $result[] = $user->create(uid: $uid, name: $uid, withAuthToken: true);
                usleep(100);
            } catch (\Exception $e) {
                var_dump($e->getMessage());
                exit;
            }
        }

        return $result;
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    protected function givenMessages(int $count): array
    {
        $message = $this->client->message();
        foreach ($message->all() as $paginator) {
            foreach ($paginator as $record) {
                $message->delete($record['id'], permanent: true);
            }
        }

        $result = [];
        for ($i = 0; $i < $count; $i++) {
            try {
                $uid = 'message_'.str_pad((string) $i, 3, '0', STR_PAD_LEFT);
                $result = $message->send(receiver: $uid, data: ['text' => 'Hi Tom!'], onBehalfOf: 'conversation_001');
                usleep(100);
            } catch (\Exception $e) {
                var_dump($e->getMessage());
            }
        }

        return $result;
    }

    /**
     * @param  \Recca0120\CometChat\Api\User  $user
     * @return void
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    protected function clearUsers(): void
    {
        $user = $this->client->user();

        foreach ($user->all() as $paginator) {
            foreach ($paginator as $record) {
                $user->delete($record['uid'], permanent: true);
            }
        }
    }
}
