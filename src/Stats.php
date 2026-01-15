<?php

namespace Souravsspace\Unsent;

/**
 * Client for /stats endpoints.
 */
class Stats
{
    /**
     * @var Unsent Unsent client instance
     */
    private $unsent;

    /**
     * Initialize the Stats resource client.
     *
     * @param Unsent $unsent Unsent client instance
     */
    public function __construct(Unsent $unsent)
    {
        $this->unsent = $unsent;
    }

    /**
     * Get statistics.
     *
     * @param array $options Optional parameters
     * @return array [data, error]
     */
    public function get(array $options = []): array
    {
        $query = http_build_query($options);
        $path = '/stats' . ($query ? '?' . $query : '');
        return $this->unsent->get($path);
    }
}
