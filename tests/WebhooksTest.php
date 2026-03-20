<?php

namespace UnsentDev\Unsent\Tests;

use Mockery;
use PHPUnit\Framework\TestCase;
use UnsentDev\Unsent\Unsent;
use UnsentDev\Unsent\Webhooks;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

/**
 * Test suite for Webhooks resource.
 */
class WebhooksTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function testList()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('GET', Mockery::pattern('/\/webhooks$/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode([
                ['id' => 'wh_1', 'url' => 'https://example.com/webhook', 'events' => ['email.sent']],
                ['id' => 'wh_2', 'url' => 'https://test.com/webhook', 'events' => ['email.delivered']]
            ])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        
        [$data, $error] = $unsent->webhooks->list();

        $this->assertNull($error);
        $this->assertIsArray($data);
        $this->assertCount(2, $data);
    }

    public function testCreate()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('POST', Mockery::pattern('/\/webhooks$/'), Mockery::on(function ($options) {
                return $options['json']['url'] === 'https://example.com/webhook' &&
                       in_array('email.sent', $options['json']['events']);
            }))
            ->andReturn(new Response(200, [], json_encode([
                'id' => 'wh_123',
                'url' => 'https://example.com/webhook',
                'events' => ['email.sent', 'email.delivered']
            ])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        
        [$data, $error] = $unsent->webhooks->create([
            'url' => 'https://example.com/webhook',
            'events' => ['email.sent', 'email.delivered']
        ]);

        $this->assertNull($error);
        $this->assertIsArray($data);
        $this->assertEquals('wh_123', $data['id']);
    }

    public function testGet()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('GET', Mockery::pattern('/\/webhooks\/wh_123$/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode([
                'id' => 'wh_123',
                'url' => 'https://example.com/webhook',
                'events' => ['email.sent']
            ])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        
        [$data, $error] = $unsent->webhooks->get('wh_123');

        $this->assertNull($error);
        $this->assertIsArray($data);
        $this->assertEquals('wh_123', $data['id']);
    }

    public function testUpdate()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('PATCH', Mockery::pattern('/\/webhooks\/wh_123$/'), Mockery::on(function ($options) {
                return $options['json']['url'] === 'https://newurl.com/webhook';
            }))
            ->andReturn(new Response(200, [], json_encode([
                'id' => 'wh_123',
                'url' => 'https://newurl.com/webhook',
                'events' => ['email.sent']
            ])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        
        [$data, $error] = $unsent->webhooks->update('wh_123', [
            'url' => 'https://newurl.com/webhook'
        ]);

        $this->assertNull($error);
        $this->assertIsArray($data);
        $this->assertEquals('https://newurl.com/webhook', $data['url']);
    }

    public function testDelete()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('DELETE', Mockery::pattern('/\/webhooks\/wh_123$/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode(['success' => true])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        
        [$data, $error] = $unsent->webhooks->delete('wh_123');

        $this->assertNull($error);
        $this->assertIsArray($data);
        $this->assertTrue($data['success']);
    }

    public function testTest()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('POST', Mockery::pattern('/\/webhooks\/wh_123\/test$/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode([
                'success' => true,
                'message' => 'Webhook test sent successfully'
            ])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        
        [$data, $error] = $unsent->webhooks->test('wh_123');

        $this->assertNull($error);
        $this->assertIsArray($data);
        $this->assertTrue($data['success']);
    }
}
