<?php

namespace Souravsspace\Unsent;

/**
 * Client for /templates endpoints.
 */
class Templates
{
    /**
     * @var Unsent Unsent client instance
     */
    private $unsent;

    /**
     * Initialize the Templates resource client.
     *
     * @param Unsent $unsent Unsent client instance
     */
    public function __construct(Unsent $unsent)
    {
        $this->unsent = $unsent;
    }

    /**
     * List all templates.
     *
     * @return array [data, error]
     */
    public function list(): array
    {
        return $this->unsent->get('/templates');
    }

    /**
     * Create a template.
     *
     * @param array $payload Template data
     * @return array [data, error]
     */
    public function create(array $payload): array
    {
        return $this->unsent->post('/templates', $payload);
    }

    /**
     * Get template details.
     *
     * @param string $id Template ID
     * @return array [data, error]
     */
    public function get(string $id): array
    {
        return $this->unsent->get("/templates/{$id}");
    }

    /**
     * Update a template.
     *
     * @param string $id Template ID
     * @param array $payload Update data
     * @return array [data, error]
     */
    public function update(string $id, array $payload): array
    {
        return $this->unsent->patch("/templates/{$id}", $payload);
    }

    /**
     * Delete a template.
     *
     * @param string $id Template ID
     * @return array [data, error]
     */
    public function delete(string $id): array
    {
        return $this->unsent->delete("/templates/{$id}");
    }
}
