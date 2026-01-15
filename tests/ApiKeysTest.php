<?php

namespace Souravsspace\Unsent\Tests;

use Mockery;
use PHPUnit\Framework\TestCase;
use Souravsspace\Unsent\Unsent;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

/**
 * Test suite for ApiKeys resource.
 */
class ApiKeysTest extends TestCase
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
            ->with('GET', Mockery::pattern('/\/api-keys$/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode([
                ['id' => 'key_1', 'name' => 'Production Key', 'permission' => 'FULL'],
                ['id' => 'key_2', 'name' => 'Test Key', 'permission' => 'SENDING']
            ])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        [$data, $error] = $unsent->apiKeys->list();

        $this->assertNull($error);
        $this->assertIsArray($data);
        $this->assertCount(2, $data);
    }

    public function testCreate()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('POST', Mockery::pattern('/\/api-keys$/'), Mockery::on(function ($options) {
                return $options['json']['name'] === 'Test Key' && $options['json']['permission'] === 'SENDING';
            }))
            ->andReturn(new Response(200, [], json_encode([
                'id' => 'key_123',
                'name' => 'Test Key',
                'token' => 'un_test_token_abc123',
                'permission' => 'SENDING'
            ])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        [$data, $error] = $unsent->apiKeys->create([
            'name' => 'Test Key',
            'permission' => 'SENDING'
        ]);

        $this->assertNull($error);
        $this->assertEquals('un_test_token_abc123', $data['token']);
    }

    public function testDelete()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->with('DELETE', Mockery::pattern('/\/api-keys\/key_123$/'), Mockery::any())
            ->andReturn(new Response(200, [], json_encode(['success' => true])));

        $unsent = new Unsent('test_key', null, true, $mockClient);
        [$data, $error] = $unsent->apiKeys->delete('key_123');

        $this->assertNull($error);
        $this->assertTrue($data['success']);
    }
}
