<?php

namespace UnsentDev\Unsent;

/**
 * Client for /metrics endpoints.
 */
class Metrics
{
    /**
     * @var Unsent Unsent client instance
     */
    private $unsent;

    /**
     * Initialize the Metrics resource client.
     *
     * @param Unsent $unsent Unsent client instance
     */
    public function __construct(Unsent $unsent)
    {
        $this->unsent = $unsent;
    }

    /**
     * Get metrics data.
     *
     * @param array $options Optional parameters
     * @return array [data, error]
     */
    public function get(array $options = []): array
    {
        $query = http_build_query($options);
        $path = '/metrics' . ($query ? '?' . $query : '');
        return $this->unsent->get($path);
    }
}
