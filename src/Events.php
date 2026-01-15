<?php

namespace Souravsspace\Unsent;

/**
 * Client for /events endpoints.
 */
class Events
{
    /**
     * @var Unsent Unsent client instance
     */
    private $unsent;

    /**
     * Initialize the Events resource client.
     *
     * @param Unsent $unsent Unsent client instance
     */
    public function __construct(Unsent $unsent)
    {
        $this->unsent = $unsent;
    }

    /**
     * List all email events.
     *
     * @param array $options Optional parameters (page, limit, status, startDate)
     * @return array [data, error]
     */
    public function list(array $options = []): array
    {
        $params = [];
        
        if (isset($options['page'])) {
            $params['page'] = (string) $options['page'];
        }
        if (isset($options['limit'])) {
            $params['limit'] = (string) $options['limit'];
        }
        if (isset($options['status'])) {
            $params['status'] = $options['status'];
        }
        if (isset($options['startDate'])) {
            $params['startDate'] = $options['startDate'];
        }
        
        $query = http_build_query($params);
        $path = '/events' . ($query ? '?' . $query : '');
        return $this->unsent->get($path);
    }
}
