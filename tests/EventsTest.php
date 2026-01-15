<?php

namespace Souravsspace\Unsent\Tests;

use Mockery;
use PHPUnit\Framework\TestCase;
use Souravsspace\Unsent\Unsent;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

/**
 * Test suite for Events resource.
 */
class EventsTest extends TestCase
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
            ->with('GET', Mockery::pattern('/\/events/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode([
                'events' => [
                    ['id' => 'evt_1', 'type' => 'SENT', 'emailId' => 'email_123'],
                    ['id' => 'evt_2', 'type' => 'DELIVERED', 'emailId' => 'email_123']
                ],
                'page' => 1,
                'limit' => 50
            ])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        [$data, $error] = $unsent->events->list(['page' => 1, 'limit' => 50]);

        $this->assertNull($error);
        $this->assertArrayHasKey('events', $data);
        $this->assertCount(2, $data['events']);
    }
}
