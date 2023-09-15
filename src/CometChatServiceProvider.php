<?php

namespace Recca0120\CometChat;

use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\ServiceProvider;
use Recca0120\CometChat\Api\AuthToken;
use Recca0120\CometChat\Api\BlockUser;
use Recca0120\CometChat\Api\Conversation;
use Recca0120\CometChat\Api\Message;
use Recca0120\CometChat\Api\User;

class CometChatServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/cometchat.php', 'cometchat');

        $this->app->bind(Client::class, function () {
            $config = config('cometchat');

            return new Client($config['app_id'], $config['api_key'], $config['region'], new GuzzleClient());
        });

        $this->app->bind(User::class, fn() => $this->app->make(Client::class)->user());
        $this->app->bind(AuthToken::class, fn() => $this->app->make(Client::class)->authToken());
        $this->app->bind(Message::class, fn() => $this->app->make(Client::class)->message());
        $this->app->bind(Conversation::class, fn() => $this->app->make(Client::class)->conversation());
        $this->app->bind(BlockUser::class, fn() => $this->app->make(Client::class)->blockUser());
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/cometchat.php' => config_path('cometchat.php'),
            ], 'cometchat');
        }
    }
}
