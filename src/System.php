<?php

namespace UnsentDev\Unsent;

/**
 * Client for /health and /version endpoints.
 */
class System
{
    /**
     * @var Unsent Unsent client instance
     */
    private $unsent;

    /**
     * Initialize the System resource client.
     *
     * @param Unsent $unsent Unsent client instance
     */
    public function __construct(Unsent $unsent)
    {
        $this->unsent = $unsent;
    }

    /**
     * Check API health status.
     *
     * @return array [data, error]
     */
    public function health(): array
    {
        return $this->unsent->get('/health');
    }

    /**
     * Get API version information.
     *
     * @return array [data, error]
     */
    public function version(): array
    {
        return $this->unsent->get('/version');
    }
}
