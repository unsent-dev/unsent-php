<?php

namespace UnsentDev\Unsent\Tests;

use Mockery;
use PHPUnit\Framework\TestCase;
use UnsentDev\Unsent\Unsent;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

/**
 * Test suite for Settings resource.
 */
class SettingsTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function testGet()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('GET', Mockery::pattern('/\/settings$/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode([
                'id' => 'team_123',
                'name' => 'My Team',
                'plan' => 'PRO',
                'limits' => [
                    'emails' => 100000,
                    'domains' => 10
                ]
            ])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        [$data, $error] = $unsent->settings->get();

        $this->assertNull($error);
        $this->assertEquals('My Team', $data['name']);
        $this->assertEquals('PRO', $data['plan']);
    }
}
