<?php

namespace Souravsspace\Unsent\Tests;

use Mockery;
use PHPUnit\Framework\TestCase;
use Souravsspace\Unsent\Unsent;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

/**
 * Test suite for Suppressions resource.
 */
class SuppressionsTest extends TestCase
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
            ->with('GET', Mockery::pattern('/\/suppressions/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode([
                ['email' => 'blocked@example.com', 'reason' => 'HARD_BOUNCE'],
                ['email' => 'spam@example.com', 'reason' => 'COMPLAINT']
            ])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        [$data, $error] = $unsent->suppressions->list();

        $this->assertNull($error);
        $this->assertIsArray($data);
        $this->assertCount(2, $data);
    }

    public function testAdd()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('POST', Mockery::pattern('/\/suppressions$/'), Mockery::on(function ($options) {
                return $options['json']['email'] === 'block@example.com' && $options['json']['reason'] === 'MANUAL';
            }))
            ->andReturn(new Response(200, [], json_encode([
                'id' => 'supp_123',
                'email' => 'block@example.com',
                'reason' => 'MANUAL',
                'success' => true
            ])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        [$data, $error] = $unsent->suppressions->add([
            'email' => 'block@example.com',
            'reason' => 'MANUAL'
        ]);

        $this->assertNull($error);
        $this->assertTrue($data['success']);
    }

    public function testDelete()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('DELETE', Mockery::pattern('/\/suppressions\/email\//'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode(['success' => true])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        [$data, $error] = $unsent->suppressions->delete('test@example.com');

        $this->assertNull($error);
        $this->assertTrue($data['success']);
    }
}
