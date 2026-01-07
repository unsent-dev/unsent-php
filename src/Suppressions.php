<?php

namespace Souravsspace\Unsent;

/**
 * Client for /suppressions endpoints.
 */
class Suppressions
{
    /**
     * @var Unsent Unsent client instance
     */
    private $unsent;

    /**
     * Initialize the Suppressions resource client.
     *
     * @param Unsent $unsent Unsent client instance
     */
    public function __construct(Unsent $unsent)
    {
        $this->unsent = $unsent;
    }

    /**
     * List suppressions.
     *
     * @param array $options Optional parameters (page, limit, search, reason)
     * @return array [data, error]
     */
    public function list(array $options = []): array
    {
        $query = http_build_query($options);
        $path = '/suppressions' . ($query ? '?' . $query : '');
        return $this->unsent->get($path);
    }

    /**
     * Add a suppression.
     *
     * @param array $payload Suppression data
     * @return array [data, error]
     */
    public function add(array $payload): array
    {
        return $this->unsent->post('/suppressions', $payload);
    }

    /**
     * Delete a suppression by email.
     *
     * @param string $email Email address
     * @return array [data, error]
     */
    public function delete(string $email): array
    {
        return $this->unsent->delete("/suppressions/email/{$email}");
    }
}
