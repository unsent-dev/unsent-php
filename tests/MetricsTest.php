<?php

namespace Souravsspace\Unsent\Tests;

use Mockery;
use PHPUnit\Framework\TestCase;
use Souravsspace\Unsent\Unsent;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

/**
 * Test suite for Metrics resource.
 */
class MetricsTest extends TestCase
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
            ->with('GET', Mockery::pattern('/\/metrics/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode([
                'metrics' => [
                    'deliveryRate' => 0.95,
                    'openRate' => 0.32,
                    'clickRate' => 0.15,
                    'bounceRate' => 0.02
                ],
                'period' => 'month'
            ])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        [$data, $error] = $unsent->metrics->get(['period' => 'month']);

        $this->assertNull($error);
        $this->assertArrayHasKey('metrics', $data);
        $this->assertEquals(0.95, $data['metrics']['deliveryRate']);
    }
}
