<?php

namespace UnsentDev\Unsent\Tests;

use Mockery;
use PHPUnit\Framework\TestCase;
use UnsentDev\Unsent\Unsent;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

/**
 * Test suite for Templates resource.
 */
class TemplatesTest extends TestCase
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
            ->with('GET', Mockery::pattern('/\/templates/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode([
                'data' => [
                    ['id' => 'tpl_1', 'name' => 'Welcome Email'],
                    ['id' => 'tpl_2', 'name' => 'Password Reset']
                ]
            ])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        
        [$data, $error] = $unsent->templates->list();

        $this->assertNull($error);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('data', $data);
    }

    public function testCreate()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('POST', Mockery::pattern('/\/templates$/'), Mockery::on(function ($options) {
                return $options['json']['name'] === 'New Template' && isset($options['json']['html']);
            }))
            ->andReturn(new Response(200, [], json_encode([
                'id' => 'tpl_123',
                'name' => 'New Template',
                'html' => '<p>{{content}}</p>'
            ])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        
        [$data, $error] = $unsent->templates->create([
            'name' => 'New Template',
            'subject' => 'Test Subject',
            'html' => '<p>{{content}}</p>'
        ]);

        $this->assertNull($error);
        $this->assertEquals('tpl_123', $data['id']);
    }

    public function testGet()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('GET', Mockery::pattern('/\/templates\/tpl_123$/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode([
                'id' => 'tpl_123',
                'name' => 'Welcome Email',
                'subject' => 'Welcome!',
                'html' => '<p>Hello {{name}}</p>'
            ])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        
        [$data, $error] = $unsent->templates->get('tpl_123');

        $this->assertNull($error);
        $this->assertEquals('tpl_123', $data['id']);
        $this->assertEquals('Welcome Email', $data['name']);
    }

    public function testUpdate()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('PATCH', Mockery::pattern('/\/templates\/tpl_123$/'), Mockery::on(function ($options) {
                return $options['json']['name'] === 'Updated Template';
            }))
            ->andReturn(new Response(200, [], json_encode([
                'id' => 'tpl_123',
                'name' => 'Updated Template'
            ])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        
        [$data, $error] = $unsent->templates->update('tpl_123', ['name' => 'Updated Template']);

        $this->assertNull($error);
        $this->assertEquals('Updated Template', $data['name']);
    }

    public function testDelete()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('DELETE', Mockery::pattern('/\/templates\/tpl_123$/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode(['success' => true])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        
        [$data, $error] = $unsent->templates->delete('tpl_123');

        $this->assertNull($error);
        $this->assertTrue($data['success']);
    }
}
