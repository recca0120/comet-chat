<?php

namespace Recca0120\CometChat\Tests;

use Http\Client\Exception\HttpException;
use JsonException;
use Psr\Http\Client\ClientExceptionInterface;
use Recca0120\CometChat\AuthToken;
use VCR\VCR;

class AuthTokenTest extends TestCase
{
    protected string $fixturePath = 'auth_token';
    private AuthToken $authToken;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authToken = $this->client->authToken();
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function test_create_auth_token(): void
    {
        VCR::insertCassette('create_auth_token.yaml');
//        $this->client->user()->create(uid: 'uuid_999', name: 'uuid_999');

        self::assertEquals([
            'uid' => 'uuid_999',
            'authToken' => 'uuid_999_16938906989ce9b1f42c23c6434b8ef8872e5625',
            'createdAt' => 1693890698,
        ], $this->authToken->create(uid: 'uuid_999', force: true));
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function test_update_auth_token(): void
    {
        VCR::insertCassette('update_auth_token.yaml');

        self::assertEquals([
            'uid' => 'uuid_999',
            'authToken' => 'uuid_999_16938906989ce9b1f42c23c6434b8ef8872e5625',
            'createdAt' => 1693890698,
        ], $this->authToken->update(uid: 'uuid_999', authToken: 'uuid_999_16938906989ce9b1f42c23c6434b8ef8872e5625'));
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function test_get_auth_token(): void
    {
        VCR::insertCassette('get_auth_token.yaml');

        self::assertEquals([
            'uid' => 'uuid_999',
            'authToken' => 'uuid_999_16938906989ce9b1f42c23c6434b8ef8872e5625',
            'createdAt' => 1693890698,
        ], $this->authToken->get(uid: 'uuid_999', authToken: 'uuid_999_16938906989ce9b1f42c23c6434b8ef8872e5625'));
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function test_list_auth_tokens(): void
    {
        VCR::insertCassette('list_auth_tokens.yaml');

//        $this->authToken->flush(uid: 'uuid_999');
//        for ($i = 0; $i < 200; $i++) {
//            $this->authToken->create(uid: 'uuid_999', force: true);
//            usleep(500);
//        }

        $generator = $this->authToken->all(uid: 'uuid_999');

        self::assertCount(200, iterator_to_array($generator));
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function test_flush_auth_tokens(): void
    {
        VCR::insertCassette('flush_auth_tokens.yaml');

        self::assertEquals([
            'success' => true,
            'message' => 'Cleared Auth Tokens successfully for uid uuid_999.',
        ], $this->authToken->flush(uid: 'uuid_999'));
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function test_get_auth_token_not_exists(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(404);

        VCR::insertCassette('get_auth_token_not_exists.yaml');

        $this->authToken->get(uid: 'uuid_999', authToken: 'uuid_999_169389049788c05534c11cef2e35b3e436c60073');
    }
}
