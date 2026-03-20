<?php

namespace UnsentDev\Unsent;

/**
 * Client for /webhooks endpoints.
 * 
 * @remarks This resource is currently in development and not fully implemented on the server side yet.
 * The methods below are placeholders/preparations for the future implementation.
 */
class Webhooks
{
    /**
     * @var Unsent Unsent client instance
     */
    private $unsent;

    /**
     * Initialize the Webhooks resource client.
     *
     * @param Unsent $unsent Unsent client instance
     */
    public function __construct(Unsent $unsent)
    {
        $this->unsent = $unsent;
    }

    /**
     * List all webhooks.
     *
     * @return array [data, error]
     */
    public function list(): array
    {
        return $this->unsent->get('/webhooks');
    }

    /**
     * Create a webhook.
     *
     * @param array $payload Webhook data (url, events)
     * @return array [data, error]
     */
    public function create(array $payload): array
    {
        return $this->unsent->post('/webhooks', $payload);
    }

    /**
     * Update a webhook.
     *
     * @param string $id Webhook ID
     * @param array $payload Update data (url, events)
     * @return array [data, error]
     */
    public function update(string $id, array $payload): array
    {
        return $this->unsent->patch("/webhooks/{$id}", $payload);
    }

    /**
     * Delete a webhook.
     *
     * @param string $id Webhook ID
     * @return array [data, error]
     */
    public function delete(string $id): array
    {
        return $this->unsent->delete("/webhooks/{$id}");
    }

    /**
     * Get webhook details.
     *
     * @param string $id Webhook ID
     * @return array [data, error]
     */
    public function get(string $id): array
    {
        return $this->unsent->get("/webhooks/{$id}");
    }

    /**
     * Test a webhook.
     *
     * @param string $id Webhook ID
     * @return array [data, error]
     */
    public function test(string $id): array
    {
        return $this->unsent->post("/webhooks/{$id}/test", []);
    }
}
