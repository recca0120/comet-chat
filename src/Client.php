<?php

namespace Recca0120\CometChat;

use Http\Client\Exception\HttpException;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use JsonException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Recca0120\CometChat\Api\AuthToken;
use Recca0120\CometChat\Api\BlockUser;
use Recca0120\CometChat\Api\Conversation;
use Recca0120\CometChat\Api\Message;
use Recca0120\CometChat\Api\User;

class Client
{
    private ClientInterface $client;
    private RequestFactoryInterface $requestFactory;
    private StreamFactoryInterface $streamFactory;

    public function __construct(
        private readonly string $appId,
        private readonly string $apiKey,
        private readonly string $region = 'us',
        ?ClientInterface $client = null,
        ?RequestFactoryInterface $requestFactory = null,
        ?StreamFactoryInterface $streamFactory = null
    ) {
        $this->client = $client ?? Psr18ClientDiscovery::find();
        $this->requestFactory = $requestFactory ?? Psr17FactoryDiscovery::findRequestFactory();
        $this->streamFactory = $streamFactory ?? Psr17FactoryDiscovery::findStreamFactory();
    }

    public function user(): User
    {
        return new User($this);
    }

    public function authToken(): AuthToken
    {
        return new AuthToken($this);
    }

    public function blockUser(): BlockUser
    {
        return new BlockUser($this);
    }

    public function message(): Message
    {
        return new Message($this);
    }

    public function conversation(): Conversation
    {
        return new Conversation($this);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function sendRequest(string $method, string $url, array $headers = [], array $data = []): array
    {
        if (! static::hasHttpScheme($url)) {
            $url = $this->getUrl($url);
        }

        $request = $this->requestFactory->createRequest($method, $url)
            ->withHeader('accept', 'application/json')
            ->withHeader('apikey', $this->apiKey)
            ->withHeader('content-type', 'application/json')
            ->withBody($this->streamFactory->createStream(json_encode(array_filter($data), JSON_THROW_ON_ERROR)));

        $headers = array_filter($headers);
        foreach ($headers as $key => $value) {
            $request = $request->withHeader($key, $value);
        }

        $response = $this->client->sendRequest($request);

        $result = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        if ($response->getStatusCode() !== 200) {
            throw new HttpException($result['error']['message'], $request, $response);
        }

        return $result;
    }

    private function getUrl($path): string
    {
        return sprintf("https://%s.api-%s.cometchat.io/v3/%s", $this->appId, $this->region, rtrim($path, '/'));
    }

    private static function hasHttpScheme(string $url): bool
    {
        return (bool) preg_match('/^http(s)?:\/\//', $url);
    }
}
