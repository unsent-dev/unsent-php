<?php

namespace Souravsspace\Unsent\Tests;

use Mockery;
use PHPUnit\Framework\TestCase;
use Souravsspace\Unsent\Unsent;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

/**
 * Test suite for Contacts resource.
 */
class ContactsTest extends TestCase
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
            ->with('GET', Mockery::pattern('/\/contactBooks\/book_123\/contacts/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode([
                ['id' => 'contact_1', 'email' => 'test1@example.com', 'name' => 'Test User 1'],
                ['id' => 'contact_2', 'email' => 'test2@example.com', 'name' => 'Test User 2']
            ])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        
        [$data, $error] = $unsent->contacts->list('book_123');

        $this->assertNull($error);
        $this->assertIsArray($data);
        $this->assertCount(2, $data);
    }

    public function testCreate()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('POST', Mockery::pattern('/\/contactBooks\/book_123\/contacts$/'), Mockery::on(function ($options) {
                return $options['json']['email'] === 'new@example.com';
            }))
            ->andReturn(new Response(200, [], json_encode([
                'id' => 'contact_123',
                'email' => 'new@example.com',
                'name' => 'New Contact'
            ])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        
        [$data, $error] = $unsent->contacts->create('book_123', [
            'email' => 'new@example.com',
            'name' => 'New Contact'
        ]);

        $this->assertNull($error);
        $this->assertIsArray($data);
        $this->assertEquals('contact_123', $data['id']);
    }

    public function testGet()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('GET', Mockery::pattern('/\/contactBooks\/book_123\/contacts\/contact_123$/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode([
                'id' => 'contact_123',
                'email' => 'test@example.com',
                'name' => 'Test User'
            ])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        
        [$data, $error] = $unsent->contacts->get('book_123', 'contact_123');

        $this->assertNull($error);
        $this->assertIsArray($data);
        $this->assertEquals('contact_123', $data['id']);
    }

    public function testUpdate()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('PATCH', Mockery::pattern('/\/contactBooks\/book_123\/contacts\/contact_123$/'), Mockery::on(function ($options) {
                return $options['json']['name'] === 'Updated Name';
            }))
            ->andReturn(new Response(200, [], json_encode([
                'id' => 'contact_123',
                'email' => 'test@example.com',
                'name' => 'Updated Name'
            ])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        
        [$data, $error] = $unsent->contacts->update('book_123', 'contact_123', ['name' => 'Updated Name']);

        $this->assertNull($error);
        $this->assertEquals('Updated Name', $data['name']);
    }

    public function testUpsert()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('PUT', Mockery::pattern('/\/contactBooks\/book_123\/contacts\/contact_123$/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode([
                'id' => 'contact_123',
                'email' => 'test@example.com',
                'name' => 'Upserted Contact'
            ])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        
        [$data, $error] = $unsent->contacts->upsert('book_123', 'contact_123', [
            'email' => 'test@example.com',
            'name' => 'Upserted Contact'
        ]);

        $this->assertNull($error);
        $this->assertEquals('Upserted Contact', $data['name']);
    }

    public function testDelete()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('DELETE', Mockery::pattern('/\/contactBooks\/book_123\/contacts\/contact_123$/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode(['success' => true])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        
        [$data, $error] = $unsent->contacts->delete('book_123', 'contact_123');

        $this->assertNull($error);
        $this->assertTrue($data['success']);
    }
}
