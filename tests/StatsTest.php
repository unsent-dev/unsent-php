<?php

namespace UnsentDev\Unsent\Tests;

use Mockery;
use PHPUnit\Framework\TestCase;
use UnsentDev\Unsent\Unsent;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

/**
 * Test suite for Stats resource.
 */
class StatsTest extends TestCase
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
            ->with('GET', Mockery::pattern('/\/stats/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode([
                'stats' => [
                    'totalSent' => 50000,
                    'totalDelivered' => 47500,
                    'totalOpened' => 20000,
                    'totalClicked' => 8000
                ],
                'dateRange' => [
                    'start' => '2024-01-01',
                    'end' => '2024-01-15'
                ]
            ])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        [$data, $error] = $unsent->stats->get();

        $this->assertNull($error);
        $this->assertArrayHasKey('stats', $data);
        $this->assertEquals(50000, $data['stats']['totalSent']);
    }
}
