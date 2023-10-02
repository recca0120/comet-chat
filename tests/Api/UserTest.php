<?php

namespace Recca0120\CometChat\Tests\Api;

use Http\Client\Exception\HttpException;
use JsonException;
use Psr\Http\Client\ClientExceptionInterface;
use Recca0120\CometChat\Api\User;
use Recca0120\CometChat\Exceptions\QuotaExhausted;
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

//        $this->clearUsers();
        $user = $this->user->create(uid: 'user_000', name: 'test', avatar: $avatar, link: $link, withAuthToken: true);

        self::assertEquals([
            'uid' => 'user_000',
            'name' => 'test',
            'link' => $link,
            'avatar' => $avatar,
            'status' => 'offline',
            'role' => 'default',
            'createdAt' => 1694667681,
            'authToken' => 'user_000_169466768147ce45a79418c20c57234ba9694e2f',
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

        $this->user->create(uid: 'user_000', name: 'test', avatar: $avatar, link: $link, withAuthToken: true);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function test_update_user(): void
    {
        VCR::insertCassette('update_user.yaml');

        $link = 'https://foo.bar';

        $user = $this->user->update(uid: 'user_000', name: 'test', link: $link, unset: ['avatar']);

        self::assertEquals([
            'uid' => 'user_000',
            'name' => 'test',
            'link' => $link,
            'status' => 'offline',
            'role' => 'default',
            'createdAt' => 1694667681,
            'updatedAt' => 1694667756,
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
            'uid' => 'user_000',
            'name' => 'test',
            'link' => 'https://foo.bar',
            'status' => 'offline',
            'role' => 'default',
            'createdAt' => 1694667681,
            'updatedAt' => 1694667756,
        ], $this->user->get(uid: 'user_000'));
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
            'message' => 'User with UID user_000 has been deleted successfully.',
        ], $this->user->delete(uid: 'user_000', permanent: true));
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

        $this->user->delete(uid: 'user_000', permanent: true);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function test_list_users(): void
    {
        VCR::insertCassette('list_users.yaml');

//        $this->givenUsers(25);

        $pages = iterator_to_array($this->user->all(perPage: 5));
        $records = array_reduce($pages, static fn($acc, $paginator) => [...$acc, ...$paginator->items()], []);

        self::assertCount(5, $pages);
        self::assertCount(25, $records);
        self::assertEquals([
            'uid' => 'user_000',
            'name' => 'user_000',
            'status' => 'offline',
            'role' => 'default',
            'createdAt' => 1694667958,
        ], last($records));
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function test_exhausted_quota(): void
    {
        $this->expectException(QuotaExhausted::class);
        $this->expectExceptionCode(402);
        $this->expectExceptionMessage('You\'ve exhausted the quota. The allowed limit of the feature Create User for your current plan free-2023-01 is 25. Please upgrade the plan to increase the limit.');

        VCR::insertCassette('exhausted_quota.yaml');
//        $this->givenUsers(26);

        $avatar = 'https://via.placeholder.com/640x480.png/001122?text=accusamus';
        $link = 'https://ankunding.com/commodi-debitis-sed-laboriosam-aliquid-voluptatum-enim-doloremque';

        $this->user->create(uid: 'user_000', name: 'test', avatar: $avatar, link: $link, withAuthToken: true);
    }
}
