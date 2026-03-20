<?php

namespace UnsentDev\Unsent;

/**
 * Client for /activity endpoints.
 */
class Activity
{
    /**
     * @var Unsent Unsent client instance
     */
    private $unsent;

    /**
     * Initialize the Activity resource client.
     *
     * @param Unsent $unsent Unsent client instance
     */
    public function __construct(Unsent $unsent)
    {
        $this->unsent = $unsent;
    }

    /**
     * Get activity feed.
     *
     * @param array $options Optional parameters (page, limit)
     * @return array [data, error]
     */
    public function get(array $options = []): array
    {
        $params = [];
        
        if (isset($options['page'])) {
            $params['page'] = (string) $options['page'];
        }
        if (isset($options['limit'])) {
            $params['limit'] = (string) $options['limit'];
        }
        
        $query = http_build_query($params);
        $path = '/activity' . ($query ? '?' . $query : '');
        return $this->unsent->get($path);
    }
}
