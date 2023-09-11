<?php

namespace Recca0120\CometChat\Tests;

use GuzzleHttp\Client as GuzzleClient;
use PHPUnit\Framework\TestCase as BaseTestCase;
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
}
