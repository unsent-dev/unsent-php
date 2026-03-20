<?php

namespace UnsentDev\Unsent\Tests;

use Mockery;
use PHPUnit\Framework\TestCase;
use UnsentDev\Unsent\Unsent;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

/**
 * Test suite for Teams resource.
 */
class TeamsTest extends TestCase
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
            ->with('GET', Mockery::pattern('/\/teams$/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode([
                ['id' => 'team_1', 'name' => 'Team 1'],
                ['id' => 'team_2', 'name' => 'Team 2']
            ])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        [$data, $error] = $unsent->teams->list();

        $this->assertNull($error);
        $this->assertIsArray($data);
        $this->assertCount(2, $data);
    }

    public function testGet()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('GET', Mockery::pattern('/\/teams\/team_123$/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode([
                'id' => 'team_123',
                'name' => 'My Team',
                'memberCount' => 5
            ])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        [$data, $error] = $unsent->teams->get('team_123');

        $this->assertNull($error);
        $this->assertEquals('team_123', $data['id']);
        $this->assertEquals('My Team', $data['name']);
    }
}
