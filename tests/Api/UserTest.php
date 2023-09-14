<?php

namespace Recca0120\CometChat\Tests\Api;

use Http\Client\Exception\HttpException;
use JsonException;
use Psr\Http\Client\ClientExceptionInterface;
use Recca0120\CometChat\Api\User;
use Recca0120\CometChat\Tests\TestCase;
use VCR\VCR;

class UserTest extends TestCase
{
    protected string $fixturePath = 'user';
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->client->user();
    }

    /**
     * @throws JsonException
     * @throws ClientExceptionInterface
     */
    public function test_create_user(): void
    {
        VCR::insertCassette('create_user.yaml');

        $avatar = 'https://via.placeholder.com/640x480.png/001122?text=accusamus';
        $link = 'https://ankunding.com/commodi-debitis-sed-laboriosam-aliquid-voluptatum-enim-doloremque';

        $user = $this->user->create(uid: 'uuid_999', name: 'test', avatar: $avatar, link: $link, withAuthToken: true);

        self::assertEquals([
            'uid' => 'uuid_999',
            'name' => 'test',
            'link' => $link,
            'avatar' => $avatar,
            'status' => 'offline',
            'role' => 'default',
            'createdAt' => 1693890190,
            'authToken' => 'uuid_999_169389019006af5f20091733fd94305f2c626ba2',
        ], $user);
    }

    /**
     * @throws JsonException
     * @throws ClientExceptionInterface
     */
    public function test_create_user_fail(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(400);

        VCR::insertCassette('create_user_fail.yaml');

        $avatar = 'https://via.placeholder.com/640x480.png/001122?text=accusamus';
        $link = 'https://ankunding.com/commodi-debitis-sed-laboriosam-aliquid-voluptatum-enim-doloremque';

        $this->user->create(uid: 'uuid_999', name: 'test', avatar: $avatar, link: $link, withAuthToken: true);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function test_update_user(): void
    {
        VCR::insertCassette('update_user.yaml');

        $link = 'https://foo.bar';

        $user = $this->user->update(uid: 'uuid_999', name: 'test', link: $link, unset: ['avatar']);

        self::assertEquals([
            'uid' => 'uuid_999',
            'name' => 'test',
            'link' => $link,
            'status' => 'offline',
            'role' => 'default',
            'createdAt' => 1693890190,
            'updatedAt' => 1693890247,
        ], $user);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function test_get_user(): void
    {
        VCR::insertCassette('get_user.yaml');

        self::assertEquals([
            'uid' => 'uuid_999',
            'name' => 'test',
            'link' => 'https://foo.bar',
            'status' => 'offline',
            'role' => 'default',
            'createdAt' => 1693890190,
            'updatedAt' => 1693890247,
        ], $this->user->get(uid: 'uuid_999'));
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function test_delete_user(): void
    {
        VCR::insertCassette('delete_user.yaml');

        self::assertEquals([
            'success' => true,
            'message' => 'User with UID uuid_999 has been deleted successfully.',
        ], $this->user->delete(uid: 'uuid_999', permanent: true));
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function test_delete_user_fail(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(404);

        VCR::insertCassette('delete_user_fail.yaml');

        $this->user->delete(uid: 'uuid_999', permanent: true);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function test_list_users(): void
    {
        VCR::insertCassette('list_users.yaml');

        $generator = $this->user->all(perPage: 5);

        self::assertCount(25, iterator_to_array($generator));
    }
}
