<?php

namespace Souravsspace\Unsent\Tests;

use Mockery;
use PHPUnit\Framework\TestCase;
use Souravsspace\Unsent\Unsent;
use Souravsspace\Unsent\Emails;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

/**
 * Test suite for Emails resource.
 *
 * Tests all methods in the Emails class including send, batch, get, update, list, etc.
 */
class EmailsTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function testSend()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('POST', Mockery::any(), Mockery::on(function ($options) {
                return isset($options['json']['to']) && isset($options['json']['subject']);
            }))
            ->andReturn(new Response(200, [], json_encode(['id' => 'email_123'])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        
        [$data, $error] = $unsent->emails->send([
            'to' => 'test@example.com',
            'subject' => 'Test Email',
            'html' => '<p>Test content</p>'
        ]);

        $this->assertNull($error);
        $this->assertIsArray($data);
        $this->assertEquals('email_123', $data['id']);
    }

    public function testSendWithIdempotencyKey()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('POST', Mockery::any(), Mockery::on(function ($options) {
                return isset($options['headers']['Idempotency-Key']) && $options['headers']['Idempotency-Key'] === 'test-key-123';
            }))
            ->andReturn(new Response(200, [], json_encode(['id' => 'email_123'])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        
        [$data, $error] = $unsent->emails->send([
            'to' => 'test@example.com',
            'subject' => 'Test',
            'html' => '<p>Test</p>'
        ], ['idempotencyKey' => 'test-key-123']);

        $this->assertNull($error);
        $this->assertIsArray($data);
    }

    public function testBatch()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('POST', Mockery::pattern('/\/emails\/batch$/'), Mockery::on(function ($options) {
                return is_array($options['json']) && count($options['json']) === 2;
            }))
            ->andReturn(new Response(200, [], json_encode(['success' => true, 'count' => 2])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        
        [$data, $error] = $unsent->emails->batch([
            ['to' => 'test1@example.com', 'subject' => 'Test 1', 'html' => '<p>Test 1</p>'],
            ['to' => 'test2@example.com', 'subject' => 'Test 2', 'html' => '<p>Test 2</p>']
        ]);

        $this->assertNull($error);
        $this->assertIsArray($data);
        $this->assertEquals(2, $data['count']);
    }

    public function testGet()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('GET', Mockery::pattern('/\/emails\/email_123$/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode(['id' => 'email_123', 'subject' => 'Test Email'])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        
        [$data, $error] = $unsent->emails->get('email_123');

        $this->assertNull($error);
        $this->assertIsArray($data);
        $this->assertEquals('email_123', $data['id']);
        $this->assertEquals('Test Email', $data['subject']);
    }

    public function testUpdate()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('PATCH', Mockery::pattern('/\/emails\/email_123$/'), Mockery::on(function ($options) {
                return isset($options['json']['subject']) && $options['json']['subject'] === 'Updated Subject';
            }))
            ->andReturn(new Response(200, [], json_encode(['id' => 'email_123', 'subject' => 'Updated Subject'])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        
        [$data, $error] = $unsent->emails->update('email_123', ['subject' => 'Updated Subject']);

        $this->assertNull($error);
        $this->assertIsArray($data);
        $this->assertEquals('Updated Subject', $data['subject']);
    }

    public function testList()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('GET', Mockery::pattern('/\/emails\?page=1&limit=10/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode(['data' => [
                ['id' => 'email_1', 'subject' => 'Test 1'],
                ['id' => 'email_2', 'subject' => 'Test 2']
            ]])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        
        [$data, $error] = $unsent->emails->list(['page' => 1, 'limit' => 10]);

        $this->assertNull($error);
        $this->assertIsArray($data);
        $this->assertCount(2, $data['data']);
    }

    public function testGetEvents()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('GET', Mockery::pattern('/\/emails\/email_123\/events/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode(['events' => [
                ['type' => 'SENT', 'timestamp' => '2024-01-15T10:00:00Z'],
                ['type' => 'DELIVERED', 'timestamp' => '2024-01-15T10:05:00Z']
            ]])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        
        [$data, $error] = $unsent->emails->getEvents('email_123');

        $this->assertNull($error);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('events', $data);
        $this->assertCount(2, $data['events']);
    }

    public function testGetComplaints()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('GET', Mockery::pattern('/\/emails\/complaints/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode(['complaints' => []])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        
        [$data, $error] = $unsent->emails->getComplaints();

        $this->assertNull($error);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('complaints', $data);
    }

    public function testGetBounces()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('GET', Mockery::pattern('/\/emails\/bounces/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode(['bounces' => []])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        
        [$data, $error] = $unsent->emails->getBounces();

        $this->assertNull($error);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('bounces', $data);
    }

    public function testGetUnsubscribes()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('GET', Mockery::pattern('/\/emails\/unsubscribes/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode(['unsubscribes' => []])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        
        [$data, $error] = $unsent->emails->getUnsubscribes();

        $this->assertNull($error);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('unsubscribes', $data);
    }

    public function testCancel()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('POST', Mockery::pattern('/\/emails\/email_123\/cancel$/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode(['success' => true, 'cancelled' => true])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        
        [$data, $error] = $unsent->emails->cancel('email_123');

        $this->assertNull($error);
        $this->assertIsArray($data);
        $this->assertTrue($data['cancelled']);
    }
}
