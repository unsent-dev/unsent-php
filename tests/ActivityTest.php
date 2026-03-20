<?php

namespace UnsentDev\Unsent\Tests;

use Mockery;
use PHPUnit\Framework\TestCase;
use UnsentDev\Unsent\Unsent;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

/**
 * Test suite for Activity resource.
 */
class ActivityTest extends TestCase
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
            ->with('GET', Mockery::pattern('/\/activity/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode([
                'activities' => [
                    ['id' => 'act_1', 'type' => 'EMAIL_SENT', 'timestamp' => '2024-01-15T10:00:00Z'],
                    ['id' => 'act_2', 'type' => 'DOMAIN_VERIFIED', 'timestamp' => '2024-01-15T09:00:00Z']
                ],
                'page' => 1
            ])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        [$data, $error] = $unsent->activity->get(['page' => 1]);

        $this->assertNull($error);
        $this->assertArrayHasKey('activities', $data);
        $this->assertCount(2, $data['activities']);
    }
}
