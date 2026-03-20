<?php

namespace UnsentDev\Unsent;

/**
 * Client for /analytics endpoints.
 */
class Analytics
{
    /**
     * @var Unsent Unsent client instance
     */
    private $unsent;

    /**
     * Initialize the Analytics resource client.
     *
     * @param Unsent $unsent Unsent client instance
     */
    public function __construct(Unsent $unsent)
    {
        $this->unsent = $unsent;
    }

    /**
     * Get analytics data.
     *
     * @return array [data, error]
     */
    public function get(): array
    {
        return $this->unsent->get('/analytics');
    }

    /**
     * Get time series analytics.
     *
     * @param array $options Optional parameters (days, domain)
     * @return array [data, error]
     */
    public function getTimeSeries(array $options = []): array
    {
        $query = http_build_query($options);
        $path = '/analytics/time-series' . ($query ? '?' . $query : '');
        return $this->unsent->get($path);
    }

    /**
     * Get reputation analytics.
     *
     * @param array $options Optional parameters (domain)
     * @return array [data, error]
     */
    public function getReputation(array $options = []): array
    {
        $query = http_build_query($options);
        $path = '/analytics/reputation' . ($query ? '?' . $query : '');
        return $this->unsent->get($path);
    }
}
