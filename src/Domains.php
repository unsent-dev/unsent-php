<?php

namespace Souravsspace\Unsent;

/**
 * Client for /domains endpoints.
 */
class Domains
{
    /**
     * @var Unsent Unsent client instance
     */
    private $unsent;

    /**
     * Initialize the Domains resource client.
     *
     * @param Unsent $unsent Unsent client instance
     */
    public function __construct(Unsent $unsent)
    {
        $this->unsent = $unsent;
    }

    /**
     * List all domains.
     *
     * @return array [data, error]
     */
    public function list(): array
    {
        return $this->unsent->get('/domains');
    }

    /**
     * Create a domain.
     *
     * @param array $payload Domain data
     * @return array [data, error]
     */
    public function create(array $payload): array
    {
        return $this->unsent->post('/domains', $payload);
    }

    /**
     * Verify a domain.
     *
     * @param int $domainId Domain ID
     * @return array [data, error]
     */
    public function verify(int $domainId): array
    {
        return $this->unsent->put("/domains/{$domainId}/verify", []);
    }

    /**
     * Get domain details.
     *
     * @param int $domainId Domain ID
     * @return array [data, error]
     */
    public function get(int $domainId): array
    {
        return $this->unsent->get("/domains/{$domainId}");
    }

    /**
     * Delete a domain.
     *
     * @param int $domainId Domain ID
     * @return array [data, error]
     */
    public function delete(int $domainId): array
    {
        return $this->unsent->delete("/domains/{$domainId}");
    }
}
