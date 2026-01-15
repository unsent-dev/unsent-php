<?php

namespace Souravsspace\Unsent\Tests;

use Mockery;
use PHPUnit\Framework\TestCase;
use Souravsspace\Unsent\Unsent;
use Souravsspace\Unsent\Domains;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

/**
 * Test suite for Domains resource.
 *
 * Tests all methods in the Domains class including list, create, verify, get, delete, getAnalytics, and getStats.
 */
class DomainsTest extends TestCase
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
            ->with('GET', Mockery::pattern('/\/domains$/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode([
                ['id' => 'domain_1', 'name' => 'example.com', 'status' => 'VERIFIED'],
                ['id' => 'domain_2', 'name' => 'test.com', 'status' => 'PENDING']
            ])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        
        [$data, $error] = $unsent->domains->list();

        $this->assertNull($error);
        $this->assertIsArray($data);
        $this->assertCount(2, $data);
        $this->assertEquals('example.com', $data[0]['name']);
    }

    public function testCreate()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('POST', Mockery::pattern('/\/domains$/'), Mockery::on(function ($options) {
                return $options['json']['name'] === 'newdomain.com' && $options['json']['region'] === 'us-east-1';
            }))
            ->andReturn(new Response(200, [], json_encode([
                'id' => 'domain_123',
                'name' => 'newdomain.com',
                'status' => 'PENDING',
                'region' => 'us-east-1'
            ])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        
        [$data, $error] = $unsent->domains->create([
            'name' => 'newdomain.com',
            'region' => 'us-east-1'
        ]);

        $this->assertNull($error);
        $this->assertIsArray($data);
        $this->assertEquals('newdomain.com', $data['name']);
        $this->assertEquals('PENDING', $data['status']);
    }

    public function testVerify()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('PUT', Mockery::pattern('/\/domains\/domain_123\/verify$/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode([
                'id' => 'domain_123',
                'name' => 'example.com',
                'status' => 'VERIFIED'
            ])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        
        [$data, $error] = $unsent->domains->verify('domain_123');

        $this->assertNull($error);
        $this->assertIsArray($data);
        $this->assertEquals('VERIFIED', $data['status']);
    }

    public function testGet()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('GET', Mockery::pattern('/\/domains\/domain_123$/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode([
                'id' => 'domain_123',
                'name' => 'example.com',
                'status' => 'VERIFIED',
                'dnsRecords' => []
            ])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        
        [$data, $error] = $unsent->domains->get('domain_123');

        $this->assertNull($error);
        $this->assertIsArray($data);
        $this->assertEquals('domain_123', $data['id']);
        $this->assertEquals('example.com', $data['name']);
    }

    public function testDelete()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('DELETE', Mockery::pattern('/\/domains\/domain_123$/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode(['success' => true])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        
        [$data, $error] = $unsent->domains->delete('domain_123');

        $this->assertNull($error);
        $this->assertIsArray($data);
        $this->assertTrue($data['success']);
    }

    public function testGetAnalytics()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('GET', Mockery::pattern('/\/domains\/domain_123\/analytics/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode([
                ['date' => '2024-01-15', 'sent' => 100, 'delivered' => 95, 'opened' => 60, 'clicked' => 30, 'bounced' => 2, 'complained' => 1]
            ])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        
        [$data, $error] = $unsent->domains->getAnalytics('domain_123', ['period' => 'day']);

        $this->assertNull($error);
        $this->assertIsArray($data);
        $this->assertCount(1, $data);
        $this->assertEquals(100, $data[0]['sent']);
    }

    public function testGetStats()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('GET', Mockery::pattern('/\/domains\/domain_123\/stats/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode([
                'totalSent' => 1000,
                'totalDelivered' => 950,
                'totalOpened' => 600,
                'totalClicked' => 300
            ])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        
        [$data, $error] = $unsent->domains->getStats('domain_123');

        $this->assertNull($error);
        $this->assertIsArray($data);
        $this->assertEquals(1000, $data['totalSent']);
        $this->assertEquals(950, $data['totalDelivered']);
    }
}
