<?php

namespace Souravsspace\Unsent\Tests;

use Mockery;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Souravsspace\Unsent\Unsent;

class ResourceTest extends TestCase
{
    protected $mockClient;
    protected $unsent;

    protected function setUp(): void
    {
        $this->mockClient = Mockery::mock(Client::class);
        $this->unsent = new Unsent('test_api_key', null, true, $this->mockClient);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }

    // Analytics Tests
    public function testAnalyticsGet()
    {
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with('GET', 'https://api.unsent.dev/v1/analytics', Mockery::any())
            ->andReturn(new Response(200, [], json_encode(['data' => 'test'])));

        [$data, $error] = $this->unsent->analytics->get();
        $this->assertNotNull($data);
        $this->assertNull($error);
    }

    public function testAnalyticsGetTimeSeries()
    {
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with('GET', Mockery::pattern('/analytics\/time-series/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode(['data' => 'test'])));

        [$data, $error] = $this->unsent->analytics->getTimeSeries(['days' => 7]);
        $this->assertNotNull($data);
        $this->assertNull($error);
    }

    // ApiKeys Tests
    public function testApiKeysList()
    {
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with('GET', 'https://api.unsent.dev/v1/api-keys', Mockery::any())
            ->andReturn(new Response(200, [], json_encode(['data' => []])));

        [$data, $error] = $this->unsent->apiKeys->list();
        $this->assertNotNull($data);
        $this->assertNull($error);
    }

    public function testApiKeysCreate()
    {
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with('POST', 'https://api.unsent.dev/v1/api-keys', Mockery::any())
            ->andReturn(new Response(200, [], json_encode(['id' => 'key_123'])));

        [$data, $error] = $this->unsent->apiKeys->create(['name' => 'Test Key']);
        $this->assertNotNull($data);
        $this->assertNull($error);
    }

    // ContactBooks Tests
    public function testContactBooksList()
    {
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with('GET', 'https://api.unsent.dev/v1/contactBooks', Mockery::any())
            ->andReturn(new Response(200, [], json_encode(['data' => []])));

        [$data, $error] = $this->unsent->contactBooks->list();
        $this->assertNotNull($data);
        $this->assertNull($error);
    }

    public function testContactBooksCreate()
    {
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with('POST', 'https://api.unsent.dev/v1/contactBooks', Mockery::any())
            ->andReturn(new Response(200, [], json_encode(['id' => 'book_123'])));

        [$data, $error] = $this->unsent->contactBooks->create(['name' => 'Test Book']);
        $this->assertNotNull($data);
        $this->assertNull($error);
    }

    // Campaigns Tests
    public function testCampaignsList()
    {
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with('GET', 'https://api.unsent.dev/v1/campaigns', Mockery::any())
            ->andReturn(new Response(200, [], json_encode(['data' => []])));

        [$data, $error] = $this->unsent->campaigns->list();
        $this->assertNotNull($data);
        $this->assertNull($error);
    }

    public function testCampaignsCreate()
    {
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with('POST', 'https://api.unsent.dev/v1/campaigns', Mockery::any())
            ->andReturn(new Response(200, [], json_encode(['id' => 'campaign_123'])));

        [$data, $error] = $this->unsent->campaigns->create(['name' => 'Test Campaign']);
        $this->assertNotNull($data);
        $this->assertNull($error);
    }

    // Contacts Tests
    public function testContactsList()
    {
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with('GET', Mockery::pattern('/contactBooks\/book_123\/contacts/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode(['data' => []])));

        [$data, $error] = $this->unsent->contacts->list('book_123');
        $this->assertNotNull($data);
        $this->assertNull($error);
    }

    public function testContactsListWithOptions()
    {
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with('GET', Mockery::pattern('/contactBooks\/book_123\/contacts\?/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode(['data' => []])));

        [$data, $error] = $this->unsent->contacts->list('book_123', ['page' => 1, 'limit' => 10]);
        $this->assertNotNull($data);
        $this->assertNull($error);
    }

    // Domains Tests
    public function testDomainsVerifyWithStringId()
    {
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with('PUT', 'https://api.unsent.dev/v1/domains/domain_123/verify', Mockery::any())
            ->andReturn(new Response(200, [], json_encode(['verified' => true])));

        [$data, $error] = $this->unsent->domains->verify('domain_123');
        $this->assertNotNull($data);
        $this->assertNull($error);
    }

    // Emails Tests
    public function testEmailsList()
    {
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with('GET', Mockery::pattern('/emails/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode(['data' => []])));

        [$data, $error] = $this->unsent->emails->list();
        $this->assertNotNull($data);
        $this->assertNull($error);
    }

    public function testEmailsGetComplaints()
    {
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with('GET', Mockery::pattern('/emails\/complaints/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode(['data' => []])));

        [$data, $error] = $this->unsent->emails->getComplaints();
        $this->assertNotNull($data);
        $this->assertNull($error);
    }

    public function testEmailsGetBounces()
    {
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with('GET', Mockery::pattern('/emails\/bounces/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode(['data' => []])));

        [$data, $error] = $this->unsent->emails->getBounces();
        $this->assertNotNull($data);
        $this->assertNull($error);
    }

    public function testEmailsGetUnsubscribes()
    {
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with('GET', Mockery::pattern('/emails\/unsubscribes/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode(['data' => []])));

        [$data, $error] = $this->unsent->emails->getUnsubscribes();
        $this->assertNotNull($data);
        $this->assertNull($error);
    }

    // Settings Tests
    public function testSettingsGet()
    {
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with('GET', 'https://api.unsent.dev/v1/settings', Mockery::any())
            ->andReturn(new Response(200, [], json_encode(['data' => 'test'])));

        [$data, $error] = $this->unsent->settings->get();
        $this->assertNotNull($data);
        $this->assertNull($error);
    }

    // Suppressions Tests
    public function testSuppressionsList()
    {
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with('GET', Mockery::pattern('/suppressions/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode(['data' => []])));

        [$data, $error] = $this->unsent->suppressions->list();
        $this->assertNotNull($data);
        $this->assertNull($error);
    }

    public function testSuppressionsAdd()
    {
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with('POST', 'https://api.unsent.dev/v1/suppressions', Mockery::any())
            ->andReturn(new Response(200, [], json_encode(['id' => 'supp_123'])));

        [$data, $error] = $this->unsent->suppressions->add(['email' => 'test@example.com']);
        $this->assertNotNull($data);
        $this->assertNull($error);
    }

    // Templates Tests
    public function testTemplatesList()
    {
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with('GET', 'https://api.unsent.dev/v1/templates', Mockery::any())
            ->andReturn(new Response(200, [], json_encode(['data' => []])));

        [$data, $error] = $this->unsent->templates->list();
        $this->assertNotNull($data);
        $this->assertNull($error);
    }

    public function testTemplatesCreate()
    {
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with('POST', 'https://api.unsent.dev/v1/templates', Mockery::any())
            ->andReturn(new Response(200, [], json_encode(['id' => 'tmpl_123'])));

        [$data, $error] = $this->unsent->templates->create(['name' => 'Test Template']);
        $this->assertNotNull($data);
        $this->assertNull($error);
    }

    // Webhooks Tests
    public function testWebhooksList()
    {
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with('GET', 'https://api.unsent.dev/v1/webhooks', Mockery::any())
            ->andReturn(new Response(200, [], json_encode(['data' => []])));

        [$data, $error] = $this->unsent->webhooks->list();
        $this->assertNotNull($data);
        $this->assertNull($error);
    }
}
