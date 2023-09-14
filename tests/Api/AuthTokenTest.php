<?php

namespace Recca0120\CometChat\Tests\Api;

use Http\Client\Exception\HttpException;
use JsonException;
use Psr\Http\Client\ClientExceptionInterface;
use Recca0120\CometChat\Api\AuthToken;
use Recca0120\CometChat\Tests\TestCase;
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

//        $this->givenUsers(1);

        self::assertEquals([
            'uid' => 'user_000',
            'authToken' => 'user_000_1694668129d2eca40a65abba29e6860224018ad8',
            'createdAt' => 1694668129,
        ], $this->authToken->create(uid: 'user_000', force: true));
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function test_update_auth_token(): void
    {
        VCR::insertCassette('update_auth_token.yaml');

        self::assertEquals([
            'uid' => 'user_000',
            'authToken' => 'user_000_1694668129d2eca40a65abba29e6860224018ad8',
            'createdAt' => 1694668129,
        ], $this->authToken->update(uid: 'user_000', authToken: 'user_000_1694668129d2eca40a65abba29e6860224018ad8'));
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function test_get_auth_token(): void
    {
        VCR::insertCassette('get_auth_token.yaml');

        self::assertEquals([
            'uid' => 'user_000',
            'authToken' => 'user_000_1694668129d2eca40a65abba29e6860224018ad8',
            'createdAt' => 1694668129,
        ], $this->authToken->get(uid: 'user_000', authToken: 'user_000_1694668129d2eca40a65abba29e6860224018ad8'));
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
            'message' => 'Cleared Auth Tokens successfully for uid user_000.',
        ], $this->authToken->flush(uid: 'user_000'));
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

        $this->authToken->get(uid: 'user_000', authToken: 'user_000_169389049788c05534c11cef2e35b3e436c60073');
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function test_list_auth_tokens(): void
    {
        VCR::insertCassette('list_auth_tokens.yaml');

//        $this->authToken->flush(uid: 'user_000');
//        for ($i = 0; $i < 200; $i++) {
//            $this->authToken->create(uid: 'user_000', force: true);
//            usleep(100);
//        }

        $pages = iterator_to_array($this->authToken->all('user_000', 5));
        $records = array_reduce($pages, static fn($acc, $paginator) => [...$acc, ...$paginator->items()], []);

        self::assertCount(40, $pages);
        self::assertCount(200, $records);
    }
}
