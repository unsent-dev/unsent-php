<?php

namespace Souravsspace\Unsent\Tests;

use Mockery;
use PHPUnit\Framework\TestCase;
use Souravsspace\Unsent\Unsent;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

/**
 * Test suite for Analytics resource.
 */
class AnalyticsTest extends TestCase
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
            ->with('GET', Mockery::pattern('/\/analytics$/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode([
                'day' => ['sent' => 100, 'delivered' => 95, 'opened' => 60],
                'month' => ['sent' => 3000, 'delivered' => 2850, 'opened' => 1800]
            ])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        [$data, $error] = $unsent->analytics->get();

        $this->assertNull($error);
        $this->assertArrayHasKey('day', $data);
        $this->assertArrayHasKey('month', $data);
    }

    public function testGetTimeSeries()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('GET', Mockery::pattern('/\/analytics\/time-series/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode([
                'series' => [
                    ['date' => '2024-01-15', 'sent' => 100, 'delivered' => 95]
                ]
            ])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        [$data, $error] = $unsent->analytics->getTimeSeries(['days' => '7']);

        $this->assertNull($error);
        $this->assertArrayHasKey('series', $data);
    }

    public function testGetReputation()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('GET', Mockery::pattern('/\/analytics\/reputation/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode([
                'score' => 95,
                'rating' => 'EXCELLENT'
            ])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        [$data, $error] = $unsent->analytics->getReputation();

        $this->assertNull($error);
        $this->assertEquals(95, $data['score']);
    }
}
