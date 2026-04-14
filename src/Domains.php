<?php
// @manual

namespace UnsentDev\Unsent;

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
     * @param string $domainId Domain ID
     * @return array [data, error]
     */
    public function verify(string $domainId): array
    {
        return $this->unsent->put("/domains/{$domainId}/verify", []);
    }

    /**
     * Get domain details.
     *
     * @param string $domainId Domain ID
     * @return array [data, error]
     */
    public function get(string $domainId): array
    {
        return $this->unsent->get("/domains/{$domainId}");
    }

    /**
     * Delete a domain.
     *
     * @param string $domainId Domain ID
     * @return array [data, error]
     */
    public function delete(string $domainId): array
    {
        return $this->unsent->delete("/domains/{$domainId}");
    }

    /**
     * Get domain analytics.
     *
     * @param string $domainId Domain ID
     * @param array $options Optional parameters
     * @return array [data, error]
     */
    public function getAnalytics(string $domainId, array $options = []): array
    {
        $query = http_build_query($options);
        $path = "/domains/{$domainId}/analytics" . ($query ? '?' . $query : '');
        return $this->unsent->get($path);
    }

    /**
     * Get domain stats.
     *
     * @param string $domainId Domain ID
     * @param array $options Optional parameters
     * @return array [data, error]
     */
    public function getStats(string $domainId, array $options = []): array
    {
        $query = http_build_query($options);
        $path = "/domains/{$domainId}/stats" . ($query ? '?' . $query : '');
        return $this->unsent->get($path);
    }

    /**
     * List all routes for a domain.
     *
     * @param string $domainId Domain ID
     * @return array [data, error]
     */
    public function listRoutes(string $domainId): array
    {
        return $this->unsent->get("/domains/{$domainId}/routes");
    }

    /**
     * Add a route to a domain.
     *
     * @param string $domainId Domain ID
     * @param array $payload Route data
     * @return array [data, error]
     */
    public function addRoute(string $domainId, array $payload): array
    {
        return $this->unsent->post("/domains/{$domainId}/routes", $payload);
    }

    /**
     * Update a domain route.
     *
     * @param string $domainId Domain ID
     * @param string $routeId Route ID
     * @param array $payload Route data
     * @return array [data, error]
     */
    public function updateRoute(string $domainId, string $routeId, array $payload): array
    {
        return $this->unsent->patch("/domains/{$domainId}/routes/{$routeId}", $payload);
    }

    /**
     * Delete a domain route.
     *
     * @param string $domainId Domain ID
     * @param string $routeId Route ID
     * @return array [data, error]
     */
    public function deleteRoute(string $domainId, string $routeId): array
    {
        return $this->unsent->delete("/domains/{$domainId}/routes/{$routeId}");
    }
}
