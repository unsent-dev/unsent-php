<?php

namespace Souravsspace\Unsent;

/**
 * Client for /teams endpoints.
 */
class Teams
{
    /**
     * @var Unsent Unsent client instance
     */
    private $unsent;

    /**
     * Initialize the Teams resource client.
     *
     * @param Unsent $unsent Unsent client instance
     */
    public function __construct(Unsent $unsent)
    {
        $this->unsent = $unsent;
    }

    /**
     * List all teams.
     *
     * @return array [data, error]
     */
    public function list(): array
    {
        return $this->unsent->get('/teams');
    }

    /**
     * Get team details.
     *
     * @param string $teamId Team ID
     * @return array [data, error]
     */
    public function get(string $teamId): array
    {
        return $this->unsent->get("/teams/{$teamId}");
    }
}
