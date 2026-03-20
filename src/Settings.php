<?php

namespace UnsentDev\Unsent;

/**
 * Client for /settings endpoints.
 */
class Settings
{
    /**
     * @var Unsent Unsent client instance
     */
    private $unsent;

    /**
     * Initialize the Settings resource client.
     *
     * @param Unsent $unsent Unsent client instance
     */
    public function __construct(Unsent $unsent)
    {
        $this->unsent = $unsent;
    }

    /**
     * Get settings.
     *
     * @return array [data, error]
     */
    public function get(): array
    {
        return $this->unsent->get('/settings');
    }
}
