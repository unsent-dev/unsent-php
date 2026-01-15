<?php

namespace Souravsspace\Unsent\Tests;

use Mockery;
use PHPUnit\Framework\TestCase;
use Souravsspace\Unsent\Unsent;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

/**
 * Test suite for System resource.
 */
class SystemTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function testHealth()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('GET', Mockery::pattern('/\/health$/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode([
                'status' => 'ok',
                'uptime' => 12345,
                'timestamp' => time()
            ])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        [$data, $error] = $unsent->system->health();

        $this->assertNull($error);
        $this->assertEquals('ok', $data['status']);
    }

    public function testVersion()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('GET', Mockery::pattern('/\/version$/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode([
                'version' => '1.0.0',
                'environment' => 'production'
            ])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        [$data, $error] = $unsent->system->version();

        $this->assertNull($error);
        $this->assertEquals('1.0.0', $data['version']);
    }
}
