<?php

namespace UnsentDev\Unsent\Tests;

use Mockery;
use PHPUnit\Framework\TestCase;
use UnsentDev\Unsent\Unsent;
use UnsentDev\Unsent\Emails;
use UnsentDev\Unsent\Contacts;
use UnsentDev\Unsent\Campaigns;
use UnsentDev\Unsent\Domains;
use UnsentDev\Unsent\Analytics;
use UnsentDev\Unsent\ApiKeys;
use UnsentDev\Unsent\ContactBooks;
use UnsentDev\Unsent\Settings;
use UnsentDev\Unsent\Suppressions;
use UnsentDev\Unsent\Templates;
use UnsentDev\Unsent\Webhooks;
use UnsentDev\Unsent\System;
use UnsentDev\Unsent\Events;
use UnsentDev\Unsent\Metrics;
use UnsentDev\Unsent\Stats;
use UnsentDev\Unsent\Activity;
use UnsentDev\Unsent\Teams;

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
        
        // Core email and contact resources
        $this->assertInstanceOf(Emails::class, $client->emails);
        $this->assertInstanceOf(Contacts::class, $client->contacts);
        $this->assertInstanceOf(ContactBooks::class, $client->contactBooks);
        
        // Campaign and template resources
        $this->assertInstanceOf(Campaigns::class, $client->campaigns);
        $this->assertInstanceOf(Templates::class, $client->templates);
        
        // Domain and webhook resources
        $this->assertInstanceOf(Domains::class, $client->domains);
        $this->assertInstanceOf(Webhooks::class, $client->webhooks);
        
        // Management resources
        $this->assertInstanceOf(ApiKeys::class, $client->apiKeys);
        $this->assertInstanceOf(Settings::class, $client->settings);
        $this->assertInstanceOf(Suppressions::class, $client->suppressions);
        
        // Analytics and metrics resources
        $this->assertInstanceOf(Analytics::class, $client->analytics);
        $this->assertInstanceOf(Metrics::class, $client->metrics);
        $this->assertInstanceOf(Stats::class, $client->stats);
        
        // Activity and events resources
        $this->assertInstanceOf(Activity::class, $client->activity);
        $this->assertInstanceOf(Events::class, $client->events);
        
        // Team and system resources
        $this->assertInstanceOf(Teams::class, $client->teams);
        $this->assertInstanceOf(System::class, $client->system);
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
