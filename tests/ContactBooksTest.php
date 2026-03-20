<?php

namespace UnsentDev\Unsent\Tests;

use Mockery;
use PHPUnit\Framework\TestCase;
use UnsentDev\Unsent\Unsent;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

/**
 * Test suite for ContactBooks resource.
 */
class ContactBooksTest extends TestCase
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
            ->with('GET', Mockery::pattern('/\/contactBooks$/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode([
                ['id' => 'book_1', 'name' => 'Newsletter Subscribers'],
                ['id' => 'book_2', 'name' => 'VIP Customers']
            ])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        
        [$data, $error] = $unsent->contactBooks->list();

        $this->assertNull($error);
        $this->assertIsArray($data);
        $this->assertCount(2, $data);
    }

    public function testCreate()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('POST', Mockery::pattern('/\/contactBooks$/'), Mockery::on(function ($options) {
                return $options['json']['name'] === 'New Contact Book';
            }))
            ->andReturn(new Response(200, [], json_encode([
                'id' => 'book_123',
                'name' => 'New Contact Book'
            ])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        
        [$data, $error] = $unsent->contactBooks->create(['name' => 'New Contact Book']);

        $this->assertNull($error);
        $this->assertEquals('book_123', $data['id']);
    }

    public function testGet()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('GET', Mockery::pattern('/\/contactBooks\/book_123$/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode([
                'id' => 'book_123',
                'name' => 'Test Contact Book',
                'contactCount' => 42
            ])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        
        [$data, $error] = $unsent->contactBooks->get('book_123');

        $this->assertNull($error);
        $this->assertEquals('book_123', $data['id']);
        $this->assertEquals(42, $data['contactCount']);
    }

    public function testUpdate()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('PATCH', Mockery::pattern('/\/contactBooks\/book_123$/'), Mockery::on(function ($options) {
                return $options['json']['name'] === 'Updated Name';
            }))
            ->andReturn(new Response(200, [], json_encode([
                'id' => 'book_123',
                'name' => 'Updated Name'
            ])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        
        [$data, $error] = $unsent->contactBooks->update('book_123', ['name' => 'Updated Name']);

        $this->assertNull($error);
        $this->assertEquals('Updated Name', $data['name']);
    }

    public function testDelete()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('DELETE', Mockery::pattern('/\/contactBooks\/book_123$/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode(['success' => true])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        
        [$data, $error] = $unsent->contactBooks->delete('book_123');

        $this->assertNull($error);
        $this->assertTrue($data['success']);
    }
}
