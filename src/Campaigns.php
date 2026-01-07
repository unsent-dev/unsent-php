<?php

namespace Souravsspace\Unsent;

/**
 * Client for /campaigns endpoints.
 */
class Campaigns
{
    /**
     * @var Unsent Unsent client instance
     */
    private $unsent;

    /**
     * Initialize the Campaigns resource client.
     *
     * @param Unsent $unsent Unsent client instance
     */
    public function __construct(Unsent $unsent)
    {
        $this->unsent = $unsent;
    }

    /**
     * List all campaigns.
     *
     * @return array [data, error]
     */
    public function list(): array
    {
        return $this->unsent->get('/campaigns');
    }

    /**
     * Create a campaign.
     *
     * @param array $payload Campaign data
     * @return array [data, error]
     */
    public function create(array $payload): array
    {
        return $this->unsent->post('/campaigns', $payload);
    }

    /**
     * Get campaign details.
     *
     * @param string $campaignId Campaign ID
     * @return array [data, error]
     */
    public function get(string $campaignId): array
    {
        return $this->unsent->get("/campaigns/{$campaignId}");
    }

    /**
     * Schedule a campaign.
     *
     * @param string $campaignId Campaign ID
     * @param array $payload Schedule data
     * @return array [data, error]
     */
    public function schedule(string $campaignId, array $payload): array
    {
        return $this->unsent->post("/campaigns/{$campaignId}/schedule", $payload);
    }

    /**
     * Pause a campaign.
     *
     * @param string $campaignId Campaign ID
     * @return array [data, error]
     */
    public function pause(string $campaignId): array
    {
        return $this->unsent->post("/campaigns/{$campaignId}/pause", []);
    }

    /**
     * Resume a campaign.
     *
     * @param string $campaignId Campaign ID
     * @return array [data, error]
     */
    public function resume(string $campaignId): array
    {
        return $this->unsent->post("/campaigns/{$campaignId}/resume", []);
    }
}
