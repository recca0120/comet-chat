<?php

namespace Recca0120\CometChat;

use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\ServiceProvider;

class CometChatServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/comet-chat.php', 'comet-chat');

        $this->app->singleton(Client::class, function () {
            $config = config('comet-chat');

            return new Client($config['app_id'], $config['api_key'], $config['region'], new GuzzleClient());
        });

        $this->app->singleton(User::class, fn() => $this->app->make(Client::class)->user());
        $this->app->singleton(AuthToken::class, fn() => $this->app->make(Client::class)->authToken());
        $this->app->singleton(Message::class, fn() => $this->app->make(Client::class)->message());
        $this->app->singleton(Conversation::class, fn() => $this->app->make(Client::class)->conversation());
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/comet-chat.php' => config_path('comet-chat.php'),
            ], 'comet-chat');
        }
    }
}
