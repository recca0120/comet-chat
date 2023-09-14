<?php

namespace Recca0120\CometChat\Tests\Api;

use JsonException;
use Psr\Http\Client\ClientExceptionInterface;
use Recca0120\CometChat\Api\BlockUser;
use Recca0120\CometChat\Tests\TestCase;
use VCR\VCR;

class BlockUserTest extends TestCase
{
    protected string $fixturePath = 'block_user';
    private BlockUser $blockUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->blockUser = $this->client->blockUser();
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function test_block_user(): void
    {
        VCR::insertCassette('block_user.yaml');

//        $this->givenUsers(2);

        self::assertEquals(
            [
                'user_001' => [
                    'success' => true,
                    'message' => 'The user with UID user_000 has blocked user with UID user_001 successfully.',
                ],
            ],
            $this->blockUser->blockUser('user_000', ['user_001']),
        );
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function test_unblock_user(): void
    {
        VCR::insertCassette('unblock_user.yaml');

//        $this->givenUsers(2);

        self::assertEquals(
            [
                'user_001' => [
                    'success' => true,
                    'message' => 'The user with UID user_000 has unblocked user with UID user_001 successfully.',
                ],
            ],
            $this->blockUser->unblockUser('user_000', ['user_001']),
        );
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function test_list_block_users(): void
    {
        VCR::insertCassette('list_block_users.yaml');

        $pages = iterator_to_array($this->blockUser->all('user_000', 5));

        self::assertEquals([
            'uid' => 'user_024',
            'name' => 'user_024',
            'status' => 'offline',
            'role' => 'default',
            'blockedByMe' => true,
            'blockedByMeAt' => 1694666040,
            'blockedAt' => 1694666040,
            'createdAt' => 1694665810,
            'conversationId' => "user_000_user_user_024",
        ], last($pages)->last());
    }
}
