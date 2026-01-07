<?php

namespace Souravsspace\Unsent\Tests;

use Mockery;
use PHPUnit\Framework\TestCase;
use Souravsspace\Unsent\Unsent;
use Souravsspace\Unsent\Emails;
use Souravsspace\Unsent\Contacts;
use Souravsspace\Unsent\Campaigns;
use Souravsspace\Unsent\Domains;
use Souravsspace\Unsent\Analytics;
use Souravsspace\Unsent\ApiKeys;
use Souravsspace\Unsent\ContactBooks;
use Souravsspace\Unsent\Settings;
use Souravsspace\Unsent\Suppressions;
use Souravsspace\Unsent\Templates;
use Souravsspace\Unsent\Webhooks;

class UnsentTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function testConstructorWithApiKey()
    {
        $client = new Unsent('test_api_key');
        $this->assertInstanceOf(Unsent::class, $client);
    }

    public function testConstructorThrowsExceptionWithoutApiKey()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing API key');
        
        putenv('UNSENT_API_KEY');
        new Unsent();
    }

    public function testResourceClientsAreInitialized()
    {
        $client = new Unsent('test_api_key');
        
        $this->assertInstanceOf(Emails::class, $client->emails);
        $this->assertInstanceOf(Contacts::class, $client->contacts);
        $this->assertInstanceOf(Campaigns::class, $client->campaigns);
        $this->assertInstanceOf(Domains::class, $client->domains);
        $this->assertInstanceOf(Analytics::class, $client->analytics);
        $this->assertInstanceOf(ApiKeys::class, $client->apiKeys);
        $this->assertInstanceOf(ContactBooks::class, $client->contactBooks);
        $this->assertInstanceOf(Settings::class, $client->settings);
        $this->assertInstanceOf(Suppressions::class, $client->suppressions);
        $this->assertInstanceOf(Templates::class, $client->templates);
        $this->assertInstanceOf(Webhooks::class, $client->webhooks);
    }

    public function testConstructorUsesEnvVariable()
    {
        putenv('UNSENT_API_KEY=env_test_key');
        $client = new Unsent();
        $this->assertInstanceOf(Unsent::class, $client);
        putenv('UNSENT_API_KEY');
    }

    public function testConstructorWithCustomUrl()
    {
        $client = new Unsent('test_api_key', 'https://custom.api.url');
        $this->assertInstanceOf(Unsent::class, $client);
    }
}
