<?php

namespace Souravsspace\Unsent\Tests;

use Mockery;
use PHPUnit\Framework\TestCase;
use Souravsspace\Unsent\Unsent;
use Souravsspace\Unsent\Campaigns;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

/**
 * Test suite for Campaigns resource.
 */
class CampaignsTest extends TestCase
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
            ->with('GET', Mockery::pattern('/\/campaigns$/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode([
                ['id' => 'camp_1', 'name' => 'Newsletter Q1', 'status' => 'DRAFT'],
                ['id' => 'camp_2', 'name' => 'Promo Campaign', 'status' => 'SCHEDULED']
            ])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        
        [$data, $error] = $unsent->campaigns->list();

        $this->assertNull($error);
        $this->assertIsArray($data);
        $this->assertCount(2, $data);
    }

    public function testCreate()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('POST', Mockery::pattern('/\/campaigns$/'), Mockery::on(function ($options) {
                return $options['json']['name'] === 'New Campaign';
            }))
            ->andReturn(new Response(200, [], json_encode([
                'id' => 'camp_123',
                'name' => 'New Campaign',
                'status' => 'DRAFT'
            ])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        
        [$data, $error] = $unsent->campaigns->create([
            'name' => 'New Campaign',
            'subject' => 'Test Subject',
            'html' => '<p>Campaign content</p>',
            'contactBookId' => 'book_123'
        ]);

        $this->assertNull($error);
        $this->assertIsArray($data);
        $this->assertEquals('camp_123', $data['id']);
    }

    public function testGet()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('GET', Mockery::pattern('/\/campaigns\/camp_123$/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode([
                'id' => 'camp_123',
                'name' => 'Test Campaign',
                'status' => 'SCHEDULED'
            ])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        
        [$data, $error] = $unsent->campaigns->get('camp_123');

        $this->assertNull($error);
        $this->assertIsArray($data);
        $this->assertEquals('camp_123', $data['id']);
    }

    public function testSchedule()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('POST', Mockery::pattern('/\/campaigns\/camp_123\/schedule$/'), Mockery::on(function ($options) {
                return isset($options['json']['scheduledAt']);
            }))
            ->andReturn(new Response(200, [], json_encode([
                'id' => 'camp_123',
                'status' => 'SCHEDULED',
                'scheduledAt' => '2024-01-20T10:00:00Z'
            ])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        
        [$data, $error] = $unsent->campaigns->schedule('camp_123', [
            'scheduledAt' => '2024-01-20T10:00:00Z'
        ]);

        $this->assertNull($error);
        $this->assertIsArray($data);
        $this->assertEquals('SCHEDULED', $data['status']);
    }

    public function testPause()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('POST', Mockery::pattern('/\/campaigns\/camp_123\/pause$/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode([
                'id' => 'camp_123',
                'status' => 'PAUSED'
            ])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        
        [$data, $error] = $unsent->campaigns->pause('camp_123');

        $this->assertNull($error);
        $this->assertIsArray($data);
        $this->assertEquals('PAUSED', $data['status']);
    }

    public function testResume()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('POST', Mockery::pattern('/\/campaigns\/camp_123\/resume$/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode([
                'id' => 'camp_123',
                'status' => 'ACTIVE'
            ])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        
        [$data, $error] = $unsent->campaigns->resume('camp_123');

        $this->assertNull($error);
        $this->assertIsArray($data);
        $this->assertEquals('ACTIVE', $data['status']);
    }
}
